<?php

/**
 * Test case for WordPoints_Installable_Extension.
 *
 * @package WordPoints\PHPUnit\Tests
 * @since 2.4.0
 */

/**
 * Tests WordPoints_Installable_Extension.
 *
 * @since 2.4.0
 *
 * @covers WordPoints_Installable_Extension
 */
class WordPoints_Installable_Extension_Test extends WordPoints_PHPUnit_TestCase {

	/**
	 * Tests getting the slug of the installable.
	 *
	 * @since 2.4.0
	 */
	public function test_get_slug() {

		$this->mock_apps();

		WordPoints_Modules::register(
			'
				Extension Name: Demo Extension
				Version:        1.0.0
				Author:         WordPoints Tester
				Author URI:     https://www.example.com/
				Extension URI:  https://www.example.com/demo/
				Description:    A demo extension.
				Text Domain:    demo
				Namespace:      Demo
		    '
			, wordpoints_extensions_dir() . '/demo/demo.php'
		);

		$installable = $this->getMockForAbstractClass(
			'WordPoints_Installable_Extension'
			, array( 'demo' )
		);

		$this->assertSame( 'demo', $installable->get_slug() );
	}

	/**
	 * Tests getting the version of the installable.
	 *
	 * @since 2.4.0
	 */
	public function test_get_version() {

		$this->mock_apps();

		WordPoints_Modules::register(
			'
				Extension Name: Demo Extension
				Version:        1.0.0
				Author:         WordPoints Tester
				Author URI:     https://www.example.com/
				Extension URI:  https://www.example.com/demo/
				Description:    A demo extension.
				Text Domain:    demo
				Namespace:      Demo
		    '
			, wordpoints_extensions_dir() . '/demo/demo.php'
		);

		$installable = $this->getMockForAbstractClass(
			'WordPoints_Installable_Extension'
			, array( 'demo' )
		);

		$this->assertSame( '1.0.0', $installable->get_version() );
	}
}

// EOF
