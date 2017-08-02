<?php

/**
 * Test case for WordPoints_Installable_Component.
 *
 * @package WordPoints\PHPUnit\Tests
 * @since 2.4.0
 */

/**
 * Tests WordPoints_Installable_Component.
 *
 * @since 2.4.0
 *
 * @covers WordPoints_Installable_Component
 */
class WordPoints_Installable_Component_Test extends WordPoints_PHPUnit_TestCase {

	/**
	 * Tests getting the version of the installable.
	 *
	 * @since 2.4.0
	 */
	public function test_get_version() {

		$installable = $this->getMockForAbstractClass( 'WordPoints_Installable_Component' );

		$this->set_protected_property( $installable, 'slug', 'points' );

		$this->assertSame( WORDPOINTS_VERSION, $installable->get_version() );
	}
}

// EOF
