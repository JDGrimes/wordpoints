<?php

/**
 * Routine class.
 *
 * @package WordPoints
 * @since   2.4.0
 */

/**
 * Bootstrap for running a routine, such as installing or uninstalling something.
 *
 * Useful when a routine needs to run in relation to several different contexts, like
 * on each site on a network and in context of the network itself. This class
 * automatically determines what needs to run based on whether multisite is enabled,
 * the routine needs to run network-wide, etc. It takes care of properly setting up
 * each context, and then cleaning up afterward.
 *
 * @since 2.4.0
 */
abstract class WordPoints_Routine implements WordPoints_RoutineI {

	/**
	 * Whether to run across the network, or just the current site.
	 *
	 * @since 2.4.0
	 *
	 * @var bool
	 */
	protected $network_wide;

	/**
	 * @since 2.4.0
	 */
	public function run() {

		wordpoints_prevent_interruptions();

		$hooks = wordpoints_hooks();
		$hooks_mode = $hooks->get_current_mode();
		$hooks->set_current_mode( 'standard' );

		if ( is_multisite() ) {

			$hooks->set_current_mode( 'network' );

			$this->run_for_network();

			$hooks->set_current_mode( 'standard' );

			if ( $this->network_wide ) {
				$this->run_for_sites();
			} else {
				$this->run_for_site();
			}

		} else {

			$this->run_for_single();
		}

		$hooks->set_current_mode( $hooks_mode );
	}

	/**
	 * Runs for each of the sites.
	 *
	 * @since 2.4.0
	 */
	protected function run_for_sites() {

		$ms_switched_state = new WordPoints_Multisite_Switched_State();
		$ms_switched_state->backup();

		foreach ( $this->get_site_ids() as $site_id ) {
			switch_to_blog( $site_id );
			$this->run_for_site();
		}

		$ms_switched_state->restore();
	}

	/**
	 * Gets the IDs of the sites to run on.
	 *
	 * @since 2.4.0
	 *
	 * @return int[] The IDs of the sites to run on.
	 */
	protected function get_site_ids() {
		return $this->get_all_site_ids();
	}

	/**
	 * Gets the IDs of all sites on the network.
	 *
	 * @since 2.4.0
	 *
	 * @return int[] The IDs of all sites on the network.
	 */
	protected function get_all_site_ids() {

		$site_ids = get_site_transient( 'wordpoints_all_site_ids' );

		if ( ! $site_ids ) {

			$site_ids = get_sites(
				array(
					'fields'     => 'ids',
					'network_id' => get_current_network_id(),
					'number'     => 0,
				)
			);

			set_site_transient( 'wordpoints_all_site_ids', $site_ids, 2 * MINUTE_IN_SECONDS );
		}

		return $site_ids;
	}

	/**
	 * Runs for the network.
	 *
	 * This is called on multisite to run only the things that are common to the
	 * whole network.
	 *
	 * @since 2.4.0
	 */
	abstract protected function run_for_network();

	/**
	 * Runs on a single site on the network.
	 *
	 * This runs on multisite for a single site on the network, which will be the
	 * current site when this method is called.
	 *
	 * @since 2.4.0
	 */
	abstract protected function run_for_site();

	/**
	 * Runs on a single site.
	 *
	 * This runs when the WordPress install is not a multisite.
	 *
	 * @since 2.4.0
	 */
	abstract protected function run_for_single();
}

// EOF
