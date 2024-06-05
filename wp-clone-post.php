<?php
/*
Plugin Name: Clone Post
Description: A plugin to duplicate posts.
Author: fondly.cz
Author URI: https://www.fondly.cz
Version: 1.0
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require_once plugin_dir_path(__FILE__) . 'classes/ClonePost.php';



function clone_post_load_textdomain() {
    load_plugin_textdomain('clone-post', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

add_action('plugins_loaded', 'clone_post_load_textdomain');


function clone_post_plugin() {
    $instance = ClonePost::getInstance();
    $instance->initialize();
}

add_action('plugins_loaded', 'clone_post_plugin');
