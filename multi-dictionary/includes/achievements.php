<?php
/**
 * Extension for ML Dictionary
 *
 * This file extends Achievements to support actions from ML Dictionary
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( has_action('dpa_ready') ): // Check to see if Achievements plugin is in use.

/**
 * Extends Achievements to support actions from ML Dictionary
 *
 */

function dpa_init_mld_extension() {
	achievements()->extensions->mld = new DPA_MLD_Extension;

	// Tell the world that the Invite Anyone extension is ready
	do_action( 'dpa_init_mld_extension' );
}
add_action( 'dpa_ready', 'dpa_init_mld_extension' );

/**
 * Extension to add ML Dictionary support to Achievements
 *
 */
class DPA_MLD_Extension extends DPA_Extension {
	/**
	 * Constructor
	 *
	 * Sets up extension properties. See class phpdoc for details.
	 *
	 */
	public function __construct() {

		$this->actions = array(
			'mld_submit_translation' => "A user submits a translation (front-end)",
			'mld_import_translation' => "A user's translation is imported (back-end)",
			/*
			'mld_approve_translation' => "A user's translation is approved",
			'mld_cast_vote' => "A user votes on a translation",
			'mld_get_vote' => "A user's translation gets a positive vote",
			'mld_moderate' => "A moderator approves or disapproves a translation",
			*/
		);

		$this->contributors = array(
			array(
				'name'         => 'Zachary Coyne',
				'gravatar_url' => false,
				'profile_url'  => false,
			)
		);

		$this->description     = __( "Award points to users for using the Multilingual Dictionary.", 'dpa' );
		$this->id              = 'multi-dictionary';
		$this->image_url       = false;
		$this->name            = __( 'Multilingual Dictionary', 'dpa' );
		$this->rss_url         = false;
		$this->small_image_url = false;
		$this->version         = 1;
		$this->wporg_url       = false;

		add_filter( 'dpa_handle_event_user_id', array( $this, 'event_user_id' ), 10, 3 );
	}

	/**
 	 * For the mld_import_translation and mld_get_vote action from Multilingual Dictionary, get the user ID from the function
 	 * arguments as the user ID we're awarding isn't the logged in user.
	 *
	 * @param int $user_id
	 * @param string $action_name
	 * @param array $action_func_args The action's arguments from func_get_args().
	 * @return int|false New user ID or false to skip any further processing
	 * @since Achievements (3.0)
	 */
	public function event_user_id( $user_id, $action_name, $action_func_args ) {
		if ( 'mld_import_translation' !== $action_name || 'mld_get_vote' !== $action_name )
			return $user_id;

		return (int) $action_func_args[0];
	}
}

endif; // End check to see if Achievements plugin is in use.