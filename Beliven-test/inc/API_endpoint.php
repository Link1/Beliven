<?php

function beliven_test_register_api_endpoint() {
    register_rest_route('beliven-test/v1', '/logs', array(
        'methods' => 'GET',
        'callback' => 'beliven_test_get_logs',
        'permission_callback' => 'beliven_test_check_permissions',
    ));
}
add_action('rest_api_init', 'beliven_test_register_api_endpoint');



function beliven_test_get_logs($data) {
    // Verifica se è specificato un filtro log_action
    $log_action_filter = isset($data['filter']) ? sanitize_text_field($data['filter']) : '';

    // Verifica se è specificato un valore per il filtro log_action
    $log_action_value = isset($data['filter_value']) ? sanitize_text_field($data['filter_value']) : '';

    // Esegui la query per ottenere i log in base al filtro log_action
    $args = array(
        'post_type' => 'log',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    );

    // Aggiungi il filtro log_action, se specificato
    if ($log_action_filter && $log_action_value) {
        $args['meta_query'] = array(
            array(
                'key' => 'log_action',
                'value' => $log_action_value,
                'compare' => '=',
            ),
        );
    }

    $logs = get_posts($args);

    // Restituisci la lista dei log
    return rest_ensure_response($logs);
}



function beliven_test_check_permissions() {
    // Debug: Aggiungi questa linea per debug
    error_log('Debug: Checking permissions for REST API.');

    // Verifica se l'utente è loggato e ha il ruolo di amministratore
    if (is_user_logged_in() && current_user_can('activate_plugins')) {
        return true;
    }

    // Se l'utente non è autorizzato, restituisci un messaggio di errore
    return new WP_Error('rest_forbidden', __('Sorry, you are not allowed to do that.'), array('status' => 401));
}

// Gestisci l'autenticazione e consenti l'accesso agli utenti autenticati
add_filter('rest_authentication_errors', function($result) {
    if (!empty($result)) {
        return $result;
    }

    // Verifica se l'utente è autenticato e ha il ruolo di amministratore
    if (!is_user_logged_in() || !current_user_can('activate_plugins')) {
        return new WP_Error('rest_forbidden', __('Sorry, you are not allowed to do that.'), array('status' => 401));
    }

    // return $result;
    return true;
});
