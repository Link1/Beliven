<?php

// Hook per l'eliminazione dei dati quando il plugin viene disinstallato

function delete_data_on_uninstall() {
    if (!current_user_can('delete_plugins')) {
        return;
    }
    error_log('Plugin uninstallation initiated.'); // Messaggio di debug

    $delete_on_uninstall = get_option('delete_on_uninstall', false);

    if ($delete_on_uninstall) {
        // Uso la funzione gia scritta in CPT_options
        delete_all_logs_ajax_callback();

        error_log('Log deletion started.'); // Messaggio di debug
        // Recupera tutti i post del tipo "log"
    /*    
        $log_posts = get_posts(array(
            'post_type' => 'log',
            'posts_per_page' => -1,
            'post_status' => 'any',
        ));

        // Elimina ogni post del tipo "log"
        foreach ($log_posts as $log_post) {
            wp_delete_post($log_post->ID, true);
        }
    */
        // Aggiungi qui ulteriori logiche per eliminare altri dati salvati dal plugin, se necessario
    }
}

register_deactivation_hook(__FILE__, 'delete_data_on_uninstall');
register_uninstall_hook(__FILE__, 'delete_data_on_uninstall');


