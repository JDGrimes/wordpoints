<?php

/**
 * A test case for the WordPoints_Dropdown_Builder class.
 *
 * @package WordPoints\Tests
 * @since 1.4.0
 */

/**
 * Test that the WordPoints_Dropdown_Builder class builds dropdowns properly.
 *
 * @since 1.4.0
 *
 * @covers WordPoints_Dropdown_Builder
 */
class WordPoints_Dropdown_Builder_Test extends WordPoints_PHPUnit_TestCase {

	//
	// Helper Methods.
	//

	/**
	 * Get the HTML generated by the dropdown builder given the passed arguments.
	 *
	 * @since 1.4.0
	 *
	 * @param array $args    The arguments for the dropdown builder.
	 * @param array $options The options to include in the dropdown.
	 *
	 * @return string The output of the generator.
	 */
	protected function get_dropdown_html( array $args = array(), array $options = null ) {

		if ( null === $options ) {
			$options = array( 1 => 'Option 1', 2 => 'Option 2' );
		}

		$dropdown = new WordPoints_Dropdown_Builder( $options, $args );

		ob_start();
		$dropdown->display();
		return ob_get_clean();
	}

	/**
	 * Get a DOMXPath object for the dropdown HTML.
	 *
	 * @since 1.7.0
	 *
	 * @param array $args The arguments for the dropdown builder.
	 * @param array $options The options to include in the dropdown.
	 *
	 * @return DOMXPath The XPath object to run queries on the dropdown HTML.
	 */
	protected function get_dropdown_xpath( array $args = array(), array $options = null ) {

		$dropdown = $this->get_dropdown_html( $args, $options );

		$document = new DOMDocument;
		$document->loadHTML( $dropdown );
		$xpath    = new DOMXPath( $document );

		return $xpath;
	}

	//
	// Tests.
	//

	/**
	 * Test that it builds a basic dropdown properly.
	 *
	 * @since 1.4.0
	 */
	public function test_basic_dropdown() {

		$xpath = $this->get_dropdown_xpath();

		$options = $xpath->query( '//option' );

		$this->assertSame( 2, $options->length );

		$option_1 = $options->item( 0 );
		$this->assertSame( 'Option 1', $option_1->textContent );
		$this->assertSame(
			'1'
			, $option_1->attributes->getNamedItem( 'value' )->nodeValue
		);

		$option_2 = $options->item( 1 );
		$this->assertSame( 'Option 2', $option_2->textContent );
		$this->assertSame(
			'2'
			, $option_2->attributes->getNamedItem( 'value' )->nodeValue
		);
	}

	/**
	 * Test the 'selected' argument.
	 *
	 * @since 1.4.0
	 */
	public function test_selected_option_selected() {

		$xpath = $this->get_dropdown_xpath( array( 'selected' => 1 ) );

		$this->assertSame(
			1
			, $xpath->query( '//option[@value = "1" and @selected]' )->length
		);
	}

	/**
	 * Test the 'name' argument is used as the select element's name.
	 *
	 * @since 1.4.0
	 */
	public function test_name() {

		$xpath = $this->get_dropdown_xpath( array( 'name' => 'test' ) );

		$this->assertSame(
			'test'
			, $xpath->query( '//select' )
				->item( 0 )
				->attributes
				->getNamedItem( 'name' )
				->nodeValue
		);
	}

	/**
	 * Test the 'id' argument is used as the select element's id.
	 *
	 * @since 1.4.0
	 */
	public function test_id() {

		$xpath = $this->get_dropdown_xpath( array( 'id' => 'test' ) );

		$this->assertSame(
			'test'
			, $xpath->query( '//select' )
				->item( 0 )
				->attributes
				->getNamedItem( 'id' )
				->nodeValue
		);
	}

	/**
	 * Test the 'class' argument is used as the select element's class.
	 *
	 * @sincne 1.4.0
	 */
	public function test_class() {

		$xpath = $this->get_dropdown_xpath( array( 'class' => 'test' ) );

		$this->assertSame(
			'test'
			, $xpath->query( '//select' )
				->item( 0 )
				->attributes
				->getNamedItem( 'class' )
				->nodeValue
		);
	}

	/**
	 * Test the 'show_option_none' argument.
	 *
	 * @since 1.4.0
	 */
	public function test_show_option_none() {

		$xpath = $this->get_dropdown_xpath( array( 'show_option_none' => 'None' ) );

		$options = $xpath->query( '//option' );

		$this->assertSame( 3, $options->length );

		$option_1 = $options->item( 0 );
		$this->assertSame( 'None', $option_1->textContent );
		$this->assertSame(
			'-1'
			, $option_1->attributes->getNamedItem( 'value' )->nodeValue
		);
	}

	/**
	 * Test the 'show_option_none' argument with the 'selected' argument.
	 *
	 * @since 1.7.0
	 */
	public function test_show_option_none_selected() {

		$xpath = $this->get_dropdown_xpath(
			array( 'show_option_none' => 'None', 'selected' => -1 )
		);

		$this->assertSame(
			1
			, $xpath->query( '//option[@value = "-1" and @selected]' )->length
		);
	}

	/**
	 * Test the 'options_key' and 'values_key' arguments with options as arrays.
	 *
	 * @since 1.4.0
	 */
	public function test_options_as_arrays() {

		$xpath = $this->get_dropdown_xpath(
			array( 'options_key' => 'option', 'values_key' => 'value' )
			, array(
				array( 'option' => 'Option 1', 'value' => '1' ),
				array( 'option' => 'Option 2', 'value' => '2' ),
			)
		);

		$options = $xpath->query( '//option' );

		$this->assertSame( 2, $options->length );

		$option_1 = $options->item( 0 );
		$this->assertSame( 'Option 1', $option_1->textContent );
		$this->assertSame(
			'1'
			, $option_1->attributes->getNamedItem( 'value' )->nodeValue
		);

		$option_2 = $options->item( 1 );
		$this->assertSame( 'Option 2', $option_2->textContent );
		$this->assertSame(
			'2'
			, $option_2->attributes->getNamedItem( 'value' )->nodeValue
		);
	}

	/**
	 * Test the 'options_key' and 'values_key' arguments with options as objects.
	 *
	 * @since 1.4.0
	 */
	public function test_options_as_objects() {

		$xpath = $this->get_dropdown_xpath(
			array( 'options_key' => 'option', 'values_key' => 'value' )
			, array(
				(object) array( 'option' => 'Option 1', 'value' => '1' ),
				(object) array( 'option' => 'Option 2', 'value' => '2' ),
			)
		);

		$options = $xpath->query( '//option' );

		$this->assertSame( 2, $options->length );

		$option_1 = $options->item( 0 );
		$this->assertSame( 'Option 1', $option_1->textContent );
		$this->assertSame(
			'1'
			, $option_1->attributes->getNamedItem( 'value' )->nodeValue
		);

		$option_2 = $options->item( 1 );
		$this->assertSame( 'Option 2', $option_2->textContent );
		$this->assertSame(
			'2'
			, $option_2->attributes->getNamedItem( 'value' )->nodeValue
		);
	}
}

// EOF
