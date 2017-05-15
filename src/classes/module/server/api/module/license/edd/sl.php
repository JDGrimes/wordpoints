<?php

/**
 * Easy Digital Downloads Software Licencing module server API module license class.
 *
 * @package WordPoints
 * @since   2.4.0
 */

/**
 * Represents a license for a module from a server that uses the EDD SL API.
 *
 * @since 2.4.0
 */
class WordPoints_Module_Server_API_Module_License_EDD_SL
	implements WordPoints_Module_Server_API_Module_License_ActivatableI,
		WordPoints_Module_Server_API_Module_License_DeactivatableI,
		WordPoints_Module_Server_API_Module_License_ExpirableI,
		WordPoints_Module_Server_API_Module_License_Renewable_URLI {

	/**
	 * The API to communicate with the server with.
	 *
	 * @since 2.4.0
	 *
	 * @var WordPoints_Module_Server_API_EDD_SL
	 */
	protected $api;

	/**
	 * The ID of the module this license is for.
	 *
	 * @since 2.4.0
	 *
	 * @var WordPoints_Module_Server_API_Module_DataI
	 */
	protected $module_data;

	/**
	 * The module license key.
	 *
	 * @since 2.4.0
	 *
	 * @var string
	 */
	protected $license_key;

	/**
	 * @since 2.4.0
	 *
	 * @param WordPoints_Module_Server_API_EDD_SL       $api         The server API.
	 * @param WordPoints_Module_Server_API_Module_DataI $module_data The module data.
	 * @param string                                    $license_key The license key.
	 */
	public function __construct(
		WordPoints_Module_Server_API_EDD_SL $api,
		WordPoints_Module_Server_API_Module_DataI $module_data,
		$license_key
	) {

		$this->api = $api;
		$this->module_data = $module_data;
		$this->license_key = $license_key;
	}

	/**
	 * @since 2.4.0
	 */
	public function is_valid() {

		$status = $this->get_info( 'license_status' );

		$valid_statuses = array(
			'expired'       => true,
			'inactive'      => true,
			'site_inactive' => true,
			'valid'         => true,
		);

		return isset( $valid_statuses[ $status ] );
	}

	/**
	 * @since 2.4.0
	 */
	public function is_activatable() {

		$status = $this->get_info( 'license_status' );

		$activatable_statuses = array(
			'site_inactive' => true,
			'inactive'      => true,
			'valid'         => true,
		);

		return isset( $activatable_statuses[ $status ] );
	}

	/**
	 * @since 2.4.0
	 */
	public function is_active() {
		return 'valid' === $this->get_info( 'license_status' );
	}

	/**
	 * @since 2.4.0
	 */
	public function activate() {

		$response = $this->request( 'activate_license' );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( ! isset( $response['license'] ) ) {
			return false;
		}

		// This 'license' key actually holds the license's status.
		$this->module_data->set( 'license_status', $response['license'] );

		if ( 'valid' !== $response['license'] ) {
			return false;
		}

		return true;
	}

	/**
	 * @since 2.4.0
	 */
	public function is_deactivatable() {

		$status = $this->get_info( 'license_status' );

		$deactivatable_statuses = array(
			'site_inactive' => true,
			'inactive'      => true,
			'valid'         => true,
		);

		return isset( $deactivatable_statuses[ $status ] );
	}

	/**
	 * @since 2.4.0
	 */
	public function deactivate() {

		$response = $this->request( 'deactivate_license' );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( ! isset( $response['license'] ) ) {
			return false;
		}

		// This 'license' key holds either 'deactivated' or 'failed'.
		// The status of the license is not returned.
		if ( 'deactivated' !== $response['license'] ) {
			$this->module_data->delete( 'license_status' );
			return ( 'inactive' === $this->get_info( 'license_status' ) );
		}

		$this->module_data->set( 'license_status', 'inactive' );

		return true;
	}

	/**
	 * @since 2.4.0
	 */
	public function expires() {
		return 'lifetime' !== $this->get_info( 'license_expiration' );
	}

	/**
	 * @since 2.4.0
	 */
	public function is_expired() {
		return 'expired' === $this->get_info( 'license_status' );
	}

	/**
	 * @since 2.4.0
	 */
	public function get_expiration_date() {

		$expiration = $this->get_info( 'license_expiration' );

		if ( 'lifetime' === $expiration ) {
			return false;
		}

		return date_create( $expiration, timezone_open( 'UTC' ) );
	}

	/**
	 * @since 2.4.0
	 */
	public function is_renewable() {
		return $this->is_valid();
	}

	/**
	 * @since 2.4.0
	 */
	public function get_renewal_url() {
		return $this->api->get_module_info( $this->module_data, 'homepage' );
	}

	/**
	 * Performs a request to the remote server.
	 *
	 * @since 2.4.0
	 *
	 * @param string $action The action being performed.
	 *
	 * @return array|WP_Error The response, or an error object.
	 */
	protected function request( $action ) {

		return $this->api->request(
			$action
			, $this->module_data->get_id()
			, $this->license_key
		);
	}

	/**
	 * Gets a piece of data for the license.
	 *
	 * Will check for a cached version from the module data object, and if not
	 * present, it will request the information from the remote server.
	 *
	 * @since 2.4.0
	 *
	 * @param string $key The data to get.
	 *
	 * @return mixed The piece of info that was requested, or null if not found.
	 */
	protected function get_info( $key ) {

		$value = $this->module_data->get( $key );

		if ( null !== $value ) {
			return $value;
		}

		$this->request_info();

		return $this->module_data->get( $key );
	}

	/**
	 * Requests license info from the remote server.
	 *
	 * @since 2.4.0
	 *
	 * @return array|WP_Error The response from the server, or an error on failure.
	 */
	protected function request_info() {

		$response = $this->request( 'check_license' );

		if ( ! is_array( $response ) ) {
			return $response;
		}

		if ( isset( $response['license'] ) ) {
			$this->module_data->set( 'license_status', $response['license'] );
		}

		if ( isset( $response['expires'] ) ) {
			$this->module_data->set( 'license_expiration', $response['expires'] );
		}

		return $response;
	}
}

// EOF
