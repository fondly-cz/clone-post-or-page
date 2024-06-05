<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class ClonePost
{

    private static $instance;

    private function __construct()
    {
        // Private constructor to enforce singleton pattern.
    }

    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function initialize()
    {
        add_action('post_row_actions', [$this, 'addCloneLink'], 10, 2);
        add_action('admin_action_clone_post', [$this, 'handleClonePost']);
    }

    public function addCloneLink($actions, $post)
    {
        if (current_user_can('edit_posts')) {
            $actions['duplicate'] = '<a href="' . wp_nonce_url('admin.php?action=clone_post&post=' . $post->ID, 'clone_post_' . $post->ID) . '" title="' . esc_attr__('Clone this post', 'clone-post') . '" rel="permalink">' . esc_html__('Clone', 'clone-post') . '</a>';
        }
        // how to echo a string which will be translated in wordpress?


        return $actions;
    }

    public function handleClonePost()
    {
        if (!isset($_GET['post']) || !isset($_GET['_wpnonce'])) {
            wp_die('No post to duplicate has been supplied!');
        }

        $post_id = absint($_GET['post']);
        $nonce = $_GET['_wpnonce'];

        if (!wp_verify_nonce($nonce, 'clone_post_' . $post_id)) {
            wp_die('Security check failed!');
        }

        $post = get_post($post_id);

        if (isset($post) && $post != null) {
            $this->clonePost($post);
        } else {
            wp_die('Post creation failed, could not find original post.');
        }
    }

    private function clonePost($post)
    {
        $current_user = wp_get_current_user();
        $new_post_author = $current_user->ID;

        $args = [
            'comment_status' => $post->comment_status,
            'ping_status' => $post->ping_status,
            'post_author' => $new_post_author,
            'post_content' => $post->post_content,
            'post_excerpt' => $post->post_excerpt,
            'post_name' => $post->post_name,
            'post_parent' => $post->post_parent,
            'post_password' => $post->post_password,
            'post_status' => 'draft',
            'post_title' => $post->post_title . ' (Copy)',
            'post_type' => $post->post_type,
            'to_ping' => $post->to_ping,
            'menu_order' => $post->menu_order
        ];

        $new_post_id = wp_insert_post($args);

        $taxonomies = get_object_taxonomies($post->post_type);
        foreach ($taxonomies as $taxonomy) {
            $post_terms = wp_get_object_terms($post->ID, $taxonomy, ['fields' => 'slugs']);
            wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
        }

        $post_meta = get_post_meta($post->ID);
        foreach ($post_meta as $meta_key => $meta_value) {
            update_post_meta($new_post_id, $meta_key, maybe_unserialize($meta_value[0]));
        }

        wp_redirect(admin_url('post.php?action=edit&post=' . $new_post_id));
        exit;
    }
}
