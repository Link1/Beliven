<?php

// Hook per le operazioni CRUD
function log_operation_hook($post_ID, $post, $update) {
    // Esci dalla funzione se il post type è 'log'
     if (get_post_type($post_ID) == 'log' || get_post_type($post_ID) == 'revision' || empty($post->ID)) {
        return;
    }

    // Ignora l'operazione se è una lettura
    if ($update === 'read') { 
        return;
    }

    // Verifica se è già stato registrato un log per questo post durante questo aggiornamento
    $existing_log = get_post(array(
        'post_type' => 'log',
        'meta_query' => array(
            array(
                'key' => 'post_id',
                'value' => $post_ID,
            ),
        ),
    ));

    // Se esiste già un log, esci senza crearne uno nuovo
    if ($existing_log) {
     //   return;
    }


    // Ottieni informazioni sull'operazione
    $log_datetime = current_time('mysql');
    $user_id = get_current_user_id();
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $log_post_type = get_post_type($post_ID);
    $log_action = $update ? 'update' : 'create'; // $update è già booleano

    // Ottieni altri dati personalizzati se necessario
    $log_metadata = ''; // Puoi aggiungere altri metadati qui

    // Crea un nuovo log senza serializzare
    $log_data = array(
        'post_id' => $post_ID,
        'log_datetime' => $log_datetime,
        'user_id' => $user_id,
        'user_ip' => $user_ip,
        'log_post_type' => $log_post_type,
        'log_action' => $log_action,
        'log_metadata' => $log_metadata,
    );

    // Prepara i dati per l'inserimento
    $log_title = 'Log for Post ' . $post_ID.' - '.$log_action;
    $log_content = wp_json_encode($log_data);

    // Evita la serializzazione duplicata
    unset($log_data);

    // Rilascia la memoria utilizzata prima della chiamata a wp_insert_post
    wp_cache_flush();
    @ini_set('memory_limit', '512M'); // Aumenta il limite di memoria temporaneamente

    sleep(2);
    // Usa la funzione wp_insert_post per creare un nuovo log
    $log_id = wp_insert_post(array(
        'post_type' => 'log',
        'post_status' => 'publish',
        'post_title' => $log_title,
        'post_content' => $log_content,
    ));

    // Ripristina il limite di memoria dopo la chiamata a wp_insert_post
    @ini_restore('memory_limit');

    if ($log_id) {
        // Operazione di creazione del log riuscita
        // Puoi fare ulteriori azioni qui se necessario
        return;
    } else {
        // Gestisci l'errore nella creazione del log
        error_log('Errore nella creazione del log per il post ' . $post_ID);
    }
}
add_action('save_post', 'log_operation_hook', 10, 3);



// WP Cron per la retention dei log
function retention_cron_hook() {
    // Ottieni il numero di giorni per la retention dalle opzioni
    $retention_days = get_option('logs_retention_days', 30);

    // Ottieni la data di scadenza
    $expiration_date = date('Y-m-d H:i:s', strtotime("-{$retention_days} days"));

    // Query per ottenere i log scaduti
    $args = array(
        'post_type' => 'log',
        'post_status' => 'publish',
        'date_query' => array(
            'before' => $expiration_date,
        ),
        'posts_per_page' => -1,
    );

    $logs_query = new WP_Query($args);

    // Elimina i log scaduti
    if ($logs_query->have_posts()) {
        while ($logs_query->have_posts()) {
            $logs_query->the_post();
            wp_delete_post(get_the_ID(), true);
        }
        wp_reset_postdata();
    }
}
add_action('my_custom_cron', 'retention_cron_hook');

// Registra l'evento cron
if (!wp_next_scheduled('my_custom_cron')) {
    wp_schedule_event(time(), 'daily', 'my_custom_cron');
}

