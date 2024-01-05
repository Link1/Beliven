<?php

// Aggiunge campi personalizzati al custom post type "Log"
function add_log_custom_fields() {
    add_meta_box(
        'log_additional_info',
        __('Additional Information'),
        'log_additional_info_callback',
        'log'
    );
}
add_action('add_meta_boxes', 'add_log_custom_fields');

// Callback per visualizzare i campi personalizzati
function log_additional_info_callback($post) {
    $log_datetime = get_post_meta($post->ID, 'log_datetime', true);
    $user_id = get_post_meta($post->ID, 'user_id', true);
    $user_ip = get_post_meta($post->ID, 'user_ip', true);
    $log_post_type = get_post_meta($post->ID, 'log_post_type', true);
    $log_action = get_post_meta($post->ID, 'log_action', true);
    $log_metadata = get_post_meta($post->ID, 'log_metadata', true);

    // Mostra i campi HTML dei campi personalizzati qui
}

// Salva i dati dei campi personalizzati
function save_log_custom_fields($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    // Verifica e assegna i valori solo se le chiavi esistono in $_POST
    $log_datetime = isset($_POST['log_datetime']) ? sanitize_text_field($_POST['log_datetime']) : '';
    $user_id = isset($_POST['user_id']) ? sanitize_text_field($_POST['user_id']) : '';
    $user_ip = isset($_POST['user_ip']) ? sanitize_text_field($_POST['user_ip']) : '';
    $log_post_type = isset($_POST['log_post_type']) ? sanitize_text_field($_POST['log_post_type']) : '';
    $log_action = isset($_POST['log_action']) ? sanitize_text_field($_POST['log_action']) : '';
    $log_metadata = isset($_POST['log_metadata']) ? sanitize_text_field($_POST['log_metadata']) : '';

    // Aggiorna i dati dei campi personalizzati
    update_post_meta($post_id, 'log_datetime', $log_datetime);
    update_post_meta($post_id, 'user_id', $user_id);
    update_post_meta($post_id, 'user_ip', $user_ip);
    update_post_meta($post_id, 'log_post_type', $log_post_type);
    update_post_meta($post_id, 'log_action', $log_action);
    update_post_meta($post_id, 'log_metadata', $log_metadata);
}
add_action('save_post_log', 'save_log_custom_fields');


