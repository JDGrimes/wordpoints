<?php

/**
 * Tests canceling deleting a points reaction.
 *
 * @package WordPoints\Codeception
 * @since 2.1.0
 */

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Delete a points reaction, but change my mind' );
$reaction = $I->hadCreatedAPointsReaction();
$I->amLoggedInAsAdminOnPage( 'wp-admin/admin.php?page=wordpoints_points_types' );
$I->see( 'Test description.', '#points-user_register .wordpoints-hook-reaction .title' );
$I->click( 'Edit', '#points-user_register .wordpoints-hook-reaction' );
$I->canSeeInFormFields(
	'#points-user_register .wordpoints-hook-reaction form'
	, array(
		'description' => 'Test description.',
		'log_text'    => 'Test log text.',
		'points'      => '10',
	)
);
$I->click( 'Delete', '#points-user_register .wordpoints-hook-reaction' );
$I->seeJQueryDialog( 'Are you sure?' );
$I->click( 'Cancel', '.wordpoints-delete-hook-reaction-dialog' );
$I->waitForJqueryAjax();
$I->canSeeElement( '#points-user_register .wordpoints-hook-reaction' );
$I->canSeePointsReactionInDB( $reaction->get_id() );

// EOF
