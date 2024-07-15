<?php
/*
Plugin Name: Clone Post or Page
Description: A plugin to duplicate posts or pages.
Version: 1.0.0
Author: fondly.cz
Author URI: https://www.fondly.cz
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.txt
Text Domain: clone-post-or-page
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require_once plugin_dir_path(__FILE__) . 'classes/Cloner.php';



function clone_post_or_page_load_textdomain() {
    load_plugin_textdomain('clone-post-or-page', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

add_action('plugins_loaded', 'clone_post_or_page_load_textdomain');


function clone_post_or_page_plugin() {
    $instance = Cloner::getInstance();
    $instance->initialize();
}

add_action('plugins_loaded', 'clone_post_or_page_plugin');
