<?php

/**
 * Administration-side functions.
 *
 * This and the included files are run on the admin side only. They create all of
 * the main administration screens, enqueue scripts and styles where needed, etc.
 *
 * Note that each component has its own administration package also.
 *
 * @package WordPoints\Administration
 * @since 1.0.0
 */

/**
 * Admin-side functions.
 *
 * @since 2.1.0
 */
require_once WORDPOINTS_DIR . '/admin/includes/functions.php';

/**
 * Admin-side script- and style-related functions.
 *
 * @since 2.5.0
 */
require_once WORDPOINTS_DIR . '/admin/includes/scripts.php';

/**
 * Admin-side extension-related functions.
 *
 * @since 2.5.0
 */
require_once WORDPOINTS_DIR . '/admin/includes/extensions.php';

/**
 * Functions relating to checking the PHP version requirements for updates.
 *
 * @since 2.5.0
 */
require_once WORDPOINTS_DIR . '/admin/includes/updates-php-version-check.php';

/**
 * Admin-side hooks.
 *
 * @since 2.1.0
 */
require_once WORDPOINTS_DIR . '/admin/includes/filters.php';

/**
 * Screen: Configuration.
 *
 * @since 1.0.0
 */
require_once WORDPOINTS_DIR . 'admin/screens/configure.php';

WordPoints_Class_Autoloader::register_dir( WORDPOINTS_DIR . 'admin/includes' );
WordPoints_Class_Autoloader::register_dir( WORDPOINTS_DIR . 'admin/classes' );

// EOF
