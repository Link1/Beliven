<?php

// Registra il custom post type "Log"

function register_log_post_type() {
    $labels = array(
        'name' => _x('Logs', 'post type general name'),
        'singular_name' => _x('Log', 'post type singular name'),
        'add_new' => _x('Add New', 'Log'),
        'add_new_item' => __('Add New Log'),
        'edit_item' => __('Edit Log'),
        'new_item' => __('New Log'),
        'view_item' => __('View Log'),
        'search_items' => __('Search Logs'),
        'not_found' => __('No logs found'),
        'not_found_in_trash' => __('No logs found in Trash'),
        'parent_item_colon' => '',
        'menu_name' => 'Logs'
    );

    $capabilities = array(
        'read' => true,
        'create_posts' => false,
        'edit_posts' => false,
        'delete_posts' => false,
        'publish_posts' => false,
    );

    $args = array(
        'labels' => $labels,
        'public' => false,
        'show_ui' => true,
 //       'capability_type' => 'log',
//        'capabilities' => $capabilities,
        'map_meta_cap' => true,
        'supports' => array('title', 'editor'),
    );

    register_post_type('log', $args);
}
add_action('init', 'register_log_post_type');


// Rimuovi "Add New" dal menu
function remove_add_new_menu() {
    global $submenu;
    unset($submenu['edit.php?post_type=log'][10]); // 10 è la posizione del link "Add New"
}
add_action('admin_menu', 'remove_add_new_menu');


// Rimuovi il pulsante "Add New" nella pagina dei log
function remove_add_new_button() {
    global $typenow;

    if ($typenow == 'log') {
        echo '<style>.page-title-action, .wrap h1 a { display: none; }</style>';
    }
}
add_action('admin_head', 'remove_add_new_button');


// Rimuovi il pulsante "Add New" nella pagina di modifica dei log
function remove_add_new_post_button($actions) {
    global $post;

    if ($post->post_type == 'log') {
        unset($actions['new']);
    }

    return $actions;
}
add_filter('post_row_actions', 'remove_add_new_post_button', 10, 2);
