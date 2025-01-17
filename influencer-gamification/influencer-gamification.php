<?php
/*
Plugin Name: Influencer Gamification
Plugin URI: http://www.ash.app.br
Description: A plugin to gamify influencer marketing.
Version: 0.4.3
Author: Lucas L. Galvão
Author URI: http://www.lg.adv.br
Text Domain: influencer-gamification
*/

if ( ! defined( 'WPINC' ) ) {
    die;
}

define( 'INFLUENCER_GAMIFICATION_VERSION', '0.4.3' );

function activate_influencer_gamification() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-influencer-gamification-activator.php';
    Influencer_Gamification_Activator::activate();
}

function deactivate_influencer_gamification() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-influencer-gamification-deactivator.php';
    Influencer_Gamification_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_influencer_gamification' );
register_deactivation_hook( __FILE__, 'deactivate_influencer_gamification' );

require plugin_dir_path( __FILE__ ) . 'includes/class-influencer-gamification.php';

function run_influencer_gamification() {
    $plugin = new Influencer_Gamification();
    $plugin->run();
}
run_influencer_gamification();