
# Plugin WordPress Beliven Test

Il plugin WordPress **Beliven Test** è una soluzione completa per il logging delle operazioni CRUD nel pannello di amministrazione di WordPress. Offre funzionalità come la creazione di un tipo di post personalizzato "Log", l'aggiunta di etichette traducibili, capacità dedicate e altro ancora. Di seguito troverai una guida dettagliata su come utilizzare e configurare il plugin.

## Funzionalità
### Tipo di Post Personalizzato "Log"
**Capacità:** Progettato solo per la lettura, assegnato alle sole ruoli "Amministratore" e "Editor".
**Campi:** Utilizza le funzioni native di WordPress per aggiungere campi come "log_datetime", "user_id", "user_ip", "log_post_type", "log_action" e "log_metadata".

### Hook di WordPress per le Operazioni CRUD
**Logging:** Sfrutta gli hook di WordPress per creare un nuovo log per ogni operazione CRUD (escludendo la lettura) eseguita nel pannello di amministrazione su tutti i tipi di post di WordPress.

### Politica di Conservazione dei Log
**Politica di Conservazione:** Implementa una politica di conservazione tramite un lavoro WP cron, eliminando i log più vecchi di un numero specificato di giorni.
**Configurazione:** Il valore dei giorni può essere configurato da una pagina delle opzioni del plugin o da una costante in wp-config.php.

### Eliminazione Dati alla Disinstallazione
**Scelta dell'Utente:** Consente agli utenti di scegliere se eliminare tutti i dati del plugin durante la disinstallazione.
**Opzioni di Configurazione:** Questa scelta può essere gestita attraverso una costante in wp-config.php o una pagina delle opzioni del plugin.

### Endpoint Autenticato della REST API
**Creazione Endpoint:** Genera un endpoint autenticato della REST API utilizzando il metodo di autenticazione desiderato.
**Filtraggio Log:** Permette agli utenti di ottenere una lista dei log attraverso filtri come "log_datetime", "user_id", "log_post_type" e "log_action".

### Installazione
Carica la cartella del plugin nella directory wp-content/plugins/ del tuo sito WordPress.
Attiva il plugin dalla pagina "Plugin" nel pannello di amministrazione di WordPress.

### Configurazione
Accedi all'opzione di configurazione del plugin per personalizzare le impostazioni.
Configura le capacità e i campi del tipo di post personalizzato "Log" secondo le tue preferenze.

**Tag: WordPress, Plugin, Logs, CRUD, REST API**