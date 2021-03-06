<?php

/**
 * Test case for the hooks functions.
 *
 * @package WordPoints\PHPUnit\Tests
 * @since 2.1.0
 */

/**
 * Tests the hooks functions.
 *
 * @since 2.1.0
 */
class WordPoints_Hooks_Functions_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * @since 2.1.0
	 */
	public function tearDown() {

		parent::tearDown();

		_unregister_post_type( 'test' );
	}

	/**
	 * Test initializing the API registers the actions.
	 *
	 * @since 2.1.0
	 *
	 * @covers ::wordpoints_init_hooks
	 */
	public function test_init() {

		$action = new WordPoints_PHPUnit_Mock_Filter();

		add_action(
			'wordpoints_init_app_registry-hooks-actions'
			, array( $action, 'action' )
		);

		$this->mock_apps();

		WordPoints_App::$main = null;

		$this->assertSame( 0, $action->call_count );

		wordpoints_init_hooks();

		$this->assertSame( 1, $action->call_count );
	}

	/**
	 * Test getting the app.
	 *
	 * @since 2.1.0
	 *
	 * @covers ::wordpoints_hooks
	 */
	public function test_get_app() {

		$this->mock_apps();

		$this->assertInstanceOf( 'WordPoints_Hooks', wordpoints_hooks() );
	}

	/**
	 * Test getting the app when the apps haven't been initialized.
	 *
	 * @since 2.1.0
	 *
	 * @covers ::wordpoints_hooks
	 */
	public function test_get_app_not_initialized() {

		$this->mock_apps();

		WordPoints_App::$main = null;

		$this->assertInstanceOf( 'WordPoints_Hooks', wordpoints_hooks() );
	}

	/**
	 * Test the extension registration function.
	 *
	 * @since 2.1.0
	 *
	 * @covers ::wordpoints_hook_extensions_init
	 */
	public function test_extensions() {

		$this->mock_apps();

		$extensions = new WordPoints_Class_Registry_Persistent();

		wordpoints_hook_extensions_init( $extensions );

		$this->assertTrue( $extensions->is_registered( 'blocker' ) );
		$this->assertTrue( $extensions->is_registered( 'repeat_blocker' ) );
		$this->assertTrue( $extensions->is_registered( 'reversals' ) );
		$this->assertTrue( $extensions->is_registered( 'conditions' ) );
		$this->assertTrue( $extensions->is_registered( 'periods' ) );
	}

	/**
	 * Test the conditions registration function.
	 *
	 * @since 2.1.0
	 *
	 * @covers ::wordpoints_hook_conditions_init
	 */
	public function test_conditions() {

		$this->mock_apps();

		$conditions = new WordPoints_Class_Registry_Children();

		wordpoints_hook_conditions_init( $conditions );

		$this->assertTrue( $conditions->is_registered( 'decimal_number', 'equals' ) );
		$this->assertTrue( $conditions->is_registered( 'decimal_number', 'greater_than' ) );
		$this->assertTrue( $conditions->is_registered( 'decimal_number', 'less_than' ) );
		$this->assertTrue( $conditions->is_registered( 'entity', 'equals' ) );
		$this->assertTrue( $conditions->is_registered( 'entity_array', 'contains' ) );
		$this->assertTrue( $conditions->is_registered( 'integer', 'equals' ) );
		$this->assertTrue( $conditions->is_registered( 'integer', 'greater_than' ) );
		$this->assertTrue( $conditions->is_registered( 'integer', 'less_than' ) );
		$this->assertTrue( $conditions->is_registered( 'text', 'contains' ) );
		$this->assertTrue( $conditions->is_registered( 'text', 'equals' ) );
	}

	/**
	 * Test the action registration function.
	 *
	 * @since 2.1.0
	 *
	 * @covers ::wordpoints_hook_actions_init
	 *
	 * @expectedDeprecated wordpoints_register_hook_actions_for_post_types
	 */
	public function test_actions() {

		$this->mock_apps();

		$actions = wordpoints_hooks()->get_sub_app( 'actions' );

		$filter = 'wordpoints_register_hook_actions_for_post_types';
		$this->listen_for_filter( $filter );

		$events_filter = 'wordpoints_register_hook_events_for_post_types';
		$this->listen_for_filter( $events_filter );

		wordpoints_hook_actions_init( $actions );

		$this->assertSame( 1, $this->filter_was_called( $filter ) );
		$this->assertSame( 1, $this->filter_was_called( $events_filter ) );

		$this->assertTrue( $actions->is_registered( 'user_register' ) );
		$this->assertTrue( $actions->is_registered( 'user_delete' ) );
		$this->assertTrue( $actions->is_registered( 'user_visit' ) );

		$this->assertTrue( $actions->is_registered( 'post_publish\post' ) );
		$this->assertTrue( $actions->is_registered( 'post_depublish\post' ) );
		$this->assertTrue( $actions->is_registered( 'post_depublish_delete\post' ) );
		$this->assertTrue( $actions->is_registered( 'post_delete\post' ) );
		$this->assertTrue( $actions->is_registered( 'comment_approve\post' ) );
		$this->assertTrue( $actions->is_registered( 'comment_new\post' ) );
		$this->assertTrue( $actions->is_registered( 'comment_deapprove\post' ) );

		$this->assertTrue( $actions->is_registered( 'post_publish\page' ) );
		$this->assertTrue( $actions->is_registered( 'post_depublish\page' ) );
		$this->assertTrue( $actions->is_registered( 'post_depublish_delete\page' ) );
		$this->assertTrue( $actions->is_registered( 'post_delete\page' ) );
		$this->assertTrue( $actions->is_registered( 'comment_approve\page' ) );
		$this->assertTrue( $actions->is_registered( 'comment_new\page' ) );
		$this->assertTrue( $actions->is_registered( 'comment_deapprove\page' ) );

		$this->assertTrue( $actions->is_registered( 'add_attachment' ) );
		$this->assertTrue( $actions->is_registered( 'post_delete\attachment' ) );
		$this->assertTrue( $actions->is_registered( 'comment_approve\attachment' ) );
		$this->assertTrue( $actions->is_registered( 'comment_new\attachment' ) );
		$this->assertTrue( $actions->is_registered( 'comment_deapprove\attachment' ) );
	}

	/**
	 * Test the events registration function.
	 *
	 * @since 2.1.0
	 *
	 * @covers ::wordpoints_hook_events_init
	 */
	public function test_events() {

		$this->mock_apps();

		$events = wordpoints_hooks()->get_sub_app( 'events' );

		$filter = 'wordpoints_register_hook_events_for_post_types';
		$this->listen_for_filter( $filter );

		wordpoints_hook_events_init( $events );

		$this->assertSame( 1, $this->filter_was_called( $filter ) );

		$this->assertEventRegistered( 'user_register', 'user' );
		$this->assertEventRegistered( 'user_visit', 'current:user' );

		$this->assertEventRegistered( 'post_publish\post', 'post\post' );
		$this->assertEventRegistered( 'post_publish\page', 'post\page' );
		$this->assertEventRegistered( 'media_upload', 'post\attachment' );

		$this->assertEventRegistered( 'comment_leave\post', 'comment\post' );
		$this->assertEventRegistered( 'comment_leave\page', 'comment\page' );
		$this->assertEventRegistered( 'comment_leave\attachment', 'comment\attachment' );
	}

	/**
	 * Test the get post types for hook events function.
	 *
	 * @since 2.2.0
	 *
	 * @covers ::wordpoints_get_post_types_for_hook_events
	 */
	public function test_get_post_types_for_entities() {

		$filter = 'wordpoints_register_hook_events_for_post_types';
		$this->listen_for_filter( $filter );

		$this->assertSame(
			get_post_types( array( 'public' => true ) )
			, wordpoints_get_post_types_for_hook_events()
		);

		$this->assertSame( 1, $this->filter_was_called( $filter ) );
	}

	/**
	 * Test that it registers the expected events.
	 *
	 * @since 2.1.0
	 *
	 * @covers ::wordpoints_register_post_type_hook_events
	 */
	public function test_register_post_type_hook_events() {

		$this->mock_apps();

		$this->factory->wordpoints->post_type->create(
			array( 'name' => 'test', 'supports' => array( 'testing' ) )
		);

		$mock = $this->listen_for_filter(
			'wordpoints_register_post_type_hook_events'
		);

		wordpoints_register_post_type_hook_events( 'test' );

		$this->assertSame( 1, $mock->call_count );
		$this->assertSame( array( 'test' ), $mock->calls[0] );

		$this->assertEventRegistered( 'post_publish\test', 'post\test' );
	}

	/**
	 * Test that it registers the expected events for an attachment.
	 *
	 * @since 2.1.0
	 *
	 * @covers ::wordpoints_register_post_type_hook_events
	 */
	public function test_register_post_type_hook_events_attachment() {

		$this->mock_apps();

		wordpoints_register_post_type_hook_events( 'attachment' );

		$this->assertEventNotRegistered( 'post_publish\attachment', 'post\attachment' );
		$this->assertEventRegistered( 'media_upload', 'post\attachment' );
	}

	/**
	 * Test that it registers the comment entities only when comments are supported.
	 *
	 * @since 2.1.0
	 *
	 * @covers ::wordpoints_register_post_type_hook_events
	 */
	public function test_supports_comments() {

		$this->mock_apps();

		$this->factory->wordpoints->post_type->create(
			array( 'name' => 'test', 'supports' => array() )
		);

		wordpoints_register_post_type_hook_events( 'test' );

		$this->assertEventNotRegistered( 'comment_leave\test', 'comment\test' );

		add_post_type_support( 'test', 'comments' );

		wordpoints_register_post_type_hook_events( 'test' );

		$this->assertEventRegistered( 'comment_leave\test', 'comment\test' );
	}
}

// EOF
