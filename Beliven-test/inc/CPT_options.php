<?php
// Aggiungi voce di sottomenu "Options" sotto "Logs"
function add_options_submenu() {
    add_submenu_page(
        'edit.php?post_type=log', // Slug della pagina padre ("Logs")
        'Options',                 // Titolo della pagina
        'Options',                 // Testo nel menu
        'manage_options',          // Capability richiesta per accedere
        'log_options_page',        // Slug della pagina
        'log_options_page_callback' // Funzione di callback per la pagina
    );
}
add_action('admin_menu', 'add_options_submenu');

// Funzione di callback per la pagina delle opzioni
function log_options_page_callback() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <form method="post" action="options.php">
            <?php
            // Mostra gli errori/successi delle opzioni
            settings_errors();

            // Carica le opzioni
            $logs_retention_days = get_option('logs_retention_days', 30);
            $delete_on_uninstall = get_option('delete_on_uninstall', false);
            ?>

            <label for="logs_retention_days">Enter retention days (1-999): </label>
            <input type="number" id="logs_retention_days" name="logs_retention_days" min="1" max="999" value="<?php echo esc_attr($logs_retention_days); ?>">

            <br>

            <label for="delete_on_uninstall">
                <input type="checkbox" id="delete_on_uninstall" name="delete_on_uninstall" <?php checked(isset($delete_on_uninstall) && $delete_on_uninstall, true); ?>>
                Delete all data on uninstall
            </label>

            <?php
            // Aggiungi i campi nonce per la sicurezza
            settings_fields('logs_retention_options_group');

            // Aggiungi il pulsante di submit
            submit_button('Save');

            // Aggiungi il pulsante per cancellare i log
            ?>
            <br>
            <button type="button" class="button" id="delete_logs_button">Delete All Logs</button>
            <div id="confirm_delete_logs" style="display:none;">
                <p id="delete_logs_message">Are you sure you want to delete all logs? This action cannot be undone.</p>
                <button type="button" class="button" id="confirm_delete_logs_button">Yes, Delete All Logs</button>
                <button type="button" class="button" id="cancel_delete_logs_button">Cancel</button>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var deleteButton = document.getElementById('delete_logs_button');
                    var confirmBox = document.getElementById('confirm_delete_logs');
                    var confirmButton = document.getElementById('confirm_delete_logs_button');
                    var cancelButton = document.getElementById('cancel_delete_logs_button');
                    var deleteLogsMessage = document.getElementById('delete_logs_message');

                    deleteButton.addEventListener('click', function () {
                        confirmBox.style.display = 'block';
                    });

                    cancelButton.addEventListener('click', function () {
                        confirmBox.style.display = 'none';
                    });

                    confirmButton.addEventListener('click', function () {
                        // Add logic to delete all logs
                        var data = {
                            action: 'delete_all_logs',
                            security: '<?php echo wp_create_nonce("delete_all_logs_nonce"); ?>'
                        };

                        // Perform AJAX request to handle log deletion
                        deleteLogs(data, 1);
                    });

                    function deleteLogs(data, paged) {
                        data.paged = paged;
                        jQuery.post(ajaxurl, data, function (response) {
                            console.log(response.message); // Log the response message

                            if (response.success && response.next_page && response.next_page <= response.total_pages) {
                                // There are more logs to delete, update the message and initiate the next deletion
                                deleteLogsMessage.innerText = 'Deleting logs... ' + response.total_pages * response.logs_per_page + ' logs remaining.';
                                deleteLogs(data, response.next_page);
                            } else {
                                // All logs deleted successfully, update the message
                                deleteLogsMessage.innerText = response.message;
                                confirmBox.style.display = 'none';
                            }
                        });
                    }
                });
            </script>
        </form>
    </div>
    <?php
}

// Aggiungi azione AJAX per gestire la cancellazione dei log
add_action('wp_ajax_delete_all_logs', 'delete_all_logs_ajax_callback');

// Funzione per gestire la cancellazione dei log tramite AJAX
function delete_all_logs_ajax_callback() {
    check_ajax_referer('delete_all_logs_nonce', 'security');

    $logs_per_page = 1000;
    $paged = isset($_POST['paged']) ? absint($_POST['paged']) : 1;

    // Recupera tutti i post del tipo "log" per la pagina corrente
    $log_posts = get_posts(array(
        'post_type' => 'log',
        'posts_per_page' => $logs_per_page,
        'paged' => $paged,
        'post_status' => 'any',
    ));

    // Elimina ogni post del tipo "log"
    foreach ($log_posts as $log_post) {
        wp_delete_post($log_post->ID, true);
    }

    $total_logs = wp_count_posts('log')->publish;

    // Calcola il numero totale di pagine
    $total_pages = ceil($total_logs / $logs_per_page);

    // Incrementa la pagina per la prossima chiamata
    $next_page = $paged + 1;

    if ($next_page <= $total_pages) {
        $response = array(
            'success' => true,
            'message' => 'Logs deleted successfully!',
            'next_page' => $next_page,
            'total_pages' => $total_pages,
            'logs_per_page' => $logs_per_page,
        );
    } else {
        $response = array(
            'success' => true,
            'message' => 'All logs deleted successfully!',
        );
    }

    wp_send_json($response);
    wp_die();
}
?>
