<?php
/**
 * Plugin Name: AI Spam Shield Plugin
 * Description: AI Spam Shield Plugin.
 * Version: 1.0.0
 * Author: Rajan Vijayan
 * License: GPL-2.0-or-later
 */

defined( 'ABSPATH' ) || exit;

// Autoload dependencies using Composer
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require __DIR__ . '/vendor/autoload.php';
}

use AISpamShield\Admin\SettingsPage;
use AISpamShield\Moderation\CommentModerator;
use AISpamShield\Spam\EmailValidator;

// Initialize the plugin
function ai_comment_moderator_init() {
    // Load admin settings and logs page
    if ( is_admin() ) {
        new SettingsPage();
    }

    // Initialize comment moderation module
    new CommentModerator();
    new EmailValidator();
}
add_action( 'plugins_loaded', 'ai_comment_moderator_init' );