<?php

declare(strict_types=1);

return [
    /*
      |----------------------------------------------------------------------------------------
      | Authentication Pages [English(en)]
      |----------------------------------------------------------------------------------------
      |
      | The following language lines are used in all authentication related issues to translate
      | some words in view to English. You are free to change them to anything you want to
      | customize your views to better match your application.
      |
     */
    'navigation' => [
        'name' => 'Ticket',
        'plural' => 'Ticket',
        'group' => [
            'name' => 'Management',
        ],
    ],
    'fields' => [
        'name' => 'Nome Area',
        'parent' => 'Settore di appartenenza',
        'parent.name' => 'Settore di appartenenza',
        'parent_name' => 'Settore di appartenenza',
        'assets' => 'Quantità di asset',
    ],
    'actions' => [
        'import' => [
            'name' => 'Importa da file',
            'fields' => [
                'import_file' => 'Seleziona un file XLS o CSV da caricare',
            ],
        ],
        'export' => [
            'name' => 'Esporta dati',
            'filename_prefix' => 'Aree al',
            'columns' => [
                'name' => 'Nome area',
                'parent_name' => 'Nome area livello superiore',
            ],
        ],
    ],

    /*
      |--------------------------------------
      |   Error
      |--------------------------------------
     */
    'success' => [
        'label' => 'Successo',
        'placeholder' => 'Successo',
    ],
    'fails' => [
        'label' => 'Fallito',
        'placeholder' => 'Fallito',
    ],
    'alert' => [
        'label' => 'Avviso',
        'placeholder' => 'Avviso',
    ],
    'warning' => [
        'label' => 'Attenzione',
        'placeholder' => 'Attenzione',
    ],
    'required-error' => [
        'label' => 'Per favore compila tutti i campi richiesti',
        'placeholder' => 'Per favore compila tutti i campi richiesti',
    ],
    'invalid' => [
        'label' => 'ID utente o password sbagliate',
        'placeholder' => 'ID utente o password sbagliate',
    ],
    'sorry_something_went_wrong' => [
        'label' => 'Ci dispiace, qualcosa è andato storto',
        'placeholder' => 'Ci dispiace, qualcosa è andato storto',
    ],
    'were_working_on_it_and_well_get_it_fixed_as_soon_as_we_can' => [
        'label' => 'Ci stiamo lavorando e risolveremo il problema quanto prima.',
        'placeholder' => 'Ci stiamo lavorando e risolveremo il problema quanto prima.',
    ],
    'we_are_sorry_but_the_page_you_are_looking_for_can_not_be_found' => [
        'label' => 'Ci dispiace, la pagina che stai cercando non esiste.',
        'placeholder' => 'Ci dispiace, la pagina che stai cercando non esiste.',
    ],
    'go_back' => [
        'label' => 'Torna indietro',
        'placeholder' => 'Torna indietro',
    ],
    'the_board_is_offline' => [
        'label' => 'Il sistema è offline',
        'placeholder' => 'Il sistema è offline',
    ],
    'error_establishing_connection_to_database' => [
        'label' => 'Impossibile stabilire la connessione al database',
        'placeholder' => 'Impossibile stabilire la connessione al database',
    ],
    'unauthorized_access' => [
        'label' => 'Accesso non autorizzato',
        'placeholder' => 'Accesso non autorizzato',
    ],
    'not-autherised' => [
        'label' => 'Non sei autorizzato',
        'placeholder' => 'Non sei autorizzato',
    ],
    'otp-not-matched' => [
        'label' => 'Oops! Il codice OTP che hai inserito non corrisponde a quello che ti abbiamo spedito.',
        'placeholder' => 'Oops! Il codice OTP che hai inserito non corrisponde a quello che ti abbiamo spedito.',
    ],
    'otp-invalid' => [
        'label' => 'Il codice OTP deve essere un numero a 6 cifre.',
        'placeholder' => 'Il codice OTP deve essere un numero a 6 cifre.',
    ],
    'otp-input-title' => [
        'label' => 'Inserisci il codice OTP di 6 cifre.',
        'placeholder' => 'Inserisci il codice OTP di 6 cifre.',
    ],
    'otp-expired' => [
        'label' => 'Il tuo codice OTP è scaduto.<br/> Clicca su "Ricevi OTP" per ricevere un nuovo codice OTP sul tuo cellulare.',
        'placeholder' => 'Il tuo codice OTP è scaduto.<br/> Clicca su "Ricevi OTP" per ricevere un nuovo codice OTP sul tuo cellulare.',
    ],
    'resend-otp-title' => [
        'label' => 'Clicca qui per reinviare il codice OTP',
        'placeholder' => 'Clicca qui per reinviare il codice OTP',
    ],
    /*
      |--------------------------------------
      |   Login Page
      |--------------------------------------
     */
    'login_to_start_your_session' => [
        'label' => 'Collegati per cominciare la tua sessione',
        'placeholder' => 'Collegati per cominciare la tua sessione',
    ],
    'login' => [
        'label' => 'Accedi',
        'placeholder' => 'Accedi',
    ],
    'remember' => [
        'label' => 'Ricordami',
        'placeholder' => 'Ricordami',
    ],
    'signmein' => [
        'label' => 'Iscrivimi',
        'placeholder' => 'Iscrivimi',
    ],
    'iforgot' => [
        'label' => 'Ho dimenticato la password',
        'placeholder' => 'Ho dimenticato la password',
    ],
    'email_address' => [
        'label' => 'Indirizzo email',
        'placeholder' => 'Indirizzo email',
    ],
    'password' => [
        'label' => 'Password',
        'placeholder' => 'Password',
    ],
    'password_confirmation' => [
        'label' => 'Conferma password',
        'placeholder' => 'Conferma password',
    ],
    'woops' => [
        'label' => 'Whoops!',
        'placeholder' => 'Whoops!',
    ],
    'theirisproblem' => [
        'label' => 'Ci sono problemi con ciò che hai inserito.',
        'placeholder' => 'Ci sono problemi con ciò che hai inserito.',
    ],
    'e-mail' => [
        'label' => 'E-mail',
        'placeholder' => 'E-mail',
    ],
    'reg_new_member' => [
        'label' => 'Registra un nuovo membro',
        'placeholder' => 'Registra un nuovo membro',
    ],
    'this_account_is_currently_inactive' => [
        'label' => 'Questo account non è attualmente attivo!',
        'placeholder' => 'Questo account non è attualmente attivo!',
    ],
    'not-registered' => [
        'label' => 'Email/utente non registrati',
        'placeholder' => 'Email/utente non registrati',
    ],
    'verify' => [
        'label' => 'Verifica',
        'placeholder' => 'Verifica',
    ],
    'enter-otp' => [
        'label' => 'Inserisci OTP',
        'placeholder' => 'Inserisci OTP',
    ],
    'did-not-recive-code' => [
        'label' => 'Non hai ricevuto il codice?',
        'placeholder' => 'Non hai ricevuto il codice?',
    ],
    'resend_otp' => [
        'label' => 'Ricevi OTP',
        'placeholder' => 'Ricevi OTP',
    ],
    'verify-screen-msg' => [
        'label' => 'Il tuo account non è attualmente attivo.<br/>Per attivarlo, per favore inserisci il codice OTP che abbiamo inviato',
        'placeholder' => 'Il tuo account non è attualmente attivo.<br/>Per attivarlo, per favore inserisci il codice OTP che abbiamo inviato',
    ],
    'otp-sent' => [
        'label' => 'Abbiamo un inviato un codice OTP al tuo numero.',
        'placeholder' => 'Abbiamo un inviato un codice OTP al tuo numero.',
    ],
    'verify-number' => [
        'label' => 'Verifica numero',
        'placeholder' => 'Verifica numero',
    ],
    'get-verify-message' => [
        'label' => 'Inserisci il codice OTP che abbiamo inviato al tuo numero.',
        'placeholder' => 'Inserisci il codice OTP che abbiamo inviato al tuo numero.',
    ],
    'number-verification-sussessfull' => [
        'label' => 'Il tuo numero è stato verificato correttamente, per favore attendi. Stiamo aggiornando il tuo profilo.',
        'placeholder' => 'Il tuo numero è stato verificato correttamente, per favore attendi. Stiamo aggiornando il tuo profilo.',
    ],
    'enter_your_email_here' => [
        'label' => 'Inserisci la tua mail',
        'placeholder' => 'Inserisci la tua mail',
    ],
    /*
      |--------------------------------------
      |   Register Page
      |--------------------------------------
     */
    'registration' => [
        'label' => 'Registrazione',
        'placeholder' => 'Registrazione',
    ],
    'full_name' => [
        'label' => 'Nome completo',
        'placeholder' => 'Nome completo',
    ],
    'first_name' => [
        'label' => 'Nome',
        'placeholder' => 'Nome',
    ],
    'lastname' => [
        'label' => 'Cognome',
        'placeholder' => 'Cognome',
    ],
    'profilepicture' => [
        'label' => 'Foto del profilo',
        'placeholder' => 'Foto del profilo',
    ],
    'oldpassword' => [
        'label' => 'Vecchia password',
        'placeholder' => 'Vecchia password',
    ],
    'newpassword' => [
        'label' => 'Nuova password',
        'placeholder' => 'Nuova password',
    ],
    'retype_password' => [
        'label' => 'Reinserisci password',
        'placeholder' => 'Reinserisci password',
    ],
    'i_agree_to_the' => [
        'label' => 'Concordo con',
        'placeholder' => 'Concordo con',
    ],
    'terms' => [
        'label' => 'termini',
        'placeholder' => 'termini',
    ],
    'register' => [
        'label' => 'Registrati',
        'placeholder' => 'Registrati',
    ],
    'i_already_have_a_membership' => [
        'label' => 'Sono già registrato',
        'placeholder' => 'Sono già registrato',
    ],
    'see-profile1' => [
        'label' => 'Clicca qui per vedere il profilo di ',
        'placeholder' => 'Clicca qui per vedere il profilo di ',
    ],
    'see-profile2' => [
        'label' => '',
        'placeholder' => '',
    ],
    'activate_your_account_click_on_Link_that_send_to_your_mail' => [
        'label' => 'Attiva il tuo account! Clicca sul link che abbiamo spedito alla tua email',
        'placeholder' => 'Attiva il tuo account! Clicca sul link che abbiamo spedito alla tua email',
    ],
    'activate_your_account_click_on_Link_that_send_to_your_mail_and_moble' => [
        'label' => 'Attiva il tuo account! Clicca sul link che abbiamo spedito alla tua email o collegati con il tuo account ed inserisci il codice OTP che abbiamo inviato sul tuo cellulare',
        'placeholder' => 'Attiva il tuo account! Clicca sul link che abbiamo spedito alla tua email o collegati con il tuo account ed inserisci il codice OTP che abbiamo inviato sul tuo cellulare',
    ],
    'activate_your_account_click_on_Link_that_send_to_your_mail_sms_plugin_inactive_or_not_setup' => [
        'label' => 'Account creato, per favore contatta l\'amministratore poichè non siamo riusciti a spedire il codice OTP sul tuo cellulare e la mail al tuo indirizzo.',
        'placeholder' => 'Account creato, per favore contatta l\'amministratore poichè non siamo riusciti a spedire il codice OTP sul tuo cellulare e la mail al tuo indirizzo.',
    ],
    'this_field_do_not_match_our_records' => [
        'label' => 'Questo campo non corrisponde ai nostri archivi.',
        'placeholder' => 'Questo campo non corrisponde ai nostri archivi.',
    ],
    'we_have_e-mailed_your_password_reset_link' => [
        'label' => 'Abbiamo spedito per email il link di reset password!',
        'placeholder' => 'Abbiamo spedito per email il link di reset password!',
    ],
    "we_can't_find_a_user_with_that_e-mail_address" => "Non riusciamo a trovare un utente con quell\'indirizzo email.",
    /*
      |--------------------------------------
      |   Reset Password Page
      |--------------------------------------
     */
    'reset_password' => [
        'label' => 'Resetta la password',
        'placeholder' => 'Resetta la password',
    ],
    'password-reset-successfully' => [
        'label' => 'La tua password è stata resettata. Collegati con la nuova password',
        'placeholder' => 'La tua password è stata resettata. Collegati con la nuova password',
    ],
    'password-can-not-reset' => [
        'label' => 'Non è stato possibile resettare la password, per favore riprova più tardi.',
        'placeholder' => 'Non è stato possibile resettare la password, per favore riprova più tardi.',
    ],
    /*
      |--------------------------------------
      |   Forgot Password Page
      |--------------------------------------
     */
    'i_know_my_password' => [
        'label' => 'Conosco la mia password',
        'placeholder' => 'Conosco la mia password',
    ],
    'recover_passord' => [
        'label' => 'Recupera password',
        'placeholder' => 'Recupera password',
    ],
    'send_password_reset_link' => [
        'label' => 'Invia link per il reset della password',
        'placeholder' => 'Invia link per il reset della password',
    ],
    'enter_email_to_reset_password' => [
        'label' => 'Inserisci email per il reset della password',
        'placeholder' => 'Inserisci email per il reset della password',
    ],
    'link' => [
        'label' => 'Link',
        'placeholder' => 'Link',
    ],
    'email_or_mobile' => [
        'label' => 'Email o cellulare',
        'placeholder' => 'Email o cellulare',
    ],
    /*
      |----------------------------------------------------------------------------------------
      | Emails Pages [English(en)]
      |----------------------------------------------------------------------------------------
      |
      | The following language lines are used in all Emails related issues to translate
      | some words in view to English. You are free to change them to anything you want to
      | customize your views to better match your application.
      |
     */
    'admin_panel' => [
        'label' => 'Panello amministrativo',
        'placeholder' => 'Panello amministrativo',
    ],
    /*
      |--------------------------------------
      |  Emails Create Page
      |--------------------------------------
     */
    'emails' => [
        'label' => 'Lista email',
        'placeholder' => 'Lista email',
    ],
    'incoming_emails' => [
        'label' => 'Email in arrivo',
        'placeholder' => 'Email in arrivo',
    ],
    'reuired_authentication' => [
        'label' => 'Autenticazione richiesta',
        'placeholder' => 'Autenticazione richiesta',
    ],
    'fetching_email_via_imap' => [
        'label' => 'Caricamento email via IMAP',
        'placeholder' => 'Caricamento email via IMAP',
    ],
    'create_email' => [
        'label' => 'Crea email',
        'placeholder' => 'Crea email',
    ],
    'email_name' => [
        'label' => 'Nome email',
        'placeholder' => 'Nome email',
    ],
    'help_topic' => [
        'label' => 'Area di supporto',
        'placeholder' => 'Area di supporto',
    ],
    'auto_response' => [
        'label' => 'Auto risponditore',
        'placeholder' => 'Auto risponditore',
    ],
    'host_name' => [
        'label' => 'Host',
        'placeholder' => 'Host',
    ],
    'port_number' => [
        'label' => 'Porta',
        'placeholder' => 'Porta',
    ],
    'mail_box_protocol' => [
        'label' => 'Protocollo casella Mail',
        'placeholder' => 'Protocollo casella Mail',
    ],
    'authentication_required' => [
        'label' => 'Autenticazione richiesta',
        'placeholder' => 'Autenticazione richiesta',
    ],
    'yes' => [
        'label' => 'Si',
        'placeholder' => 'Si',
    ],
    'no' => [
        'label' => 'No',
        'placeholder' => 'No',
    ],
    'header_spoofing' => [
        'label' => 'Header Spoofing',
        'placeholder' => 'Header Spoofing',
    ],
    'allow_for_this_email' => [
        'label' => 'Permetti per questa email',
        'placeholder' => 'Permetti per questa email',
    ],
    'imap_config' => [
        'label' => 'Configurazione IMAP',
        'placeholder' => 'Configurazione IMAP',
    ],
    'email_information_and_settings' => [
        'label' => 'Informazioni e parametri email',
        'placeholder' => 'Informazioni e parametri email',
    ],
    'incoming_email_information' => [
        'label' => 'Parametri posta in entrata',
        'placeholder' => 'Parametri posta in entrata',
    ],
    'outgoing_email_information' => [
        'label' => 'Parametri posta in uscita',
        'placeholder' => 'Parametri posta in uscita',
    ],
    'new_ticket_settings' => [
        'label' => 'Settaggi Nuovo Ticket',
        'placeholder' => 'Settaggi Nuovo Ticket',
    ],
    'protocol' => [
        'label' => 'Protocollo',
        'placeholder' => 'Protocollo',
    ],
    'fetching_protocol' => [
        'label' => 'Protocollo',
        'placeholder' => 'Protocollo',
    ],
    'transfer_protocol' => [
        'label' => 'Protocollo',
        'placeholder' => 'Protocollo',
    ],
    'from_name' => [
        'label' => 'Nome mittente',
        'placeholder' => 'Nome mittente',
    ],
    'add_an_email' => [
        'label' => 'Aggiungi email',
        'placeholder' => 'Aggiungi email',
    ],
    'edit_an_email' => [
        'label' => 'Modifica email',
        'placeholder' => 'Modifica email',
    ],
    'disable_for_this_email_address' => [
        'label' => 'Disabilita per questo indirizzo email ',
        'placeholder' => 'Disabilita per questo indirizzo email ',
    ],
    'validate_certificates_from_tls_or_ssl_server' => [
        'label' => 'Valida certificato TLS/SSL',
        'placeholder' => 'Valida certificato TLS/SSL',
    ],
    'authentication' => [
        'label' => 'Autenticazione',
        'placeholder' => 'Autenticazione',
    ],
    'incoming_email_connection_failed_please_check_email_credentials_or_imap_settings' => [
        'label' => 'Connessione posta in entrata fallita! Per favore controlla credenziali e parametri',
        'placeholder' => 'Connessione posta in entrata fallita! Per favore controlla credenziali e parametri',
    ],
    'outgoing_email_connection_failed' => [
        'label' => 'Connessione posta in uscita fallita! Per favore controlla credenziali e parametri',
        'placeholder' => 'Connessione posta in uscita fallita! Per favore controlla credenziali e parametri',
    ],
    'you_cannot_delete_system_default_email' => [
        'label' => "Non puoi cancellare l'email di default di sistema",
        'placeholder' => "Non puoi cancellare l'email di default di sistema",
    ],
    'email_deleted_sucessfully' => [
        'label' => 'Email cancellata',
        'placeholder' => 'Email cancellata',
    ],
    'email_can_not_delete' => [
        'label' => 'Impossibile cancellare email',
        'placeholder' => 'Impossibile cancellare email',
    ],
    'outgoing_email_failed' => [
        'label' => 'Invio email fallito',
        'placeholder' => 'Invio email fallito',
    ],
    'system-email-not-configured' => [
        'label' => 'Non è possibile processare la richiesta mail in quanto il sistema non ha email configurate per l\'invio. Per favore contatta l\'amministratore di sistema.',
        'placeholder' => 'Non è possibile processare la richiesta mail in quanto il sistema non ha email configurate per l\'invio. Per favore contatta l\'amministratore di sistema.',
    ],
    /*
      |--------------------------------------
      |  Ban Emails Create Page
      |--------------------------------------
     */
    'ban_lists' => [
        'label' => 'Lista ban',
        'placeholder' => 'Lista ban',
    ],
    'ban_email' => [
        'label' => 'Banna email',
        'placeholder' => 'Banna email',
    ],
    'banlists' => [
        'label' => 'Liste ban',
        'placeholder' => 'Liste ban',
    ],
    'ban_status' => [
        'label' => 'Stato ban',
        'placeholder' => 'Stato ban',
    ],
    'version' => [
        'label' => 'Versione',
        'placeholder' => 'Versione',
    ],
    'list_of_banned_emails' => [
        'label' => 'Lista email bannate',
        'placeholder' => 'Lista email bannate',
    ],
    'edit_banned_email' => [
        'label' => 'Modifica email bannata',
        'placeholder' => 'Modifica email bannata',
    ],
    'create_a_banned_email' => [
        'label' => 'Crea ban email',
        'placeholder' => 'Crea ban email',
    ],
    'email_banned_sucessfully' => [
        'label' => 'Email bannata con successo',
        'placeholder' => 'Email bannata con successo',
    ],
    'email_can_not_ban' => [
        'label' => 'Non è stato possibile bannare l\'email',
        'placeholder' => 'Non è stato possibile bannare l\'email',
    ],
    'banned_email_updated_sucessfully' => [
        'label' => 'Ban email aggiornato',
        'placeholder' => 'Ban email aggiornato',
    ],
    'banned_email_not_updated' => [
        'label' => 'Ban email non aggiornato',
        'placeholder' => 'Ban email non aggiornato',
    ],
    'banned_removed_sucessfully' => [
        'label' => 'Ban rimosso con successo',
        'placeholder' => 'Ban rimosso con successo',
    ],
    /*
      |--------------------------------------
      |  Templates Index Page
      |--------------------------------------
     */
    'templates' => [
        'label' => 'Modelli',
        'placeholder' => 'Modelli',
    ],
    'template_set' => [
        'label' => 'Set modelli',
        'placeholder' => 'Set modelli',
    ],
    'create_template' => [
        'label' => 'Crea modello',
        'placeholder' => 'Crea modello',
    ],
    'edit_template' => [
        'label' => 'Modifica modello',
        'placeholder' => 'Modifica modello',
    ],
    'list_of_templates_sets' => [
        'label' => 'Lista modelli',
        'placeholder' => 'Lista modelli',
    ],
    'create_set' => [
        'label' => 'Crea set',
        'placeholder' => 'Crea set',
    ],
    'template_name' => [
        'label' => 'Nome modello',
        'placeholder' => 'Nome modello',
    ],
    'template_saved_successfully' => [
        'label' => 'Modello salvato con successo',
        'placeholder' => 'Modello salvato con successo',
    ],
    'template_updated_successfully' => [
        'label' => 'Modello aggiornato con successo',
        'placeholder' => 'Modello aggiornato con successo',
    ],
    'in_use' => [
        'label' => 'In uso',
        'placeholder' => 'In uso',
    ],
    'you_have_created_a_new_template_set' => [
        'label' => 'Hai creato un nuovo set di modelli',
        'placeholder' => 'Hai creato un nuovo set di modelli',
    ],
    'you_have_successfully_activated_this_set' => [
        'label' => 'Hai attivato con successo questo set di modelli',
        'placeholder' => 'Hai attivato con successo questo set di modelli',
    ],
    'template_set_deleted_successfully' => [
        'label' => 'Set di modelli cancellato con successo',
        'placeholder' => 'Set di modelli cancellato con successo',
    ],
    // Template Description
    'Create ticket agent' => [
        'label' => 'Email di notifica che viene inviata ad agente ed amministratore quando il ticket viene creato',
        'placeholder' => 'Email di notifica che viene inviata ad agente ed amministratore quando il ticket viene creato',
    ],
    'Assign ticket' => [
        'label' => 'Ticket assegnato ad un agente',
        'placeholder' => 'Ticket assegnato ad un agente',
    ],
    'Create ticket' => [
        'label' => 'Mail inviata al cliente per conferma creazione ticket',
        'placeholder' => 'Mail inviata al cliente per conferma creazione ticket',
    ],
    'Check ticket' => [
        'label' => 'Se un cliente vuole controllare attraverso il portale clienti un link verrà inviato al cliente. Questo link è per il cliente per vedere i dettagli del ticket co il suo numero senza loggarsi nel sistema',
        'placeholder' => 'Se un cliente vuole controllare attraverso il portale clienti un link verrà inviato al cliente. Questo link è per il cliente per vedere i dettagli del ticket co il suo numero senza loggarsi nel sistema',
    ],
    'Ticket reply agent' => [
        'label' => 'Una notifica è inviata ad un agente una volta che il cliente risponde al ticket',
        'placeholder' => 'Una notifica è inviata ad un agente una volta che il cliente risponde al ticket',
    ],
    'Registration notification' => [
        'label' => 'Password e nome utente sono inviati per email alla prima registrazione',
        'placeholder' => 'Password e nome utente sono inviati per email alla prima registrazione',
    ],
    'Reset password' => [
        'label' => 'Email con il link per il reset della password',
        'placeholder' => 'Email con il link per il reset della password',
    ],
    'Error report' => [
        'label' => 'Report errori',
        'placeholder' => 'Report errori',
    ],
    'Ticket creation' => [
        'label' => 'Prima notifica inviata dal sistema sulla creazione del ticket al cliente',
        'placeholder' => 'Prima notifica inviata dal sistema sulla creazione del ticket al cliente',
    ],
    'Ticket reply' => [
        'label' => 'Una risposta fatta da un agente sul ticket,una notifica è inviata al cliente e ai collaboratori',
        'placeholder' => 'Una risposta fatta da un agente sul ticket,una notifica è inviata al cliente e ai collaboratori',
    ],
    'Close ticket' => [
        'label' => 'Mail inviata al cliente per la chiusura di un ticket',
        'placeholder' => 'Mail inviata al cliente per la chiusura di un ticket',
    ],
    'Create ticket by agent' => [
        'label' => 'Un agente crea un ticket per il cliente a nome del cliente',
        'placeholder' => 'Un agente crea un ticket per il cliente a nome del cliente',
    ],
    /*
      |--------------------------------------
      |  Templates Create Page
      |--------------------------------------
     */
    'template_set_to_clone' => [
        'label' => 'Set modelli da clonare',
        'placeholder' => 'Set modelli da clonare',
    ],
    'language' => [
        'label' => 'Lingua',
        'placeholder' => 'Lingua',
    ],
    /*
      |--------------------------------------
      |  Diagnostics Page
      |--------------------------------------
     */
    'diagnostics' => [
        'label' => 'Diagnostica',
        'placeholder' => 'Diagnostica',
    ],
    'from' => [
        'label' => 'Da',
        'placeholder' => 'Da',
    ],
    'to' => [
        'label' => 'A',
        'placeholder' => 'A',
    ],
    'subject' => [
        'label' => 'Oggetto',
        'placeholder' => 'Oggetto',
    ],
    'message' => [
        'label' => 'Messaggio',
        'placeholder' => 'Messaggio',
    ],
    'send' => [
        'label' => 'Invia',
        'placeholder' => 'Invia',
    ],
    'choose_an_email' => [
        'label' => 'Scegli email',
        'placeholder' => 'Scegli email',
    ],
    'email_diagnostic' => [
        'label' => 'Diagnostica email',
        'placeholder' => 'Diagnostica email',
    ],
    'send-mail-to-diagnos' => [
        'label' => 'Spedisci una mail per controllare i parametri in uscita',
        'placeholder' => 'Spedisci una mail per controllare i parametri in uscita',
    ],
    'message_has_been_sent' => [
        'label' => 'Messaggio spedito',
        'placeholder' => 'Messaggio spedito',
    ],
    'message_sent_from_php_mail' => [
        'label' => 'Messaggio spedito da PHP-Mail',
        'placeholder' => 'Messaggio spedito da PHP-Mail',
    ],
    'mailer_error' => [
        'label' => 'Errore Mailer',
        'placeholder' => 'Errore Mailer',
    ],
    /*
      |----------------------------------------------------------------------------------------
      | Settings Pages [English(en)]
      |----------------------------------------------------------------------------------------
      |
      | The following language lines are used in all Setting related issues to translate
      | some words in view to English. You are free to change them to anything you want to
      | customize your views to better match your application.
      |
     */

    /*
      |--------------------------------------
      |   Company Settings Page
      |--------------------------------------
     */
    'country-code' => [
        'label' => 'INT',
        'placeholder' => 'INT',
    ],
    'company' => [
        'label' => 'Azienda',
        'placeholder' => 'Azienda',
    ],
    'company_settings' => [
        'label' => 'Impostazioni azienda',
        'placeholder' => 'Impostazioni azienda',
    ],
    'website' => [
        'label' => 'Sito Web',
        'placeholder' => 'Sito Web',
    ],
    'phone' => [
        'label' => 'Telefono',
        'placeholder' => 'Telefono',
    ],
    'address' => [
        'label' => 'Indirizzo',
        'placeholder' => 'Indirizzo',
    ],
    'landing' => [
        'label' => 'Pagina di destinazione',
        'placeholder' => 'Pagina di destinazione',
    ],
    'offline' => [
        'label' => 'Pagina offline',
        'placeholder' => 'Pagina offline',
    ],
    'thank' => [
        'label' => 'Pagina di ringraziamento',
        'placeholder' => 'Pagina di ringraziamento',
    ],
    'logo' => [
        'label' => 'Logo',
        'placeholder' => 'Logo',
    ],
    'save' => [
        'label' => 'Salva',
        'placeholder' => 'Salva',
    ],
    'delete-logo' => [
        'label' => 'Cancella logo',
        'placeholder' => 'Cancella logo',
    ],
    'click-delete' => [
        'label' => 'Clicca per cancellare',
        'placeholder' => 'Clicca per cancellare',
    ],
    'use_logo' => [
        'label' => 'Usa logo',
        'placeholder' => 'Usa logo',
    ],
    'company_updated_successfully' => [
        'label' => 'Azienda aggiornata correttamente',
        'placeholder' => 'Azienda aggiornata correttamente',
    ],
    'company_can_not_updated' => [
        'label' => 'Impossibile aggiornare dati azienda',
        'placeholder' => 'Impossibile aggiornare dati azienda',
    ],
    'enter-country-phone-code' => [
        'label' => 'Inserisci il codice telefonico del paese',
        'placeholder' => 'Inserisci il codice telefonico del paese',
    ],
    'country-code-required-error' => [
        'label' => 'Il codice paese è obbligatorio con i numeri telefonici.',
        'placeholder' => 'Il codice paese è obbligatorio con i numeri telefonici.',
    ],
    'incorrect-country-code-error' => [
        'label' => 'Codice paese errato.',
        'placeholder' => 'Codice paese errato.',
    ],
    /*
      |--------------------------------------
      |   System Settings Page
      |--------------------------------------
     */
    'api' => [
        'label' => 'Api',
        'placeholder' => 'Api',
    ],
    'api_key' => [
        'label' => 'Chiave Api',
        'placeholder' => 'Chiave Api',
    ],
    'api_key_mandatory' => [
        'label' => 'Chiave Api obbligatoria',
        'placeholder' => 'Chiave Api obbligatoria',
    ],
    'api_configurations' => [
        'label' => 'Configurazioni Api',
        'placeholder' => 'Configurazioni Api',
    ],
    'generate_key' => [
        'label' => 'Genera chiave',
        'placeholder' => 'Genera chiave',
    ],
    'system' => [
        'label' => 'Sistema',
        'placeholder' => 'Sistema',
    ],
    'online' => [
        'label' => 'Online',
        'placeholder' => 'Online',
    ],
    'name/title' => [
        'label' => 'Nome/Titolo',
        'placeholder' => 'Nome/Titolo',
    ],
    'pagesize' => [
        'label' => 'Grandezza pagina',
        'placeholder' => 'Grandezza pagina',
    ],
    'url' => [
        'label' => 'URL',
        'placeholder' => 'URL',
    ],
    'default_department' => [
        'label' => 'Dipartimento di default',
        'placeholder' => 'Dipartimento di default',
    ],
    'loglevel' => [
        'label' => 'Livello log',
        'placeholder' => 'Livello log',
    ],
    'purglog' => [
        'label' => 'Ripulisci logs',
        'placeholder' => 'Ripulisci logs',
    ],
    'nameformat' => [
        'label' => 'Formattazione nome',
        'placeholder' => 'Formattazione nome',
    ],
    'timeformat' => [
        'label' => 'Formato ora',
        'placeholder' => 'Formato ora',
    ],
    'date' => [
        'label' => 'Data',
        'placeholder' => 'Data',
    ],
    'dateformat' => [
        'label' => 'Formato data',
        'placeholder' => 'Formato data',
    ],
    'date_time' => [
        'label' => 'Formato data ed ora',
        'placeholder' => 'Formato data ed ora',
    ],
    'day_date_time' => [
        'label' => 'Formato giorno, data ed ora',
        'placeholder' => 'Formato giorno, data ed ora',
    ],
    'timezone' => [
        'label' => 'Fuso orario di default',
        'placeholder' => 'Fuso orario di default',
    ],
    'Ticket-created-successfully' => [
        'label' => 'Ticket creato con successo!',
        'placeholder' => 'Ticket creato con successo!',
    ],
    'Ticket-created-successfully2' => [
        'label' => 'Il ticket è stato creato ma non verificato. Verrà mostrato non appena l\'utente verificherà il proprio account.',
        'placeholder' => 'Il ticket è stato creato ma non verificato. Verrà mostrato non appena l\'utente verificherà il proprio account.',
    ],
    'system_updated_successfully' => [
        'label' => 'Sistema aggiornato correttamente',
        'placeholder' => 'Sistema aggiornato correttamente',
    ],
    'system_can_not_updated' => [
        'label' => 'Il sistema non può essere aggiornato',
        'placeholder' => 'Il sistema non può essere aggiornato',
    ],
    'ticket_updated_successfully' => [
        'label' => 'Ticket aggiornato con successo',
        'placeholder' => 'Ticket aggiornato con successo',
    ],
    'ticket_can_not_updated' => [
        'label' => 'Impossibile aggiornare il ticket',
        'placeholder' => 'Impossibile aggiornare il ticket',
    ],
    'email_updated_successfully' => [
        'label' => 'Email aggiornata con successo',
        'placeholder' => 'Email aggiornata con successo',
    ],
    'email_can_not_updated' => [
        'label' => 'Impossibile aggiornare email',
        'placeholder' => 'Impossibile aggiornare email',
    ],
    'select_a_time_zone' => [
        'label' => 'Seleziona un fuso orario',
        'placeholder' => 'Seleziona un fuso orario',
    ],
    'select_a_date_time_format' => [
        'label' => 'Seleziona un formato data e ora',
        'placeholder' => 'Seleziona un formato data e ora',
    ],
    'Ticket-has-been-created-successfully-your-ticket-number-is' => [
        'label' => 'Ticket creato con successo, il numero ticket è',
        'placeholder' => 'Ticket creato con successo, il numero ticket è',
    ],
    'Please-save-this-for-future-reference' => [
        'label' => 'Per favore conserva questo numero per riferimenti futuri',
        'placeholder' => 'Per favore conserva questo numero per riferimenti futuri',
    ],
    'email-moble-already-taken' => [
        'label' => 'Email o cellulare già presente nel sistema',
        'placeholder' => 'Email o cellulare già presente nel sistema',
    ],
    'mobile-has-been-taken' => [
        'label' => 'Cellulare già presente nel sistema',
        'placeholder' => 'Cellulare già presente nel sistema',
    ],
    'failed-to-create-user-tcket-as-mobile-has-been-taken' => [
        'label' => 'Impossibile creare un nuovo ticket poichè il cellulare inserito è associato con un utente già esistente. Verifica i dettagli inseriti o crea un nuovo utente',
        'placeholder' => 'Impossibile creare un nuovo ticket poichè il cellulare inserito è associato con un utente già esistente. Verifica i dettagli inseriti o crea un nuovo utente',
    ],
    'rtl' => [
        'label' => 'RTL (Da destra a sinistra)',
        'placeholder' => 'RTL (Da destra a sinistra)',
    ],
    'the_rtl_support_is_only_applicable_to_the_outgoing_mails' => [
        'label' => 'Il supporto RTL è applicabile solamente alle mail in uscita',
        'placeholder' => 'Il supporto RTL è applicabile solamente alle mail in uscita',
    ],
    'user_set_ticket_status' => [
        'label' => 'Consenti agli utenti di impostare lo stato del ticket',
        'placeholder' => 'Consenti agli utenti di impostare lo stato del ticket',
    ],
    'send_otp_for_account_verfication' => [
        'label' => 'Invia codice OTP per la verifica degli utenti',
        'placeholder' => 'Invia codice OTP per la verifica degli utenti',
    ],
    'otp_usage_info' => [
        'label' => 'Se disabilitato invieremo una mail di verifica ed un codice OTP agli utenti. Se la mail non è obbligatoria gli utenti riceveranno le credenziali di accesso sul cellulare. [NOTA: gli SMS verranno spediti usando il plugin Faveo SMS].',
        'placeholder' => 'Se disabilitato invieremo una mail di verifica ed un codice OTP agli utenti. Se la mail non è obbligatoria gli utenti riceveranno le credenziali di accesso sul cellulare. [NOTA: gli SMS verranno spediti usando il plugin Faveo SMS].',
    ],
    'send_otp_title_message' => [
        'label' => 'Send OTP for user account verification, reset password and mobile number verification',
        'placeholder' => 'Send OTP for user account verification, reset password and mobile number verification',
    ],
    'allow_unverified_users_to_create_ticket' => [
        'label' => 'Consenti agli utenti non verificati di creare ticket',
        'placeholder' => 'Consenti agli utenti non verificati di creare ticket',
    ],
    'make-email-mandatroy' => [
        'label' => "Rendi l'email obbligatoria per la creazione utente/ticket",
        'placeholder' => "Rendi l'email obbligatoria per la creazione utente/ticket",
    ],
    'email_man_info' => [
        'label' => 'Se disabilitato gli utenti saranno in grado di registrarsi senza indirizzo email. Si raccomanda di disabilitare la possibilità di creare ticket da parte di utenti non verificati. Così facendo gli utenti potranno ricevere notifiche e collegarsi col proprio cellulare con nome utente e password ricevuti.',
        'placeholder' => 'Se disabilitato gli utenti saranno in grado di registrarsi senza indirizzo email. Si raccomanda di disabilitare la possibilità di creare ticket da parte di utenti non verificati. Così facendo gli utenti potranno ricevere notifiche e collegarsi col proprio cellulare con nome utente e password ricevuti.',
    ],
    /*
      |--------------------------------------
      |   Email Settings Page
      |--------------------------------------
     */
    'email' => [
        'label' => 'Email',
        'placeholder' => 'Email',
    ],
    'email-settings' => [
        'label' => 'Impostazioni email',
        'placeholder' => 'Impostazioni email',
    ],
    'default_template' => [
        'label' => 'Modello di default:',
        'placeholder' => 'Modello di default:',
    ],
    'default_system_email' => [
        'label' => 'Sistema email di default:',
        'placeholder' => 'Sistema email di default:',
    ],
    'default_alert_email' => [
        'label' => 'Allerta email di default:',
        'placeholder' => 'Allerta email di default:',
    ],
    'admin_email' => [
        'label' => 'Indirizzo email amministratori:',
        'placeholder' => 'Indirizzo email amministratori:',
    ],
    'email_fetch' => [
        'label' => 'Caricamento email:',
        'placeholder' => 'Caricamento email:',
    ],
    'enable' => [
        'label' => 'Attivo',
        'placeholder' => 'Attivo',
    ],
    'default_MTA' => [
        'label' => 'MTA di default',
        'placeholder' => 'MTA di default',
    ],
    'fetch_auto-corn' => [
        'label' => 'Carica con auto-cron',
        'placeholder' => 'Carica con auto-cron',
    ],
    'strip_quoted_reply' => [
        'label' => 'Rimuovi citazioni nella risposta',
        'placeholder' => 'Rimuovi citazioni nella risposta',
    ],
    'reply_separator' => [
        'label' => 'Tag separatore nella risposta',
        'placeholder' => 'Tag separatore nella risposta',
    ],
    'accept_all_email' => [
        'label' => 'Accetta tutte le emails',
        'placeholder' => 'Accetta tutte le emails',
    ],
    'accept_email_unknown' => [
        'label' => 'Accetta email da utenti sconosciuti',
        'placeholder' => 'Accetta email da utenti sconosciuti',
    ],
    'accept_email_collab' => [
        'label' => 'Accetta email da collaboratori',
        'placeholder' => 'Accetta email da collaboratori',
    ],
    'automatically_and_collab_from_email' => [
        'label' => 'Aggiungi automaticamente collaboratori dai campi email',
        'placeholder' => 'Aggiungi automaticamente collaboratori dai campi email',
    ],
    'attachments' => [
        'label' => 'Allegati',
        'placeholder' => 'Allegati',
    ],
    'email_attahment_user' => [
        'label' => "Allegati email per l'utente",
        'placeholder' => "Allegati email per l'utente",
    ],
    'cron_notification' => [
        'label' => 'Attiva notifica cron',
        'placeholder' => 'Attiva notifica cron',
    ],
    'cron' => [
        'label' => 'Schedulatore cron',
        'placeholder' => 'Schedulatore cron',
    ],
    'cron-jobs' => [
        'label' => 'Cron jobs',
        'placeholder' => 'Cron jobs',
    ],
    'crone-url-message' => [
        'label' => 'Questi sono i cron job di Faveo per il tuo sistema.',
        'placeholder' => 'Questi sono i cron job di Faveo per il tuo sistema.',
    ],
    'clipboard-copy-message' => [
        'label' => 'Copiato negli appunti.',
        'placeholder' => 'Copiato negli appunti.',
    ],
    'click' => [
        'label' => 'Clicca qui',
        'placeholder' => 'Clicca qui',
    ],
    'check-cron-set' => [
        'label' => 'per vedere come impostare cron jobs sul tuo server.',
        'placeholder' => 'per vedere come impostare cron jobs sul tuo server.',
    ],
    'notification-email' => [
        'label' => 'Rapporto giornaliero',
        'placeholder' => 'Rapporto giornaliero',
    ],
    'click-url-copy' => [
        'label' => 'Clicca qui per copiare URL',
        'placeholder' => 'Clicca qui per copiare URL',
    ],
    'job-scheduler-error' => [
        'label' => 'Impossibile aggiornare schedulatore cron job.',
        'placeholder' => 'Impossibile aggiornare schedulatore cron job.',
    ],
    'job-scheduler-success' => [
        'label' => 'Schedulatore cron aggiornato con successo.',
        'placeholder' => 'Schedulatore cron aggiornato con successo.',
    ],
    /*
      |--------------------------------------
      |   Ticket Settings Page
      |--------------------------------------
     */
    'ticket' => [
        'label' => 'Ticket',
        'placeholder' => 'Ticket',
    ],
    'ticket-setting' => [
        'label' => 'Impostazioni ticket',
        'placeholder' => 'Impostazioni ticket',
    ],
    'default_ticket_number_format' => [
        'label' => 'Formato numero ticket di default',
        'placeholder' => 'Formato numero ticket di default',
    ],
    'default_ticket_number_sequence' => [
        'label' => 'Formato sequenza numero ticket di default',
        'placeholder' => 'Formato sequenza numero ticket di default',
    ],
    'default_status' => [
        'label' => 'Stato di default',
        'placeholder' => 'Stato di default',
    ],
    'default_priority' => [
        'label' => 'Priorità di default',
        'placeholder' => 'Priorità di default',
    ],
    'default_sla' => [
        'label' => 'SLA di default',
        'placeholder' => 'SLA di default',
    ],
    'default_help_topic' => [
        'label' => 'Area di supporto di default',
        'placeholder' => 'Area di supporto di default',
    ],
    'maximum_open_tickets' => [
        'label' => 'Massimo numero ticket aperti',
        'placeholder' => 'Massimo numero ticket aperti',
    ],
    'agent_collision_avoidance_duration' => [
        'label' => 'Periodo prevenzione collisione agenti',
        'placeholder' => 'Periodo prevenzione collisione agenti',
    ],
    'human_verification' => [
        'label' => 'Verifica umano',
        'placeholder' => 'Verifica umano',
    ],
    'claim_on_response' => [
        'label' => 'Richiesta di risposta',
        'placeholder' => 'Richiesta di risposta',
    ],
    'assigned_tickets' => [
        'label' => 'Ticket assegnati',
        'placeholder' => 'Ticket assegnati',
    ],
    'answered_tickets' => [
        'label' => 'Ticket risposti',
        'placeholder' => 'Ticket risposti',
    ],
    'agent_identity_masking' => [
        'label' => 'Maschera identità degli agenti',
        'placeholder' => 'Maschera identità degli agenti',
    ],
    'enable_HTML_ticket_thread' => [
        'label' => 'Abilita HTML su discussione ticket',
        'placeholder' => 'Abilita HTML su discussione ticket',
    ],
    'allow_client_updates' => [
        'label' => 'Consenti aggiornamenti cliente',
        'placeholder' => 'Consenti aggiornamenti cliente',
    ],
    'lock_ticket_frequency' => [
        'label' => 'Blocco ticket',
        'placeholder' => 'Blocco ticket',
    ],
    'only-once' => [
        'label' => 'Solo una volta',
        'placeholder' => 'Solo una volta',
    ],
    'frequently' => [
        'label' => 'Frequentemente',
        'placeholder' => 'Frequentemente',
    ],
    'reload-now' => [
        'label' => 'Ricarica adesso',
        'placeholder' => 'Ricarica adesso',
    ],
    'ticket-lock-inactive' => [
        'label' => "Sei stato inattivo per un po', per favore ricarica la pagina.",
        'placeholder' => "Sei stato inattivo per un po', per favore ricarica la pagina.",
    ],
    'make-system-default-mail' => [
        'label' => 'Rendi questa mail predefinita per il sistema',
        'placeholder' => 'Rendi questa mail predefinita per il sistema',
    ],
    'thread' => [
        'label' => 'Thread',
        'placeholder' => 'Thread',
    ],
    'labels' => [
        'label' => 'Etichette',
        'placeholder' => 'Etichette',
    ],
    /*
      |--------------------------------------
      |   Access Settings Page
      |--------------------------------------
     */
    'access' => [
        'label' => 'Accesso',
        'placeholder' => 'Accesso',
    ],
    'expiration_policy' => [
        'label' => 'Politica scadenza password',
        'placeholder' => 'Politica scadenza password',
    ],
    'allow_password_resets' => [
        'label' => 'Permetti reset della password',
        'placeholder' => 'Permetti reset della password',
    ],
    'reset_token_expiration' => [
        'label' => 'Resetta scadenza token',
        'placeholder' => 'Resetta scadenza token',
    ],
    'agent_session_timeout' => [
        'label' => 'Timeout sessione agente',
        'placeholder' => 'Timeout sessione agente',
    ],
    'bind_agent_session_IP' => [
        'label' => 'Lega sessione agente ad IP',
        'placeholder' => 'Lega sessione agente ad IP',
    ],
    'registration_required' => [
        'label' => 'Registrazione richiesta',
        'placeholder' => 'Registrazione richiesta',
    ],
    'require_registration_and_login_to_create_tickets' => [
        'label' => 'Richiede registrazione e login per creare ticket',
        'placeholder' => 'Richiede registrazione e login per creare ticket',
    ],
    'registration_method' => [
        'label' => 'Metodo di registrazione',
        'placeholder' => 'Metodo di registrazione',
    ],
    'user_session_timeout' => [
        'label' => 'Timeout sessione ttente',
        'placeholder' => 'Timeout sessione ttente',
    ],
    'client_quick_access' => [
        'label' => 'Accesso rapido cliente',
        'placeholder' => 'Accesso rapido cliente',
    ],
    'cron_settings' => [
        'label' => 'Impostazioni Cron',
        'placeholder' => 'Impostazioni Cron',
    ],
    'system-settings' => [
        'label' => 'Impostazioni di sistema',
        'placeholder' => 'Impostazioni di sistema',
    ],
    'settings-2' => [
        'label' => 'Impostazioni',
        'placeholder' => 'Impostazioni',
    ],
    /*
      |--------------------------------------
      |   Auto-Response Settings Page
      |--------------------------------------
     */
    'auto_responce' => [
        'label' => 'Auto risponditore',
        'placeholder' => 'Auto risponditore',
    ],
    'auto_responce-settings' => [
        'label' => 'Impostazioni auto risponditore',
        'placeholder' => 'Impostazioni auto risponditore',
    ],
    'new_ticket' => [
        'label' => 'Nuovo ticket',
        'placeholder' => 'Nuovo ticket',
    ],
    'new_ticket_by_agent' => [
        'label' => 'Nuovo ticket per agente',
        'placeholder' => 'Nuovo ticket per agente',
    ],
    'new_message' => [
        'label' => 'Nuovo messaggio',
        'placeholder' => 'Nuovo messaggio',
    ],
    'submitter' => [
        'label' => 'Inviato da: ',
        'placeholder' => 'Inviato da: ',
    ],
    'send_receipt_confirmation' => [
        'label' => 'Invia conferma di ricevuta',
        'placeholder' => 'Invia conferma di ricevuta',
    ],
    'participants' => [
        'label' => 'Partecipanti: ',
        'placeholder' => 'Partecipanti: ',
    ],
    'send_new_activity_notice' => [
        'label' => 'Invia nuova notifica attività',
        'placeholder' => 'Invia nuova notifica attività',
    ],
    'overlimit_notice' => [
        'label' => 'Notifica di sorpasso limite',
        'placeholder' => 'Notifica di sorpasso limite',
    ],
    'email_attachments_to_the_user' => [
        'label' => 'Invia allegati email ad utente',
        'placeholder' => 'Invia allegati email ad utente',
    ],
    'auto_response_updated_successfully' => [
        'label' => 'Auto risponditore aggiornato con successo',
        'placeholder' => 'Auto risponditore aggiornato con successo',
    ],
    'auto_response_can_not_updated' => [
        'label' => 'Impossibile aggiornare auto risponditore',
        'placeholder' => 'Impossibile aggiornare auto risponditore',
    ],

    /*
      |--------------------------------------
      |   Alert & Notice Settings Page
      |--------------------------------------
     */
    'disable' => [
        'label' => 'Disattivo',
        'placeholder' => 'Disattivo',
    ],
    'admin_email_2' => [
        'label' => 'Amministratore',
        'placeholder' => 'Amministratore',
    ],
    'alert_notices' => [
        'label' => 'Avvisi e notifiche',
        'placeholder' => 'Avvisi e notifiche',
    ],
    'alert_notices_setitngs' => [
        'label' => 'Impostazioni avvisi e notifiche',
        'placeholder' => 'Impostazioni avvisi e notifiche',
    ],
    'new_ticket_alert' => [
        'label' => 'Avviso nuovo ticket',
        'placeholder' => 'Avviso nuovo ticket',
    ],
    'department_manager' => [
        'label' => 'Manager di dipartimento',
        'placeholder' => 'Manager di dipartimento',
    ],
    'department_members' => [
        'label' => 'Componenti del dipartimento',
        'placeholder' => 'Componenti del dipartimento',
    ],
    'organization_account_manager' => [
        'label' => "Account Manager dell'organizzazione",
        'placeholder' => "Account Manager dell'organizzazione",
    ],
    'new_message_alert' => [
        'label' => 'Avviso nuovo messaggio',
        'placeholder' => 'Avviso nuovo messaggio',
    ],
    'last_respondent' => [
        'label' => 'Ultimo a rispondere',
        'placeholder' => 'Ultimo a rispondere',
    ],
    'assigned_agent_team' => [
        'label' => 'Agente / Team assegnato',
        'placeholder' => 'Agente / Team assegnato',
    ],
    'new_internal_note_alert' => [
        'label' => 'Avviso nuova nota interna',
        'placeholder' => 'Avviso nuova nota interna',
    ],
    'ticket_assignment_alert' => [
        'label' => 'Avviso assegnamento ticket',
        'placeholder' => 'Avviso assegnamento ticket',
    ],
    'team_lead' => [
        'label' => 'Leader del team',
        'placeholder' => 'Leader del team',
    ],
    'team_members' => [
        'label' => 'Componenti del team',
        'placeholder' => 'Componenti del team',
    ],
    'ticket_transfer_alert' => [
        'label' => 'Avviso trasferimento Ticket',
        'placeholder' => 'Avviso trasferimento Ticket',
    ],
    'overdue_ticket_alert' => [
        'label' => 'Avviso ritardo Ticket ',
        'placeholder' => 'Avviso ritardo Ticket ',
    ],
    'system_alerts' => [
        'label' => 'Avviso di sistema',
        'placeholder' => 'Avviso di sistema',
    ],
    'system_errors' => [
        'label' => 'Errori di sistema',
        'placeholder' => 'Errori di sistema',
    ],
    'SQL_errors' => [
        'label' => 'Errori SQL',
        'placeholder' => 'Errori SQL',
    ],
    'excessive_failed_login_attempts' => [
        'label' => 'Eccessivi tentativi di login falliti',
        'placeholder' => 'Eccessivi tentativi di login falliti',
    ],
    'system_error_reports' => [
        'label' => 'Rapporto errori di sistema',
        'placeholder' => 'Rapporto errori di sistema',
    ],
    'Send_app_crash_reports_to_help_Ladybird_improve_Faveo' => [
        'label' => 'Invia rapporti di crash per aiutare Ladybird a migliorare Faveo',
        'placeholder' => 'Invia rapporti di crash per aiutare Ladybird a migliorare Faveo',
    ],
    'alert_&_notices_updated_successfully' => [
        'label' => 'Alert and notices updated successfully',
        'placeholder' => 'Alert and notices updated successfully',
    ],
    'alert_&_notices_can_not_updated' => [
        'label' => 'Alert and notices can not be updated',
        'placeholder' => 'Alert and notices can not be updated',
    ],

    /*
      |-----------------------------------------------
      | Ratings Settings
      |-----------------------------------------------
     */
    'current_ratings' => [
        'label' => 'Valutazioni correnti',
        'placeholder' => 'Valutazioni correnti',
    ],
    'edit_ratings' => [
        'label' => 'Modifica Valutazioni',
        'placeholder' => 'Modifica Valutazioni',
    ],

    /*
      |-------------------------------------------------
      |Social login
      |--------------------------------------------------
    */
    'social-login' => [
        'label' => 'Accesso con i Social',
        'placeholder' => 'Accesso con i Social',
    ],

    /*
      |------------------------------------------------
      | Language page
      |------------------------------------------------
     */
    'default' => [
        'label' => 'Default',
        'placeholder' => 'Default',
    ],
    'language-settings' => [
        'label' => 'Impostazioni lingua',
        'placeholder' => 'Impostazioni lingua',
    ],
    'iso-code' => [
        'label' => 'Codice ISO',
        'placeholder' => 'Codice ISO',
    ],
    'download' => [
        'label' => 'Scarica',
        'placeholder' => 'Scarica',
    ],
    'upload_file' => [
        'label' => 'Carica un file',
        'placeholder' => 'Carica un file',
    ],
    'enter_iso-code' => [
        'label' => 'Inserisci codice ISO',
        'placeholder' => 'Inserisci codice ISO',
    ],
    'eg.' => [
        'label' => 'Esempio',
        'placeholder' => 'Esempio',
    ],
    'for' => [
        'label' => 'Per',
        'placeholder' => 'Per',
    ],
    'english' => [
        'label' => 'Inglese',
        'placeholder' => 'Inglese',
    ],
    'language-name' => [
        'label' => 'Nome lingua',
        'placeholder' => 'Nome lingua',
    ],
    'file' => [
        'label' => 'File',
        'placeholder' => 'File',
    ],
    'read-more' => [
        'label' => 'Continua.',
        'placeholder' => 'Continua.',
    ],
    'enable_lang' => [
        'label' => 'Attiva.',
        'placeholder' => 'Attiva.',
    ],
    'add-lang-package' => [
        'label' => 'Aggiungi nuovo pacchetto lingua',
        'placeholder' => 'Aggiungi nuovo pacchetto lingua',
    ],
    'package_exist' => [
        'label' => 'Pacchetto già esistente.',
        'placeholder' => 'Pacchetto già esistente.',
    ],
    'iso-code-error' => [
        'label' => 'Errore nel codice iso, inserire codice corretto.',
        'placeholder' => 'Errore nel codice iso, inserire codice corretto.',
    ],
    'zipp-error' => [
        'label' => "Errore nel file zip, l'archivio deve contenere solo files php.",
        'placeholder' => "Errore nel file zip, l'archivio deve contenere solo files php.",
    ],
    'upload-success' => [
        'label' => 'Caricato con successo.',
        'placeholder' => 'Caricato con successo.',
    ],
    'file-error' => [
        'label' => 'Errore nel file, file non valido.',
        'placeholder' => 'Errore nel file, file non valido.',
    ],
    'delete-success' => [
        'label' => 'Pacchetto lingua cancellato con successo.',
        'placeholder' => 'Pacchetto lingua cancellato con successo.',
    ],
    'lang-doesnot-exist' => [
        'label' => 'Pacchetto lingua non esistente.',
        'placeholder' => 'Pacchetto lingua non esistente.',
    ],
    'active-lang-error' => [
        'label' => 'Pacchetto lingua non cancellabile se attivo.',
        'placeholder' => 'Pacchetto lingua non cancellabile se attivo.',
    ],
    'language-error' => [
        'label' => 'Pacchetto lingua mancante.',
        'placeholder' => 'Pacchetto lingua mancante.',
    ],
    'lang-fallback-lang' => [
        'label' => 'Impossibile cancellare la lingua di ripiego di defualt',
        'placeholder' => 'Impossibile cancellare la lingua di ripiego di defualt',
    ],

    /*
      |--------------------------------------
      | Plugin Settings
      |--------------------------------------
     */
    'add_plugin' => [
        'label' => 'Aggiungi plugin',
        'placeholder' => 'Aggiungi plugin',
    ],
    'plugins' => [
        'label' => 'Plugins',
        'placeholder' => 'Plugins',
    ],
    'upload' => [
        'label' => 'Carica',
        'placeholder' => 'Carica',
    ],
    'plugins-list' => [
        'label' => 'Lista plugins',
        'placeholder' => 'Lista plugins',
    ],
    'plugin-exists' => [
        'label' => 'Il plugin esiste già',
        'placeholder' => 'Il plugin esiste già',
    ],
    'plugin-installed' => [
        'label' => 'Plugin installato correttamente.',
        'placeholder' => 'Plugin installato correttamente.',
    ],
    'plugin-path-missing' => [
        'label' => 'Il percorso del plugin non esiste',
        'placeholder' => 'Il percorso del plugin non esiste',
    ],
    'no-plugin-file' => [
        'label' => 'Manca ',
        'placeholder' => 'Manca ',
    ],
    'plugin-config-missing' => [
        'label' => 'Manca file <b>config.php</b> o <b>ServiceProvider.php</b>',
        'placeholder' => 'Manca file <b>config.php</b> o <b>ServiceProvider.php</b>',
    ],
    'plugin-info' => [
        'label' => 'Sei uno sviluppatore? Ti incoraggiamo a sviluppare i tuoi plugin ed a condividerli con la comunità.',
        'placeholder' => 'Sei uno sviluppatore? Ti incoraggiamo a sviluppare i tuoi plugin ed a condividerli con la comunità.',
    ],
    'plugin-info-pro' => [
        'label' => ' per acquistare plugin disponibili nella versione Pro.',
        'placeholder' => ' per acquistare plugin disponibili nella versione Pro.',
    ],
    'click-here' => [
        'label' => 'Clicca qui',
        'placeholder' => 'Clicca qui',
    ],
    /*
      |----------------------------------------------------------------------------------------
      | Manage Pages [English(en)]
      |----------------------------------------------------------------------------------------
      |
      | The following language lines are used in all Manage related issues to translate
      | some words in view to English. You are free to change them to anything you want to
      | customize your views to better match your application.
      |
     */
    'manage' => [
        'label' => 'Gestione',
        'placeholder' => 'Gestione',
    ],
    /*
      |--------------------------------------
      |  Help Topic index Page
      |--------------------------------------
     */
    'help_topics' => [
        'label' => 'Aree di supporto',
        'placeholder' => 'Aree di supporto',
    ],
    'topic' => [
        'label' => 'Area',
        'placeholder' => 'Area',
    ],
    'type' => [
        'label' => 'Tipo',
        'placeholder' => 'Tipo',
    ],
    'priority' => [
        'label' => 'Priorità',
        'placeholder' => 'Priorità',
    ],
    'last_updated' => [
        'label' => 'Ultimo aggiornamento',
        'placeholder' => 'Ultimo aggiornamento',
    ],
    'create_help_topic' => [
        'label' => 'Crea area di supporto',
        'placeholder' => 'Crea area di supporto',
    ],
    'action' => [
        'label' => 'Azione',
        'placeholder' => 'Azione',
    ],
    /*
      |--------------------------------------
      |  Help Topic Create Page
      |--------------------------------------
     */
    'active' => [
        'label' => 'Attivo',
        'placeholder' => 'Attivo',
    ],
    'disabled' => [
        'label' => 'Disabilitato',
        'placeholder' => 'Disabilitato',
    ],
    'public' => [
        'label' => 'Pubblico',
        'placeholder' => 'Pubblico',
    ],
    'private' => [
        'label' => 'Privato',
        'placeholder' => 'Privato',
    ],
    'parent_topic' => [
        'label' => 'Area principale',
        'placeholder' => 'Area principale',
    ],
    'Custom_form' => [
        'label' => 'Modulo personalizzato',
        'placeholder' => 'Modulo personalizzato',
    ],
    'SLA_plan' => [
        'label' => 'Piano SLA',
        'placeholder' => 'Piano SLA',
    ],
    'sla-plans' => [
        'label' => 'Piani SLA',
        'placeholder' => 'Piani SLA',
    ],
    'auto_assign' => [
        'label' => 'Auto assegna',
        'placeholder' => 'Auto assegna',
    ],
    'auto_respons' => [
        'label' => 'Auto risponditore',
        'placeholder' => 'Auto risponditore',
    ],
    'ticket_number_format' => [
        'label' => 'Formato numero ticket',
        'placeholder' => 'Formato numero ticket',
    ],
    'system_default' => [
        'label' => 'Default di sistema',
        'placeholder' => 'Default di sistema',
    ],
    'custom' => [
        'label' => 'Personalizzato',
        'placeholder' => 'Personalizzato',
    ],
    'internal_notes' => [
        'label' => 'Note interne',
        'placeholder' => 'Note interne',
    ],
    'select_a_parent_topic' => [
        'label' => 'Seleziona area di supporto',
        'placeholder' => 'Seleziona area di supporto',
    ],
    'custom_form' => [
        'label' => 'Modulo personalizzato',
        'placeholder' => 'Modulo personalizzato',
    ],
    'select_a_form' => [
        'label' => 'Seleziona un modulo',
        'placeholder' => 'Seleziona un modulo',
    ],
    'select_a_department' => [
        'label' => 'Seleziona un dipartimento',
        'placeholder' => 'Seleziona un dipartimento',
    ],
    'departments' => [
        'label' => 'Dipartimenti',
        'placeholder' => 'Dipartimenti',
    ],
    'select_a_priority' => [
        'label' => 'Seleziona una priorità',
        'placeholder' => 'Seleziona una priorità',
    ],
    'priorities' => [
        'label' => 'Priorità',
        'placeholder' => 'Priorità',
    ],
    'select_a_sla_plan' => [
        'label' => 'Seleziona un piano SLA',
        'placeholder' => 'Seleziona un piano SLA',
    ],
    'sla_plans' => [
        'label' => 'Piani SLA',
        'placeholder' => 'Piani SLA',
    ],
    'select_an_agent' => [
        'label' => 'Seleziona un agente',
        'placeholder' => 'Seleziona un agente',
    ],
    'helptopic_created_successfully' => [
        'label' => 'Area di supporto creata correttamente',
        'placeholder' => 'Area di supporto creata correttamente',
    ],
    'helptopic_can_not_create' => [
        'label' => "Impossibile creare l'area di supporto",
        'placeholder' => "Impossibile creare l'area di supporto",
    ],
    'helptopic_updated_successfully' => [
        'label' => 'Area di supporto aggiornata correttamente',
        'placeholder' => 'Area di supporto aggiornata correttamente',
    ],
    'helptopic_can_not_update' => [
        'label' => 'Impossibile aggiornare area di supporto',
        'placeholder' => 'Impossibile aggiornare area di supporto',
    ],
    'you_cannot_delete_default_department' => [
        'label' => 'Non puoi cancellare il dipartimento predefinito',
        'placeholder' => 'Non puoi cancellare il dipartimento predefinito',
    ],
    'have_been_moved_to_default_help_topic' => [
        'label' => 'è stato spostato versa l\'area di supporto predefinita',
        'placeholder' => 'è stato spostato versa l\'area di supporto predefinita',
    ],
    'helptopic_deleted_successfully' => [
        'label' => 'Area di supporto cancellata con successo',
        'placeholder' => 'Area di supporto cancellata con successo',
    ],
    'make-default-helptopic' => [
        'label' => "Rendi l'area di supporto predefinita",
        'placeholder' => "Rendi l'area di supporto predefinita",
    ],
    /*
      |--------------------------------------
      |  SLA plan Index Page
      |--------------------------------------
     */
    'create_SLA' => [
        'label' => 'Crea SLA',
        'placeholder' => 'Crea SLA',
    ],
    'grace_period' => [
        'label' => 'Periodo di validità',
        'placeholder' => 'Periodo di validità',
    ],
    'added_date' => [
        'label' => 'Data aggiunta',
        'placeholder' => 'Data aggiunta',
    ],
    /*
      |--------------------------------------
      |  SLA plan Create Page
      |--------------------------------------
     */
    'transient' => [
        'label' => 'Transitorio',
        'placeholder' => 'Transitorio',
    ],
    'ticket_overdue_alert' => [
        'label' => 'Avvisi ritardo Ticket',
        'placeholder' => 'Avvisi ritardo Ticket',
    ],
    'sla_plan_created_successfully' => [
        'label' => 'Piano SLA creato con successo',
        'placeholder' => 'Piano SLA creato con successo',
    ],
    'sla_plan_can_not_create' => [
        'label' => 'Impossibile creare il piano SLA',
        'placeholder' => 'Impossibile creare il piano SLA',
    ],
    'sla_plan_updated_successfully' => [
        'label' => 'Piano SLAaggoprmatp con successo',
        'placeholder' => 'Piano SLAaggoprmatp con successo',
    ],
    'sla_plan_can_not_update' => [
        'label' => 'Impossibile aggiornare il piano SLA',
        'placeholder' => 'Impossibile aggiornare il piano SLA',
    ],
    'have_been_moved_to_default_sla' => [
        'label' => 'è stato spostato verso il piano SLA predefinito',
        'placeholder' => 'è stato spostato verso il piano SLA predefinito',
    ],
    'associated_department_have_been_moved_to_default_sla' => [
        'label' => 'Il dipartimento associato è stato assegnato il piano SLA predefinito',
        'placeholder' => 'Il dipartimento associato è stato assegnato il piano SLA predefinito',
    ],
    'associated_help_topic_have_been_moved_to_default_sla' => [
        'label' => 'All\'area di supporto associata è stato assegnato il piano SLA predefinito',
        'placeholder' => 'All\'area di supporto associata è stato assegnato il piano SLA predefinito',
    ],
    'sla_plan_deleted_successfully' => [
        'label' => 'Piano SLA cancellato con successo',
        'placeholder' => 'Piano SLA cancellato con successo',
    ],
    'sla_plan_can_not_delete' => [
        'label' => 'Impossibile cancellare il piano SLA',
        'placeholder' => 'Impossibile cancellare il piano SLA',
    ],
    'make-default-sla' => [
        'label' => 'Rendi il piano SLA predefinito',
        'placeholder' => 'Rendi il piano SLA predefinito',
    ],
    /*
      |--------------------------------------
      |  Work Flow
      |--------------------------------------
     */
    'workflow' => [
        'label' => 'Workflow',
        'placeholder' => 'Workflow',
    ],
    'ticket_workflow' => [
        'label' => 'Ticket workflow',
        'placeholder' => 'Ticket workflow',
    ],
    'create_workflow' => [
        'label' => 'Crea workflow',
        'placeholder' => 'Crea workflow',
    ],
    'edit_workflow' => [
        'label' => 'Modifica workflow',
        'placeholder' => 'Modifica workflow',
    ],
    'updated' => [
        'label' => 'Aggiornato',
        'placeholder' => 'Aggiornato',
    ],
    'target' => [
        'label' => 'Obbiettivo',
        'placeholder' => 'Obbiettivo',
    ],
    'target_channel' => [
        'label' => 'Canale obbiettivo',
        'placeholder' => 'Canale obbiettivo',
    ],
    'execution_order' => [
        'label' => 'Ordine di esecuzione',
        'placeholder' => 'Ordine di esecuzione',
    ],
    'workflow_rules' => [
        'label' => 'Regole workflow',
        'placeholder' => 'Regole workflow',
    ],
    'workflow_action' => [
        'label' => 'Azione workflow',
        'placeholder' => 'Azione workflow',
    ],
    'rules' => [
        'label' => 'Regole',
        'placeholder' => 'Regole',
    ],
    'order' => [
        'label' => 'Ordine',
        'placeholder' => 'Ordine',
    ],
    'condition' => [
        'label' => 'Condizione',
        'placeholder' => 'Condizione',
    ],
    'statement' => [
        'label' => 'Istruzione',
        'placeholder' => 'Istruzione',
    ],
    'select_a_channel' => [
        'label' => 'Seleziona un canale',
        'placeholder' => 'Seleziona un canale',
    ],
    'body' => [
        'label' => 'Corpo',
        'placeholder' => 'Corpo',
    ],
    'select_one' => [
        'label' => 'Seleziona',
        'placeholder' => 'Seleziona',
    ],
    'equal_to' => [
        'label' => 'Uguale a',
        'placeholder' => 'Uguale a',
    ],
    'not_equal_to' => [
        'label' => 'Non uguale a',
        'placeholder' => 'Non uguale a',
    ],
    'contains' => [
        'label' => 'Contiene',
        'placeholder' => 'Contiene',
    ],
    'does_not_contain' => [
        'label' => 'Non contiene',
        'placeholder' => 'Non contiene',
    ],
    'starts_with' => [
        'label' => 'Comincia con',
        'placeholder' => 'Comincia con',
    ],
    'ends_with' => [
        'label' => 'Finisce con',
        'placeholder' => 'Finisce con',
    ],
    'select_an_action' => [
        'label' => "Seleziona un'azione",
        'placeholder' => "Seleziona un'azione",
    ],
    'reject_ticket' => [
        'label' => 'Respingi ticket',
        'placeholder' => 'Respingi ticket',
    ],
    'set_department' => [
        'label' => 'Imposta dipartimento',
        'placeholder' => 'Imposta dipartimento',
    ],
    'set_priority' => [
        'label' => 'Imposta priorità',
        'placeholder' => 'Imposta priorità',
    ],
    'set_sla_plan' => [
        'label' => 'Imposta piano SLA',
        'placeholder' => 'Imposta piano SLA',
    ],
    'assign_team' => [
        'label' => 'Assegna team',
        'placeholder' => 'Assegna team',
    ],
    'assign_agent' => [
        'label' => 'Assegna agente',
        'placeholder' => 'Assegna agente',
    ],
    'set_help_topic' => [
        'label' => 'Imposta area di supporto',
        'placeholder' => 'Imposta area di supporto',
    ],
    'set_ticket_status' => [
        'label' => 'Imposta stato del ticket',
        'placeholder' => 'Imposta stato del ticket',
    ],
    'workflow_created_successfully' => [
        'label' => 'Workflow creato correttamente',
        'placeholder' => 'Workflow creato correttamente',
    ],
    'workflow_updated_successfully' => [
        'label' => 'Workflow aggiornato correttamente',
        'placeholder' => 'Workflow aggiornato correttamente',
    ],
    'workflow_deleted_successfully' => [
        'label' => 'Workflow cancellato correttamente',
        'placeholder' => 'Workflow cancellato correttamente',
    ],

    /*
      |--------------------------------------
      |  Form Create Page
      |--------------------------------------
     */
    'title' => [
        'label' => 'Titolo',
        'placeholder' => 'Titolo',
    ],
    'instruction' => [
        'label' => 'Istruzione',
        'placeholder' => 'Istruzione',
    ],
    'label' => [
        'label' => 'Etichetta',
        'placeholder' => 'Etichetta',
    ],
    'visibility' => [
        'label' => 'Visibilità',
        'placeholder' => 'Visibilità',
    ],
    'variable' => [
        'label' => 'Variabile',
        'placeholder' => 'Variabile',
    ],
    'create_form' => [
        'label' => 'Crea modulo',
        'placeholder' => 'Crea modulo',
    ],
    'forms' => [
        'label' => 'Moduli',
        'placeholder' => 'Moduli',
    ],
    'form_name' => [
        'label' => 'Nome del modulo',
        'placeholder' => 'Nome del modulo',
    ],
    'view_this_form' => [
        'label' => 'Vedi questo modulo',
        'placeholder' => 'Vedi questo modulo',
    ],
    'delete_from' => [
        'label' => 'Cancella modulo',
        'placeholder' => 'Cancella modulo',
    ],
    'are_you_sure_you_want_to_delete' => [
        'label' => 'Sicuro di voler cancellare',
        'placeholder' => 'Sicuro di voler cancellare',
    ],
    'close' => [
        'label' => 'Chiuso',
        'placeholder' => 'Chiuso',
    ],
    'instructions' => [
        'label' => 'Istruzioni',
        'placeholder' => 'Istruzioni',
    ],
    'instructions_on_creating_form' => "Seleziona il tipo di campo che vuoi aggiungere al modulo sottostante e clicca sul menu a cascata 'Tipo'. Non dimenticare di settare le opzioni del campo se il tipo è selezionato,checkbox, radio... Separate ogni opzione con una virgola. Dopo aver creato il modulo, potete salvarlo cliccando il pulsante \"Salva modulo\"",
    'form_properties' => [
        'label' => 'Proprietà modulo',
        'placeholder' => 'Proprietà modulo',
    ],
    'adding_fields' => [
        'label' => 'Aggiungi campi',
        'placeholder' => 'Aggiungi campi',
    ],
    'click_add_fields_button_to_add_fields' => "Clicca il pulsante <b>'Aggiungi campi'</b> per aggiungere campi",
    'add_fields' => [
        'label' => 'Aggiungi campo',
        'placeholder' => 'Aggiungi campo',
    ],
    'save_form' => [
        'label' => 'Salva modulo',
        'placeholder' => 'Salva modulo',
    ],
    'name' => [
        'label' => 'Nome',
        'placeholder' => 'Nome',
    ],
    'values(selected_fields)' => [
        'label' => 'Valori(campi selezionati)',
        'placeholder' => 'Valori(campi selezionati)',
    ],
    'required' => [
        'label' => 'Richiesto',
        'placeholder' => 'Richiesto',
    ],
    'Action' => [
        'label' => 'Azione',
        'placeholder' => 'Azione',
    ],
    'remove' => [
        'label' => 'Rimuovi',
        'placeholder' => 'Rimuovi',
    ],
    'form_deleted_successfully' => [
        'label' => 'Modulo cancellato con successo',
        'placeholder' => 'Modulo cancellato con successo',
    ],
    'successfully_created_form' => [
        'label' => 'Modulo creato con successo',
        'placeholder' => 'Modulo creato con successo',
    ],
    'please_fill_form_name' => [
        'label' => 'Per favore compila il nome del modulo',
        'placeholder' => 'Per favore compila il nome del modulo',
    ],
    'category_inserted_successfully' => [
        'label' => 'Categoria inserta correttamente',
        'placeholder' => 'Categoria inserta correttamente',
    ],
    'category_not_inserted' => [
        'label' => 'Categoria non inserta',
        'placeholder' => 'Categoria non inserta',
    ],
    'category_updated_successfully' => [
        'label' => 'Categoria aggiornata correttamente',
        'placeholder' => 'Categoria aggiornata correttamente',
    ],
    'category_not_updated' => [
        'label' => 'Categoria non aggiornata',
        'placeholder' => 'Categoria non aggiornata',
    ],
    'category_deleted_successfully' => [
        'label' => 'Categoria cancellata correttamente',
        'placeholder' => 'Categoria cancellata correttamente',
    ],
    'category_not_deleted' => [
        'label' => 'Categoria non cancellata',
        'placeholder' => 'Categoria non cancellata',
    ],
    'article_inserted_successfully' => [
        'label' => 'Articolo inserito correttamente',
        'placeholder' => 'Articolo inserito correttamente',
    ],
    'article_not_inserted' => [
        'label' => 'Articolo non inserto',
        'placeholder' => 'Articolo non inserto',
    ],
    'article_updated_successfully' => [
        'label' => 'Articolo aggiornato correttamente',
        'placeholder' => 'Articolo aggiornato correttamente',
    ],
    'article_not_updated' => [
        'label' => 'Articolo non aggiornato',
        'placeholder' => 'Articolo non aggiornato',
    ],
    'article_deleted_successfully' => [
        'label' => 'Articolo cancellato correttamente',
        'placeholder' => 'Articolo cancellato correttamente',
    ],
    'article_not_deleted' => [
        'label' => 'Articolo non cancellato',
        'placeholder' => 'Articolo non cancellato',
    ],
    'article_can_not_deleted' => [
        'label' => "Impossibile cancellare l'articolo",
        'placeholder' => "Impossibile cancellare l'articolo",
    ],
    'page_created_successfully' => [
        'label' => 'Pagina creata con successo',
        'placeholder' => 'Pagina creata con successo',
    ],
    'your_page_updated_successfully' => [
        'label' => 'Pagina aggiornata con successo',
        'placeholder' => 'Pagina aggiornata con successo',
    ],
    'page_deleted_successfully' => [
        'label' => 'Pagina cancellata con successo',
        'placeholder' => 'Pagina cancellata con successo',
    ],
    'settings_updated_successfully' => [
        'label' => 'Impostazioni aggiornate correttamente',
        'placeholder' => 'Impostazioni aggiornate correttamente',
    ],
    'settings_can_not_updated' => [
        'label' => 'Impossibile aggiornare le impostazioni',
        'placeholder' => 'Impossibile aggiornare le impostazioni',
    ],
    'can_not_process' => [
        'label' => 'Impossibile elaborare',
        'placeholder' => 'Impossibile elaborare',
    ],
    'comment_published' => [
        'label' => 'Commento pubblicato',
        'placeholder' => 'Commento pubblicato',
    ],
    'comment_deleted' => [
        'label' => 'Commento cancellato',
        'placeholder' => 'Commento cancellato',
    ],
    'publish_time' => [
        'label' => 'Data di pubblicazione',
        'placeholder' => 'Data di pubblicazione',
    ],
    /*
      |----------------------------------------------------------------------------------------
      | Theme Pages [English(en)]
      |----------------------------------------------------------------------------------------
      |
      | The following language lines are used in all Theme related issues to translate
      | some words in view to English. You are free to change them to anything you want to
      | customize your views to better match your application.
      |
     */
    'themes' => [
        'label' => 'Temi',
        'placeholder' => 'Temi',
    ],
    /*
      |--------------------------------------
      |  Footer Pages
      |--------------------------------------
     */
    'footer' => [
        'label' => 'Footer',
        'placeholder' => 'Footer',
    ],
    'footer1' => [
        'label' => 'Footer1',
        'placeholder' => 'Footer1',
    ],
    'footer2' => [
        'label' => 'Footer2',
        'placeholder' => 'Footer2',
    ],
    'footer3' => [
        'label' => 'Footer3',
        'placeholder' => 'Footer3',
    ],
    'footer4' => [
        'label' => 'Footer4',
        'placeholder' => 'Footer4',
    ],
    /*
      |--------------------------------------
      |  Custom alert box
      |--------------------------------------
     */
    'ok' => [
        'label' => 'Ok',
        'placeholder' => 'Ok',
    ],
    'cancel' => [
        'label' => 'Annulla',
        'placeholder' => 'Annulla',
    ],
    'select-ticket' => [
        'label' => 'Per favore seleziona i ticket',
        'placeholder' => 'Per favore seleziona i ticket',
    ],
    'confirm' => [
        'label' => 'Sei sicuro?',
        'placeholder' => 'Sei sicuro?',
    ],
    'delete-tickets' => [
        'label' => 'Cancella ticket',
        'placeholder' => 'Cancella ticket',
    ],
    'close-tickets' => [
        'label' => 'Chiudi ticket',
        'placeholder' => 'Chiudi ticket',
    ],
    'open-tickets' => [
        'label' => 'Apri ticket',
        'placeholder' => 'Apri ticket',
    ],
    /*
      |----------------------------------------------------------------------------------------
      | Staff Pages [English(en)]
      |----------------------------------------------------------------------------------------
      |
      | The following language lines are used in all Staff related issues to translate
      | some words in view to English. You are free to change them to anything you want to
      | customize your views to better match your application.
      |
     */
    'are_you_sure' => [
        'label' => 'Sei sicuro',
        'placeholder' => 'Sei sicuro',
    ],
    'staffs' => [
        'label' => 'Organico',
        'placeholder' => 'Organico',
    ],
    'user_name' => [
        'label' => 'Nome utente',
        'placeholder' => 'Nome utente',
    ],
    'status' => [
        'label' => 'Stato',
        'placeholder' => 'Stato',
    ],
    'group' => [
        'label' => 'Gruppo',
        'placeholder' => 'Gruppo',
    ],
    'department' => [
        'label' => 'Dipartimento',
        'placeholder' => 'Dipartimento',
    ],
    'created' => [
        'label' => 'aperti',
        'placeholder' => 'aperti',
    ],
    'lastlogin' => [
        'label' => 'Ultimo accesso',
        'placeholder' => 'Ultimo accesso',
    ],
    'createagent' => [
        'label' => 'Crea agente',
        'placeholder' => 'Crea agente',
    ],
    'delete' => [
        'label' => 'Cancella',
        'placeholder' => 'Cancella',
    ],
    'agents' => [
        'label' => 'Agenti',
        'placeholder' => 'Agenti',
    ],
    // 'create' => [
    //     'label' => 'Crea',
    //     'placeholder' => 'Crea',
    // ],
    'create' => '<i class="bi bi-plus-square-dotted"></i> Crea Ticket',
    'edit' => [
        'label' => 'Modifica',
        'placeholder' => 'Modifica',
    ],
    'groups' => [
        'label' => 'Gruppi',
        'placeholder' => 'Gruppi',
    ],
    'time_zones' => [
        'label' => 'Fusi orari',
        'placeholder' => 'Fusi orari',
    ],
    /*
      |--------------------------------------
      |  Staff Create Page
      |--------------------------------------
     */
    'create_agent' => [
        'label' => 'Crea agente',
        'placeholder' => 'Crea agente',
    ],
    'last_name' => [
        'label' => 'Cognome',
        'placeholder' => 'Cognome',
    ],
    'mobile_number' => [
        'label' => 'Cellulare',
        'placeholder' => 'Cellulare',
    ],
    'agent_signature' => [
        'label' => 'Firma agente',
        'placeholder' => 'Firma agente',
    ],
    'account_status_setting' => [
        'label' => 'Stato account ed impostazioni',
        'placeholder' => 'Stato account ed impostazioni',
    ],
    'account_type' => [
        'label' => 'Tipo account',
        'placeholder' => 'Tipo account',
    ],
    'admin' => [
        'label' => 'Amministratore',
        'placeholder' => 'Amministratore',
    ],
    'agent' => [
        'label' => 'Agente',
        'placeholder' => 'Agente',
    ],
    'account_status' => [
        'label' => 'Stato account',
        'placeholder' => 'Stato account',
    ],
    'locked' => [
        'label' => 'Bloccato',
        'placeholder' => 'Bloccato',
    ],
    'assigned_group' => [
        'label' => 'Gruppo assegnato',
        'placeholder' => 'Gruppo assegnato',
    ],
    'primary_department' => [
        'label' => 'Dipartimento principale',
        'placeholder' => 'Dipartimento principale',
    ],
    'agent_time_zone' => [
        'label' => 'Fuso orario agente',
        'placeholder' => 'Fuso orario agente',
    ],
    'day_light_saving' => [
        'label' => 'Ora legale',
        'placeholder' => 'Ora legale',
    ],
    'limit_access' => [
        'label' => 'Limita accesso',
        'placeholder' => 'Limita accesso',
    ],
    'directory_listing' => [
        'label' => 'Elenco directory',
        'placeholder' => 'Elenco directory',
    ],
    'vocation_mode' => [
        'label' => 'Vacanza',
        'placeholder' => 'Vacanza',
    ],
    'assigned_team' => [
        'label' => 'Team assegnato',
        'placeholder' => 'Team assegnato',
    ],
    'agent_send_mail_error_on_agent_creation' => [
        'label' => "Si sono verificati errori nell'invio della mail all'agente. Per favore controlla le impostazioni email e riprova",
        'placeholder' => "Si sono verificati errori nell'invio della mail all'agente. Per favore controlla le impostazioni email e riprova",
    ],
    'agent_creation_success' => [
        'label' => 'Agente creato con successo',
        'placeholder' => 'Agente creato con successo',
    ],
    'failed_to_create_agent' => [
        'label' => "Impossibile creare l'agente",
        'placeholder' => "Impossibile creare l'agente",
    ],
    'failed_to_edit_agent' => [
        'label' => "Impossibile modificare l'agente",
        'placeholder' => "Impossibile modificare l'agente",
    ],
    'agent_updated_sucessfully' => [
        'label' => 'Agente aggiornato correttamente',
        'placeholder' => 'Agente aggiornato correttamente',
    ],
    'unable_to_update_agent' => [
        'label' => "Impossibile aggiornare l'agente",
        'placeholder' => "Impossibile aggiornare l'agente",
    ],
    'agent_deleted_sucessfully' => [
        'label' => 'Agente cancellato con successo',
        'placeholder' => 'Agente cancellato con successo',
    ],
    'this_staff_is_related_to_some_tickets' => [
        'label' => 'Questo membro è collegato a qualche ticket',
        'placeholder' => 'Questo membro è collegato a qualche ticket',
    ],
    'list_of_agents' => [
        'label' => 'Lista agenti',
        'placeholder' => 'Lista agenti',
    ],
    'create_an_agent' => [
        'label' => 'Crea agente',
        'placeholder' => 'Crea agente',
    ],
    'edit_an_agent' => [
        'label' => 'Modifica agente',
        'placeholder' => 'Modifica agente',
    ],

    /*
      |--------------------------------------
      |  Department Create Page
      |--------------------------------------
     */
    'create_department' => [
        'label' => 'Crea dipartimento',
        'placeholder' => 'Crea dipartimento',
    ],
    'manager' => [
        'label' => 'Manager',
        'placeholder' => 'Manager',
    ],
    'ticket_assignment' => [
        'label' => 'Assegnamento ticket ',
        'placeholder' => 'Assegnamento ticket ',
    ],
    'restrict_ticket_assignment_to_department_members' => [
        'label' => 'Limita assegnamento ticket a membri del dipartimento',
        'placeholder' => 'Limita assegnamento ticket a membri del dipartimento',
    ],
    'outgoing_emails' => [
        'label' => 'Emails in uscita',
        'placeholder' => 'Emails in uscita',
    ],
    'outgoing_email' => [
        'label' => 'Email da usare',
        'placeholder' => 'Email da usare',
    ],
    'auto_responding_settings' => [
        'label' => 'Impostazioni auto-risposta',
        'placeholder' => 'Impostazioni auto-risposta',
    ],
    'disable_for_this_department' => [
        'label' => 'Disattiva per questo dipartimento',
        'placeholder' => 'Disattiva per questo dipartimento',
    ],
    'auto_response_email' => [
        'label' => 'Email auto-risposta',
        'placeholder' => 'Email auto-risposta',
    ],
    'recipient' => [
        'label' => 'Destinatario',
        'placeholder' => 'Destinatario',
    ],
    'group_access' => [
        'label' => 'Accesso gruppo',
        'placeholder' => 'Accesso gruppo',
    ],
    'department_signature' => [
        'label' => 'Firma dipartimento',
        'placeholder' => 'Firma dipartimento',
    ],
    'list_of_departments' => [
        'label' => 'Lista dipartimenti',
        'placeholder' => 'Lista dipartimenti',
    ],
    'create_a_department' => [
        'label' => 'Crea dipartimento',
        'placeholder' => 'Crea dipartimento',
    ],
    'outgoing_email_settings' => [
        'label' => 'Impostazioni email in uscita',
        'placeholder' => 'Impostazioni email in uscita',
    ],
    'edit_department' => [
        'label' => 'Modifica dipartimento',
        'placeholder' => 'Modifica dipartimento',
    ],
    'select_a_sla' => [
        'label' => 'Seleziona piano SLA',
        'placeholder' => 'Seleziona piano SLA',
    ],
    'select_a_manager' => [
        'label' => 'Seleziona un manager',
        'placeholder' => 'Seleziona un manager',
    ],
    'department_created_sucessfully' => [
        'label' => 'Dipartimento creato con successo',
        'placeholder' => 'Dipartimento creato con successo',
    ],
    'failed_to_create_department' => [
        'label' => 'Impossibile creare il dipartimento',
        'placeholder' => 'Impossibile creare il dipartimento',
    ],
    'department_updated_sucessfully' => [
        'label' => 'Dipartimento aggiornato correttamente',
        'placeholder' => 'Dipartimento aggiornato correttamente',
    ],
    'department_not_updated' => [
        'label' => 'Dipartimento non aggiornato',
        'placeholder' => 'Dipartimento non aggiornato',
    ],
    'have_been_moved_to_default_department' => [
        'label' => 'è stato assegnato al dipartimento predefinito',
        'placeholder' => 'è stato assegnato al dipartimento predefinito',
    ],
    'the_associated_helptopic_has_been_deactivated' => [
        'label' => 'L\'area di supporto associata è stata disattivata',
        'placeholder' => 'L\'area di supporto associata è stata disattivata',
    ],
    'department_deleted_sucessfully' => [
        'label' => 'Dipartimento cancellato con successo',
        'placeholder' => 'Dipartimento cancellato con successo',
    ],
    'department_can_not_delete' => [
        'label' => 'Impossibile cancellare dipartimento',
        'placeholder' => 'Impossibile cancellare dipartimento',
    ],
    'make-default-department' => [
        'label' => 'Rendi il dipartimento predefinito',
        'placeholder' => 'Rendi il dipartimento predefinito',
    ],
    /*
      |--------------------------------------
      |  Team Create Page
      |--------------------------------------
     */
    'create_team' => [
        'label' => 'Crea team',
        'placeholder' => 'Crea team',
    ],
    'assignment_alert' => [
        'label' => 'Avviso assegnamento',
        'placeholder' => 'Avviso assegnamento',
    ],
    'disable_for_this_team' => [
        'label' => 'Disattiva per questo team',
        'placeholder' => 'Disattiva per questo team',
    ],
    'teams' => [
        'label' => 'Teams',
        'placeholder' => 'Teams',
    ],
    'list_of_teams' => [
        'label' => 'Lista teams',
        'placeholder' => 'Lista teams',
    ],
    'create_a_team' => [
        'label' => 'Crea team',
        'placeholder' => 'Crea team',
    ],
    'edit_a_team' => [
        'label' => 'Modifica team',
        'placeholder' => 'Modifica team',
    ],
    'teams_created_successfully' => [
        'label' => 'Team creato con successo',
        'placeholder' => 'Team creato con successo',
    ],
    'teams_can_not_create' => [
        'label' => 'Impossibile creare il team',
        'placeholder' => 'Impossibile creare il team',
    ],
    'teams_updated_successfully' => [
        'label' => 'Team aggiornato con successo',
        'placeholder' => 'Team aggiornato con successo',
    ],
    'teams_can_not_update' => [
        'label' => 'Impossibile aggiornare il team',
        'placeholder' => 'Impossibile aggiornare il team',
    ],
    'teams_deleted_successfully' => [
        'label' => 'Team cancell con successo',
        'placeholder' => 'Team cancell con successo',
    ],
    'teams_can_not_delete' => [
        'label' => 'Impossibile cancellare il teams',
        'placeholder' => 'Impossibile cancellare il teams',
    ],
    'select_a_team' => [
        'label' => 'Seleziona un team',
        'placeholder' => 'Seleziona un team',
    ],
    'select_a_team_lead' => [
        'label' => 'Seleziona un team leader',
        'placeholder' => 'Seleziona un team leader',
    ],
    'members' => [
        'label' => 'Membri',
        'placeholder' => 'Membri',
    ],
    /*
      |--------------------------------------
      |  Group Create Page
      |--------------------------------------
     */
    'create_group' => [
        'label' => 'Crea gruppo',
        'placeholder' => 'Crea gruppo',
    ],
    'goups' => [
        'label' => 'Gruppi',
        'placeholder' => 'Gruppi',
    ],
    'can_create_ticket' => [
        'label' => 'Può creare ticket',
        'placeholder' => 'Può creare ticket',
    ],
    'can_edit_ticket' => [
        'label' => 'Può editare ticket',
        'placeholder' => 'Può editare ticket',
    ],
    'can_post_ticket' => [
        'label' => 'Può postare Ticket',
        'placeholder' => 'Può postare Ticket',
    ],
    'can_close_ticket' => [
        'label' => 'Può chiudere un ticket ',
        'placeholder' => 'Può chiudere un ticket ',
    ],
    'can_assign_ticket' => [
        'label' => 'Può assegnare ticket',
        'placeholder' => 'Può assegnare ticket',
    ],
    'can_transfer_ticket' => [
        'label' => 'Può trasferire ticket',
        'placeholder' => 'Può trasferire ticket',
    ],
    'can_delete_ticket' => [
        'label' => 'Può cancellare ticket',
        'placeholder' => 'Può cancellare ticket',
    ],
    'can_ban_emails' => [
        'label' => 'Può bannare le email',
        'placeholder' => 'Può bannare le email',
    ],
    'can_manage_premade' => [
        'label' => 'Può gestire premade',
        'placeholder' => 'Può gestire premade',
    ],
    'can_manage_FAQ' => [
        'label' => 'Può gestire le FAQ',
        'placeholder' => 'Può gestire le FAQ',
    ],
    'can_view_agent_stats' => [
        'label' => 'Può visualizzare le statistiche per agente',
        'placeholder' => 'Può visualizzare le statistiche per agente',
    ],
    'department_access' => [
        'label' => 'Accesso dipartimenti ',
        'placeholder' => 'Accesso dipartimenti ',
    ],
    'admin_notes' => [
        'label' => 'Note amministratore',
        'placeholder' => 'Note amministratore',
    ],
    'group_members' => [
        'label' => 'Componenti del gruppo',
        'placeholder' => 'Componenti del gruppo',
    ],
    'group_name' => [
        'label' => 'Nome gruppo',
        'placeholder' => 'Nome gruppo',
    ],
    'list_of_groups' => [
        'label' => 'Elenco gruppi',
        'placeholder' => 'Elenco gruppi',
    ],
    'select_a_group' => [
        'label' => 'Seleziona un gruppo',
        'placeholder' => 'Seleziona un gruppo',
    ],
    'create_a_group' => [
        'label' => 'Crea gruppo',
        'placeholder' => 'Crea gruppo',
    ],
    'edit_a_group' => [
        'label' => 'Modificagruppo',
        'placeholder' => 'Modificagruppo',
    ],
    'group_created_successfully' => [
        'label' => 'Gruppo creato correttamente',
        'placeholder' => 'Gruppo creato correttamente',
    ],
    'group_can_not_create' => [
        'label' => 'Impossibile creare il gruppo',
        'placeholder' => 'Impossibile creare il gruppo',
    ],
    'group_updated_successfully' => [
        'label' => 'Gruppo aggiornato con successo',
        'placeholder' => 'Gruppo aggiornato con successo',
    ],
    'group_can_not_update' => [
        'label' => 'Impossibile aggiornare il gruppo',
        'placeholder' => 'Impossibile aggiornare il gruppo',
    ],
    'there_are_agents_assigned_to_this_group_please_unassign_them_from_this_group_to_delete' => [
        'label' => 'Esistono agenti assegnati a questo gruppo, rimuovili per poterlo cancellare',
        'placeholder' => 'Esistono agenti assegnati a questo gruppo, rimuovili per poterlo cancellare',
    ],
    'group_cannot_delete' => [
        'label' => 'Impossibile cancellare il gruppo',
        'placeholder' => 'Impossibile cancellare il gruppo',
    ],
    'group_deleted_successfully' => [
        'label' => 'Gruppo cancellato correttamente',
        'placeholder' => 'Gruppo cancellato correttamente',
    ],
    'failed_to_load_the_page' => [
        'label' => 'Impossibile caricare la pagina',
        'placeholder' => 'Impossibile caricare la pagina',
    ],
    /*
      |--------------------------------------
      |  SMTP Page
      |--------------------------------------
     */
    'driver' => [
        'label' => 'Driver',
        'placeholder' => 'Driver',
    ],
    'smtp' => [
        'label' => 'SMTP',
        'placeholder' => 'SMTP',
    ],
    'host' => [
        'label' => 'Host',
        'placeholder' => 'Host',
    ],
    'port' => [
        'label' => 'Porta',
        'placeholder' => 'Porta',
    ],
    'encryption' => [
        'label' => 'Cifratura',
        'placeholder' => 'Cifratura',
    ],
    /*
      |----------------------------------------------------------------------------------------
      | Agent Panel [English(en)]
      |----------------------------------------------------------------------------------------
      |
      | The following language lines are used in all Agent Panel related issues to translate
      | some words in view to English. You are free to change them to anything you want to
      | customize your views to better match your application.
      |
     */
    'agent_panel' => [
        'label' => 'Pannello agente',
        'placeholder' => 'Pannello agente',
    ],
    'profile' => [
        'label' => 'Profilo',
        'placeholder' => 'Profilo',
    ],
    'change_password' => [
        'label' => 'Cambia password',
        'placeholder' => 'Cambia password',
    ],
    'sign_out' => [
        'label' => 'Esci',
        'placeholder' => 'Esci',
    ],
    'Tickets' => [
        'label' => 'TICKET',
        'placeholder' => 'TICKET',
    ],
    'ticket-details' => [
        'label' => 'Ticket details',
        'placeholder' => 'Ticket details',
    ],
    'inbox' => [
        'label' => 'Inbox',
        'placeholder' => 'Inbox',
    ],
    'my_tickets' => [
        'label' => 'Miei ticket',
        'placeholder' => 'Miei ticket',
    ],
    'unassigned' => [
        'label' => 'Non assegnati',
        'placeholder' => 'Non assegnati',
    ],
    'trash' => [
        'label' => 'Cestino',
        'placeholder' => 'Cestino',
    ],
    'Updates' => [
        'label' => 'AGGIORNAMENTI',
        'placeholder' => 'AGGIORNAMENTI',
    ],
    'no_new_updates' => [
        'label' => 'Nessun nuovo aggiornamento',
        'placeholder' => 'Nessun nuovo aggiornamento',
    ],
    'check_for_updates' => [
        'label' => 'Controlla aggiornamenti',
        'placeholder' => 'Controlla aggiornamenti',
    ],
    'update-version' => [
        'label' => 'Versione aggiornamento',
        'placeholder' => 'Versione aggiornamento',
    ],
    'open' => [
        'label' => 'Apri',
        'placeholder' => 'Apri',
    ],
    'inprogress' => [
        'label' => 'In corso',
        'placeholder' => 'In corso',
    ],
    'inprogress_tickets' => [
        'label' => 'Ticket in corso',
        'placeholder' => 'Ticket in corso',
    ],
    'closed' => [
        'label' => 'Chiuso',
        'placeholder' => 'Chiuso',
    ],
    'Departments' => [
        'label' => 'DIPARTIMENTI',
        'placeholder' => 'DIPARTIMENTI',
    ],
    'tools' => [
        'label' => 'Strumenti',
        'placeholder' => 'Strumenti',
    ],
    'canned' => [
        'label' => 'Predefinita',
        'placeholder' => 'Predefinita',
    ],
    'knowledge_base' => [
        'label' => 'Knowledge Base',
        'placeholder' => 'Knowledge Base',
    ],
    'kb-settings' => [
        'label' => 'Impostazioni Knowledge base',
        'placeholder' => 'Impostazioni Knowledge base',
    ],
    'loading' => [
        'label' => 'Caricamento',
        'placeholder' => 'Caricamento',
    ],
    'ratings' => [
        'label' => 'Valutazioni',
        'placeholder' => 'Valutazioni',
    ],
    'please_rate' => [
        'label' => 'Valuta:',
        'placeholder' => 'Valuta:',
    ],
    'ticket_ratings' => [
        'label' => 'VALUTAZIONE TICKET',
        'placeholder' => 'VALUTAZIONE TICKET',
    ],
    /*
      |-----------------------------------------------
      |  Ticket
      |-----------------------------------------------
     */
    'ticket_created_successfully' => [
        'label' => 'Il ticket è stato creato correttamente',
        'placeholder' => 'Il ticket è stato creato correttamente',
    ],
    'failed_to_create_a_new_ticket' => [
        'label' => 'Impossibile creare il nuovo ticket',
        'placeholder' => 'Impossibile creare il nuovo ticket',
    ],
    'your_ticket_have_been_closed' => [
        'label' => 'Il ticket è stato chiuso',
        'placeholder' => 'Il ticket è stato chiuso',
    ],
    'your_ticket_have_been_resolved' => [
        'label' => 'Il ticket è stato risolto',
        'placeholder' => 'Il ticket è stato risolto',
    ],
    'your_ticket_have_been_opened' => [
        'label' => 'Il ticket è stato aperto',
        'placeholder' => 'Il ticket è stato aperto',
    ],
    'your_ticket_have_been_moved_to_trash' => [
        'label' => 'Il ticket è stato spostato nel cestino',
        'placeholder' => 'Il ticket è stato spostato nel cestino',
    ],
    'this_email_have_been_banned' => [
        'label' => 'Questa mail è stata bannata',
        'placeholder' => 'Questa mail è stata bannata',
    ],
    'you_have_successfully_replied_to_your_ticket' => [
        'label' => 'Hai risposto al tuo ticket con successo',
        'placeholder' => 'Hai risposto al tuo ticket con successo',
    ],
    'for_some_reason_your_message_was_not_posted_please_try_again_later' => [
        'label' => 'Per qualche ragione il tuo messaggio non è stato pubblicato, per favore prova più tardi',
        'placeholder' => 'Per qualche ragione il tuo messaggio non è stato pubblicato, per favore prova più tardi',
    ],
    'for_some_reason_your_reply_was_not_posted_please_try_again_later' => [
        'label' => 'Per qualche ragione la tua risposta non è stata pubblicata, per favore prova più tardi',
        'placeholder' => 'Per qualche ragione la tua risposta non è stata pubblicata, per favore prova più tardi',
    ],
    'you_have_unassigned_your_ticket' => [
        'label' => "Hai rimosso l'assegnazione al tuo ticket",
        'placeholder' => "Hai rimosso l'assegnazione al tuo ticket",
    ],
    'for_some_reason_your_request_failed' => [
        'label' => 'Per qualche ragione la tua richiesta è fallita',
        'placeholder' => 'Per qualche ragione la tua richiesta è fallita',
    ],
    'trash-delete-ticket' => [
        'label' => 'Cancella i ticket in modo permanente',
        'placeholder' => 'Cancella i ticket in modo permanente',
    ],
    'trash-delete-title-msg' => [
        'label' => 'Clicca qui per cancellare permanentemente i ticket.',
        'placeholder' => 'Clicca qui per cancellare permanentemente i ticket.',
    ],
    'moved_to_trash' => [
        'label' => 'Ticket selezionati spostati nel cestino.',
        'placeholder' => 'Ticket selezionati spostati nel cestino.',
    ],
    'tickets_have_been_closed' => [
        'label' => 'I ticket selezionati sono stati chiusi.',
        'placeholder' => 'I ticket selezionati sono stati chiusi.',
    ],
    'tickets_have_been_opened' => [
        'label' => 'I ticket selezionati sono stati aperti.',
        'placeholder' => 'I ticket selezionati sono stati aperti.',
    ],
    'unable_to_fetch_emails' => [
        'label' => 'Impossible caricale le emails',
        'placeholder' => 'Impossible caricale le emails',
    ],
    'reply_content_is_a_required_field' => [
        'label' => 'Il contenuto della risposta è un campo richiesto',
        'placeholder' => 'Il contenuto della risposta è un campo richiesto',
    ],
    'internal_content_is_a_required_field' => [
        'label' => 'Il contenuto interno è un campo richiesto',
        'placeholder' => 'Il contenuto interno è un campo richiesto',
    ],

    /*
      |-----------------------------------------------
      |  Profile
      |-----------------------------------------------
     */
    'view-profile' => [
        'label' => 'Visualizza profilo',
        'placeholder' => 'Visualizza profilo',
    ],
    'edit-profile' => [
        'label' => 'Modifica profilo',
        'placeholder' => 'Modifica profilo',
    ],
    'user_information' => [
        'label' => 'Informazioni utente',
        'placeholder' => 'Informazioni utente',
    ],
    'time_zone' => [
        'label' => 'Fuso orario',
        'placeholder' => 'Fuso orario',
    ],
    'phone_number' => [
        'label' => 'Numero di telefono',
        'placeholder' => 'Numero di telefono',
    ],
    'contact_information' => [
        'label' => 'Infomazioni di contatto',
        'placeholder' => 'Infomazioni di contatto',
    ],
    'Profile-Updated-sucessfully' => [
        'label' => 'Profilo aggiornato correttamente.',
        'placeholder' => 'Profilo aggiornato correttamente.',
    ],
    'User-profile-Updated-Successfully' => [
        'label' => 'Profilo utente aggiornato correttamente.',
        'placeholder' => 'Profilo utente aggiornato correttamente.',
    ],
    'User-Created-Successfully' => [
        'label' => 'Utente creato correttamente.',
        'placeholder' => 'Utente creato correttamente.',
    ],
    /*
      |-----------------------------------------------
      |  Dashboard
      |-----------------------------------------------
     */
    'dashboard' => [
        'label' => 'Cruscotto',
        'placeholder' => 'Cruscotto',
    ],
    'line_chart' => [
        'label' => 'Grafico lineare',
        'placeholder' => 'Grafico lineare',
    ],
    'statistics' => [
        'label' => 'Statistiche',
        'placeholder' => 'Statistiche',
    ],
    'opened' => [
        'label' => 'Aperti',
        'placeholder' => 'Aperti',
    ],
    'resolved' => [
        'label' => 'Risolto',
        'placeholder' => 'Risolto',
    ],
    'deleted' => [
        'label' => 'Cancellato',
        'placeholder' => 'Cancellato',
    ],
    'start_date' => [
        'label' => 'Data inizio',
        'placeholder' => 'Data inizio',
    ],
    'end_date' => [
        'label' => 'Data fine',
        'placeholder' => 'Data fine',
    ],
    'filter' => [
        'label' => 'Filtra',
        'placeholder' => 'Filtra',
    ],
    'report' => [
        'label' => 'Resoconto',
        'placeholder' => 'Resoconto',
    ],
    'Legend' => [
        'label' => 'LEGENDA',
        'placeholder' => 'LEGENDA',
    ],
    'total' => [
        'label' => 'Totale',
        'placeholder' => 'Totale',
    ],
    'dashboard_reports' => [
        'label' => 'Cruscotto',
        'placeholder' => 'Cruscotto',
    ],
    /*
      |------------------------------------------------
      |User Page
      |------------------------------------------------
     */
    'user_credentials' => [
        'label' => 'Credenziali utente',
        'placeholder' => 'Credenziali utente',
    ],
    'user_directory' => [
        'label' => 'Lista utenti',
        'placeholder' => 'Lista utenti',
    ],
    'ban' => [
        'label' => 'Ban',
        'placeholder' => 'Ban',
    ],
    'user' => [
        'label' => 'Utente',
        'placeholder' => 'Utente',
    ],
    'users' => [
        'label' => 'Utenti',
        'placeholder' => 'Utenti',
    ],
    'create_user' => [
        'label' => 'Crea utente',
        'placeholder' => 'Crea utente',
    ],
    'edit_user' => [
        'label' => 'Modifica utente',
        'placeholder' => 'Modifica utente',
    ],
    'mobile' => [
        'label' => 'Cellulare',
        'placeholder' => 'Cellulare',
    ],
    'last_login' => [
        'label' => 'Ultimo accesso',
        'placeholder' => 'Ultimo accesso',
    ],
    'user_profile' => [
        'label' => 'Profilo utente',
        'placeholder' => 'Profilo utente',
    ],
    'assign' => [
        'label' => 'Assegna',
        'placeholder' => 'Assegna',
    ],
    'open_tickets' => [
        'label' => 'Ticket aperti',
        'placeholder' => 'Ticket aperti',
    ],
    'closed_tickets' => [
        'label' => 'Ticket chiusi',
        'placeholder' => 'Ticket chiusi',
    ],
    'deleted_tickets' => [
        'label' => 'Ticket cancellati',
        'placeholder' => 'Ticket cancellati',
    ],
    'user_created_successfully' => [
        'label' => 'Utente creato correttamente',
        'placeholder' => 'Utente creato correttamente',
    ],
    'user_updated_successfully' => [
        'label' => 'Utente aggiornato correttamente',
        'placeholder' => 'Utente aggiornato correttamente',
    ],
    'profile_updated_sucessfully' => [
        'label' => 'Profilo aggiornato correttamente',
        'placeholder' => 'Profilo aggiornato correttamente',
    ],
    'password_updated_sucessfully' => [
        'label' => 'Password aggiornata con successo',
        'placeholder' => 'Password aggiornata con successo',
    ],
    'password_was_not_updated_incorrect_old_password' => [
        'label' => 'Password non aggiornata. Vecchia password errata',
        'placeholder' => 'Password non aggiornata. Vecchia password errata',
    ],
    'the_user_has_been_removed_from_this_organization' => [
        'label' => 'L\'utente è stato rimosso da questa organizzazione',
        'placeholder' => 'L\'utente è stato rimosso da questa organizzazione',
    ],
    'user_report' => [
        'label' => 'Resoconto utente',
        'placeholder' => 'Resoconto utente',
    ],
    'send_password_via_email' => [
        'label' => 'Invia password per email',
        'placeholder' => 'Invia password per email',
    ],
    'user_send_mail_error_on_user_creation' => [
        'label' => "E' avvenuto un errore durante l'invio della mail all'utente. Per favore controlla le impostazioni email e riprova",
        'placeholder' => "E' avvenuto un errore durante l'invio della mail all'utente. Per favore controlla le impostazioni email e riprova",
    ],
    'country_code' => [
        'label' => 'Codice paese',
        'placeholder' => 'Codice paese',
    ],
    /*
      |------------------------------------------------
      |Organization Page
      |------------------------------------------------
     */
    'organizations' => [
        'label' => 'Organizzazioni',
        'placeholder' => 'Organizzazioni',
    ],
    'organization' => [
        'label' => 'Organizzazione',
        'placeholder' => 'Organizzazione',
    ],
    'organization_list' => [
        'label' => 'Lista organizzazioni',
        'placeholder' => 'Lista organizzazioni',
    ],
    'view_organization_profile' => [
        'label' => 'Visualizza profilo organizzazione',
        'placeholder' => 'Visualizza profilo organizzazione',
    ],
    'create_organization' => [
        'label' => 'Crea organizzazione',
        'placeholder' => 'Crea organizzazione',
    ],
    'account_manager' => [
        'label' => 'Account manager',
        'placeholder' => 'Account manager',
    ],
    'update' => [
        'label' => 'Aggiorna',
        'placeholder' => 'Aggiorna',
    ],
    'please_select_an_organization' => [
        'label' => "Per favore seleziona un'organizzazione",
        'placeholder' => "Per favore seleziona un'organizzazione",
    ],
    'please_select_an_user' => [
        'label' => 'Per favore seleziona un utente',
        'placeholder' => 'Per favore seleziona un utente',
    ],
    'organization_profile' => [
        'label' => 'Profilo organizzazione',
        'placeholder' => 'Profilo organizzazione',
    ],
    'organization-s_head' => "Capo dell'organizzazione",
    'select_department_manager' => [
        'label' => 'Seleziona manager del dipartimento',
        'placeholder' => 'Seleziona manager del dipartimento',
    ],
    'select_organization_manager' => [
        'label' => "Seleziona un manager dell'organizzazione",
        'placeholder' => "Seleziona un manager dell'organizzazione",
    ],
    'users_of' => [
        'label' => 'Utenti di',
        'placeholder' => 'Utenti di',
    ],
    'organization_created_successfully' => [
        'label' => 'Organizzazione creata con successo',
        'placeholder' => 'Organizzazione creata con successo',
    ],
    'organization_can_not_create' => [
        'label' => "Impossibile creare l'organizzazione",
        'placeholder' => "Impossibile creare l'organizzazione",
    ],
    'organization_updated_successfully' => [
        'label' => 'Organizzazione aggiornata correttamente',
        'placeholder' => 'Organizzazione aggiornata correttamente',
    ],
    'organization_can_not_update' => [
        'label' => "Impossibile aggiornare l'organizzazione",
        'placeholder' => "Impossibile aggiornare l'organizzazione",
    ],
    'organization_deleted_successfully' => [
        'label' => 'Organizzazione cancellata con successo',
        'placeholder' => 'Organizzazione cancellata con successo',
    ],
    'report_of' => [
        'label' => 'Resoconto di',
        'placeholder' => 'Resoconto di',
    ],
    'ticket_of' => [
        'label' => 'Ticket di',
        'placeholder' => 'Ticket di',
    ],
    /*
      |----------------------------------------------
      |  Ticket page
      |----------------------------------------------
     */
    'ticket_id' => [
        'label' => 'Ticket ID',
        'placeholder' => 'Ticket ID',
    ],
    'last_replier' => [
        'label' => 'Ultimo a rispondere',
        'placeholder' => 'Ultimo a rispondere',
    ],
    'assigned_to' => [
        'label' => 'Assegnato a',
        'placeholder' => 'Assegnato a',
    ],
    'last_activity' => [
        'label' => 'Ultima attività',
        'placeholder' => 'Ultima attività',
    ],
    'answered' => [
        'label' => 'Risposti',
        'placeholder' => 'Risposti',
    ],
    'assigned' => [
        'label' => 'Assegnati',
        'placeholder' => 'Assegnati',
    ],
    'create_ticket' => [
        'label' => 'Crea Ticket',
        'placeholder' => 'Crea Ticket',
    ],
    'tickets' => [
        'label' => 'Ticket',
        'placeholder' => 'Ticket',
    ],
    'Ticket_Information' => [
        'label' => 'INFORMAZIONE TICKET',
        'placeholder' => 'INFORMAZIONE TICKET',
    ],
    'Ticket_Id' => [
        'label' => 'ID TICKET',
        'placeholder' => 'ID TICKET',
    ],
    'User' => [
        'label' => 'UTENTE',
        'placeholder' => 'UTENTE',
    ],
    'Unassigned' => [
        'label' => 'NON ASSEGNATO',
        'placeholder' => 'NON ASSEGNATO',
    ],
    'unassigned-tickets' => [
        'label' => 'Ticket non assegnati',
        'placeholder' => 'Ticket non assegnati',
    ],
    'generate_pdf' => [
        'label' => 'Genera PDF',
        'placeholder' => 'Genera PDF',
    ],
    'change_status' => [
        'label' => 'Cambia stato',
        'placeholder' => 'Cambia stato',
    ],
    'more' => [
        'label' => 'Ancora',
        'placeholder' => 'Ancora',
    ],
    'delete_ticket' => [
        'label' => 'Cancella ticket',
        'placeholder' => 'Cancella ticket',
    ],
    'emergency' => [
        'label' => 'Emergenza',
        'placeholder' => 'Emergenza',
    ],
    'high' => [
        'label' => 'Alta',
        'placeholder' => 'Alta',
    ],
    'medium' => [
        'label' => 'Media',
        'placeholder' => 'Media',
    ],
    'low' => [
        'label' => 'Bassa',
        'placeholder' => 'Bassa',
    ],
    'sla_plan' => [
        'label' => 'Piano SLA',
        'placeholder' => 'Piano SLA',
    ],
    'created_date' => [
        'label' => 'Data creazione',
        'placeholder' => 'Data creazione',
    ],
    'due_date' => [
        'label' => 'Data di scadenza',
        'placeholder' => 'Data di scadenza',
    ],
    'last_response' => [
        'label' => 'Ultima risposta',
        'placeholder' => 'Ultima risposta',
    ],
    'source' => [
        'label' => 'Sorgente',
        'placeholder' => 'Sorgente',
    ],
    'last_message' => [
        'label' => 'Ultimo messaggio',
        'placeholder' => 'Ultimo messaggio',
    ],
    'reply' => [
        'label' => 'Risposta',
        'placeholder' => 'Risposta',
    ],
    'response' => [
        'label' => 'Risposta',
        'placeholder' => 'Risposta',
    ],
    'reply_content' => [
        'label' => 'Contenuto riposta',
        'placeholder' => 'Contenuto riposta',
    ],
    'attachment' => [
        'label' => 'Allegato',
        'placeholder' => 'Allegato',
    ],
    'internal_note' => [
        'label' => 'Nota interna',
        'placeholder' => 'Nota interna',
    ],
    'this_ticket_is_under_banned_user' => [
        'label' => 'Questo ticket è di un utente bannato',
        'placeholder' => 'Questo ticket è di un utente bannato',
    ],
    'ticket_source' => [
        'label' => 'Sorgente ticket',
        'placeholder' => 'Sorgente ticket',
    ],
    'are_you_sure_to_ban' => [
        'label' => 'Sicuro di voler bannare',
        'placeholder' => 'Sicuro di voler bannare',
    ],
    'whome_do_you_want_to_assign_ticket' => [
        'label' => 'A chi vuoi assegnare il ticket',
        'placeholder' => 'A chi vuoi assegnare il ticket',
    ],
    'are_you_sure_you_want_to_surrender_this_ticket' => [
        'label' => 'Sei sicuro di voler abbandonare questo ticket',
        'placeholder' => 'Sei sicuro di voler abbandonare questo ticket',
    ],
    'add_collaborator' => [
        'label' => 'Aggiungi collaboratore',
        'placeholder' => 'Aggiungi collaboratore',
    ],
    'search_existing_users' => [
        'label' => 'Cerca utenti esistenti',
        'placeholder' => 'Cerca utenti esistenti',
    ],
    'add_new_user' => [
        'label' => 'Aggiungi nuovo utente',
        'placeholder' => 'Aggiungi nuovo utente',
    ],
    'search_existing_users_or_add_new_users' => [
        'label' => 'Cerca utenti esistenti o aggiungi nuovi utenti',
        'placeholder' => 'Cerca utenti esistenti o aggiungi nuovi utenti',
    ],
    'search_by_email' => [
        'label' => 'Cerca per email',
        'placeholder' => 'Cerca per email',
    ],
    'list_of_collaborators_of_this_ticket' => [
        'label' => 'Lista dei collaboratori di questo Ticket',
        'placeholder' => 'Lista dei collaboratori di questo Ticket',
    ],
    'submit' => [
        'label' => 'Invia',
        'placeholder' => 'Invia',
    ],
    'max' => [
        'label' => 'Max',
        'placeholder' => 'Max',
    ],
    'add_cc' => [
        'label' => 'Aggiungi CC',
        'placeholder' => 'Aggiungi CC',
    ],
    'recepients' => [
        'label' => 'Destinatari',
        'placeholder' => 'Destinatari',
    ],
    'select_a_canned_response' => [
        'label' => 'Seleziona una risposta preimpostata',
        'placeholder' => 'Seleziona una risposta preimpostata',
    ],
    'assign_to' => [
        'label' => 'Assegna a',
        'placeholder' => 'Assegna a',
    ],
    'detail' => [
        'label' => 'Dettaglio',
        'placeholder' => 'Dettaglio',
    ],
    'user_details' => [
        'label' => 'Dettagli utente',
        'placeholder' => 'Dettagli utente',
    ],
    'ticket_option' => [
        'label' => 'Opzioni ticket',
        'placeholder' => 'Opzioni ticket',
    ],
    'ticket_detail' => [
        'label' => 'Dettagli ticket',
        'placeholder' => 'Dettagli ticket',
    ],
    'Assigned_To' => [
        'label' => 'ASSEGNATO A',
        'placeholder' => 'ASSEGNATO A',
    ],
    'locked-ticket' => [
        'label' => 'Attenzione! Questo ticket è etato bloccato da un altro utente ed è al momento in risposta.',
        'placeholder' => 'Attenzione! Questo ticket è etato bloccato da un altro utente ed è al momento in risposta.',
    ],
    'minutes-ago' => [
        'label' => 'minuti fa',
        'placeholder' => 'minuti fa',
    ],
    'access-ticket' => [
        'label' => 'Attenzione! Questo ticket è etato bloccato da te per ',
        'placeholder' => 'Attenzione! Questo ticket è etato bloccato da te per ',
    ],
    'minutes' => [
        'label' => 'Minuti',
        'placeholder' => 'Minuti',
    ],
    'in_minutes' => [
        'label' => 'In minuti',
        'placeholder' => 'In minuti',
    ],
    'add_another_owner' => [
        'label' => 'Aggiungi un altro possessore',
        'placeholder' => 'Aggiungi un altro possessore',
    ],
    'user-not-found' => [
        'label' => 'Utente non trovato, prova ancora o aggiungi un nuovo utente.',
        'placeholder' => 'Utente non trovato, prova ancora o aggiungi un nuovo utente.',
    ],
    'change-success' => [
        'label' => 'Successo! Il possessore di questo ticket è stato cambiato.',
        'placeholder' => 'Successo! Il possessore di questo ticket è stato cambiato.',
    ],
    'user-exists' => [
        'label' => 'Utente già esistente, prova a cercare questo stesso utente.',
        'placeholder' => 'Utente già esistente, prova a cercare questo stesso utente.',
    ],
    'valid-email' => [
        'label' => 'Inserisci un indirizzo email valido.',
        'placeholder' => 'Inserisci un indirizzo email valido.',
    ],
    'search_user' => [
        'label' => 'Cerca utente',
        'placeholder' => 'Cerca utente',
    ],
    'merge-ticket' => [
        'label' => 'Unisci ticket',
        'placeholder' => 'Unisci ticket',
    ],
    'merge' => [
        'label' => 'Unisci',
        'placeholder' => 'Unisci',
    ],
    'select_tickets' => [
        'label' => 'Seleziona ticket da unire',
        'placeholder' => 'Seleziona ticket da unire',
    ],
    'select-pparent-ticket' => [
        'label' => 'Seleziona un ticket padre',
        'placeholder' => 'Seleziona un ticket padre',
    ],
    'merge-reason' => [
        'label' => 'Motivazione unione',
        'placeholder' => 'Motivazione unione',
    ],
    'no-reason' => [
        'label' => 'Non è stata fornita alcuna ragione.',
        'placeholder' => 'Non è stata fornita alcuna ragione.',
    ],
    'get_merge_message' => [
        'label' => 'Questo ticket è stato unito con il ticket',
        'placeholder' => 'Questo ticket è stato unito con il ticket',
    ],
    'ticket_merged' => [
        'label' => ' è stato unito con il ticket.',
        'placeholder' => ' è stato unito con il ticket.',
    ],
    'no-tickets-to-merge' => [
        'label' => 'Non ci sono altri ticket di prorietà del possessore di questo ticket.',
        'placeholder' => 'Non ci sono altri ticket di prorietà del possessore di questo ticket.',
    ],
    'merge-error' => [
        'label' => 'Richiesta non processabile riprova in seguito.',
        'placeholder' => 'Richiesta non processabile riprova in seguito.',
    ],
    'merge-success' => [
        'label' => 'Ticket uniti con successo.',
        'placeholder' => 'Ticket uniti con successo.',
    ],
    'merge-error2' => [
        'label' => 'Per favore selzeziona un ticket da unire.',
        'placeholder' => 'Per favore selzeziona un ticket da unire.',
    ],
    'select-tickets-to merge' => [
        'label' => 'Seleziona due o più ticket da unire',
        'placeholder' => 'Seleziona due o più ticket da unire',
    ],
    'different-users' => [
        'label' => 'Ticket da utenti diversi',
        'placeholder' => 'Ticket da utenti diversi',
    ],
    'clean-up' => [
        'label' => 'Cancella definitivamente',
        'placeholder' => 'Cancella definitivamente',
    ],
    'hard-delete-success-message' => [
        'label' => 'I ticket sono stati cancellati permanentemente.',
        'placeholder' => 'I ticket sono stati cancellati permanentemente.',
    ],
    'overdue' => [
        'label' => 'In ritardo',
        'placeholder' => 'In ritardo',
    ],
    'overdue-tickets' => [
        'label' => 'Ticket in ritardo',
        'placeholder' => 'Ticket in ritardo',
    ],
    'change_owner_for_ticket' => [
        'label' => 'Cambia proprietario del ticket',
        'placeholder' => 'Cambia proprietario del ticket',
    ],

    /*
      |------------------------------------------------
      |Tools Page
      |------------------------------------------------
     */
    'canned_response' => [
        'label' => 'Risposta predefinita',
        'placeholder' => 'Risposta predefinita',
    ],
    'create_canned_response' => [
        'label' => 'Crea risposta predefinita',
        'placeholder' => 'Crea risposta predefinita',
    ],
    'surrender' => [
        'label' => 'Abbandona',
        'placeholder' => 'Abbandona',
    ],
    'added_successfully' => [
        'label' => 'Aggiunto correttamente',
        'placeholder' => 'Aggiunto correttamente',
    ],
    'updated_successfully' => [
        'label' => 'Aggiornato correttamente',
        'placeholder' => 'Aggiornato correttamente',
    ],
    'user_deleted_successfully' => [
        'label' => 'Utente cancellato con successo',
        'placeholder' => 'Utente cancellato con successo',
    ],
    'view' => [
        'label' => 'Visualizza',
        'placeholder' => 'Visualizza',
    ],
    /*
      |-----------------------------------------------
      | Main text
      |-----------------------------------------------
     */
    'copyright' => [
        'label' => 'Copyright',
        'placeholder' => 'Copyright',
    ],
    'all_rights_reserved' => [
        'label' => 'Tutti i diritti riservati',
        'placeholder' => 'Tutti i diritti riservati',
    ],
    'powered_by' => [
        'label' => 'Sviluppato da',
        'placeholder' => 'Sviluppato da',
    ],
    /*
      |------------------------------------------------
      |Guest-User Page
      |------------------------------------------------
     */
    'issue_summary' => [
        'label' => 'Sommario problema',
        'placeholder' => 'Sommario problema',
    ],
    'contact' => [
        'label' => 'Contatto',
        'placeholder' => 'Contatto',
    ],
    'issue_details' => [
        'label' => 'Dettagli problema',
        'placeholder' => 'Dettagli problema',
    ],
    'contact_informations' => [
        'label' => 'Informazioni contatto',
        'placeholder' => 'Informazioni contatto',
    ],
    'contact_details' => [
        'label' => 'Dettagli contatto',
        'placeholder' => 'Dettagli contatto',
    ],
    'role' => [
        'label' => 'Ruolo',
        'placeholder' => 'Ruolo',
    ],
    'ext' => [
        'label' => 'EXT',
        'placeholder' => 'EXT',
    ],
    'profile_pic' => [
        'label' => 'Foto profilo',
        'placeholder' => 'Foto profilo',
    ],
    'agent_sign' => [
        'label' => 'Firma agente',
        'placeholder' => 'Firma agente',
    ],
    'inactive' => [
        'label' => 'Inattivo',
        'placeholder' => 'Inattivo',
    ],
    'male' => [
        'label' => 'Uomo',
        'placeholder' => 'Uomo',
    ],
    'female' => [
        'label' => 'Donna',
        'placeholder' => 'Donna',
    ],
    'old_password' => [
        'label' => 'Vecchia password',
        'placeholder' => 'Vecchia password',
    ],
    'new_password' => [
        'label' => 'Nuova password',
        'placeholder' => 'Nuova password',
    ],
    'confirm_password' => [
        'label' => 'Conferma password',
        'placeholder' => 'Conferma password',
    ],
    'gender' => [
        'label' => 'Sesso',
        'placeholder' => 'Sesso',
    ],
    'ticket_number' => [
        'label' => 'Numero ticket',
        'placeholder' => 'Numero ticket',
    ],
    'content' => [
        'label' => 'Contenuto',
        'placeholder' => 'Contenuto',
    ],
    'edit_status' => [
        'label' => 'Modifica stato',
        'placeholder' => 'Modifica stato',
    ],
    'create_status' => [
        'label' => 'Crea stato',
        'placeholder' => 'Crea stato',
    ],
    'edit_details' => [
        'label' => 'Modifica dettagli',
        'placeholder' => 'Modifica dettagli',
    ],
    'edit_templates' => [
        'label' => 'Modifica modelli',
        'placeholder' => 'Modifica modelli',
    ],
    'activate_this_set' => [
        'label' => 'Attiva questo set',
        'placeholder' => 'Attiva questo set',
    ],
    'show' => [
        'label' => 'Mostra',
        'placeholder' => 'Mostra',
    ],
    'no_notification_available' => [
        'label' => 'Nessuna notifica disponibile',
        'placeholder' => 'Nessuna notifica disponibile',
    ],

    // auto-close workflow
    'close-msg1' => [
        'label' => 'Numero di giorni dopo i quali i ticket verranno chiusi automaticamente.',
        'placeholder' => 'Numero di giorni dopo i quali i ticket verranno chiusi automaticamente.',
    ],
    'no_of_days' => [
        'label' => 'Numero di giorni',
        'placeholder' => 'Numero di giorni',
    ],
    'close-msg2' => [
        'label' => 'Abilita auto chiusura workflow?',
        'placeholder' => 'Abilita auto chiusura workflow?',
    ],
    'enable_workflow' => [
        'label' => 'Abilita workflow',
        'placeholder' => 'Abilita workflow',
    ],
    'send_email_to_user' => [
        'label' => "Invia email all'utente",
        'placeholder' => "Invia email all'utente",
    ],
    'close-msg3' => [
        'label' => 'Seleziona stato da assegnare alla chiusura del ticket.',
        'placeholder' => 'Seleziona stato da assegnare alla chiusura del ticket.',
    ],
    'close-msg4' => [
        'label' => "Inviare email di auto chiusura del ticket all'utente?",
        'placeholder' => "Inviare email di auto chiusura del ticket all'utente?",
    ],
    'list_of_status' => [
        'label' => 'Lista degli stati',
        'placeholder' => 'Lista degli stati',
    ],
    'status_settings' => [
        'label' => 'Impostazioni stati',
        'placeholder' => 'Impostazioni stati',
    ],
    'icon_class' => [
        'label' => 'Icona',
        'placeholder' => 'Icona',
    ],
    'close_ticket_workflow' => [
        'label' => 'Chiudi workflow ticket',
        'placeholder' => 'Chiudi workflow ticket',
    ],
    'ratings_settings' => [
        'label' => 'Impostazioni valutazioni',
        'placeholder' => 'Impostazioni valutazioni',
    ],
    'notification' => [
        'label' => 'Notifica',
        'placeholder' => 'Notifica',
    ],
    'status_has_been_updated_successfully' => [
        'label' => 'Stato aggiornato correttamente',
        'placeholder' => 'Stato aggiornato correttamente',
    ],
    'status_has_been_created_successfully' => [
        'label' => 'Stato creato correttamente',
        'placeholder' => 'Stato creato correttamente',
    ],
    'status_has_been_deleted' => [
        'label' => 'Status cancellato',
        'placeholder' => 'Status cancellato',
    ],
    'you_cannot_delete_this_status' => [
        'label' => 'Non puoi cancellare questo stato',
        'placeholder' => 'Non puoi cancellare questo stato',
    ],
    'you_have_deleted_all_the_read_notifications' => [
        'label' => 'Hai cancellato tutte le notifiche lette',
        'placeholder' => 'Hai cancellato tutte le notifiche lette',
    ],
    'you_have_deleted_all_the_notification_records_since' => [
        'label' => 'Hai cancellato tutti i record di notifica dal ',
        'placeholder' => 'Hai cancellato tutti i record di notifica dal ',
    ],
    'ratings_updated_successfully' => [
        'label' => 'Valutazione aggiornate correttamente',
        'placeholder' => 'Valutazione aggiornate correttamente',
    ],
    'ratings_can_not_be_created' => [
        'label' => 'Impossibile creare la valutazione',
        'placeholder' => 'Impossibile creare la valutazione',
    ],
    'successfully_created_this_rating' => [
        'label' => 'Valutazione creata con successo',
        'placeholder' => 'Valutazione creata con successo',
    ],
    'rating_deleted_successfully' => [
        'label' => 'Valutazione cancellata correttamente',
        'placeholder' => 'Valutazione cancellata correttamente',
    ],
    // status msg

    'status_msg1' => [
        'label' => 'Se scegli SI verrà inviata una mail di notifica all\'utente.',
        'placeholder' => 'Se scegli SI verrà inviata una mail di notifica all\'utente.',
    ],
    'notify_user' => [
        'label' => "Notificare l'utente su questo stato?",
        'placeholder' => "Notificare l'utente su questo stato?",
    ],
    'deleted_status' => [
        'label' => 'Questo è lo stato di un ticket cancellato?',
        'placeholder' => 'Questo è lo stato di un ticket cancellato?',
    ],
    'resolved_status' => [
        'label' => 'Questo è lo stato di un ticket risolto?',
        'placeholder' => 'Questo è lo stato di un ticket risolto?',
    ],
    'status_msg3' => [
        'label' => 'Se scegli SI lo stato del ticket verrà impostato su risolto.',
        'placeholder' => 'Se scegli SI lo stato del ticket verrà impostato su risolto.',
    ],
    'status_msg2' => [
        'label' => 'Se scegli SI lo stato del ticket verrà impostato su cancellato.',
        'placeholder' => 'Se scegli SI lo stato del ticket verrà impostato su cancellato.',
    ],
    'rating-msg2' => [
        'label' => 'Seleziona un dipartimento per restringere la valutazione dei ticket o chat a questa appartenenza. Se non selezioni alcuna voce la valutazione apparirà su tutti i dipartimenti.',
        'placeholder' => 'Seleziona un dipartimento per restringere la valutazione dei ticket o chat a questa appartenenza. Se non selezioni alcuna voce la valutazione apparirà su tutti i dipartimenti.',
    ],
    'rating-msg3' => [
        'label' => 'Se scegli SI l\'utente potrà modificare la valutazione.',
        'placeholder' => 'Se scegli SI l\'utente potrà modificare la valutazione.',
    ],
    'rating_restrict' => [
        'label' => 'Restringi valutazione ad un dipartimento',
        'placeholder' => 'Restringi valutazione ad un dipartimento',
    ],
    'rating_change' => [
        'label' => 'Consenti agli utenti di cambiare la valutazione?',
        'placeholder' => 'Consenti agli utenti di cambiare la valutazione?',
    ],
    'security_msg1' => [
        'label' => 'Messaggio da mostrare quando un utente è stato bloccato.',
        'placeholder' => 'Messaggio da mostrare quando un utente è stato bloccato.',
    ],
    'security_msg2' => [
        'label' => 'Numero massimo di login errati che un utente può effettuare prima che venga bloccato fuori dal sistema. Imposta 0 per registrare i login errati senza bloccare l\'utente.',
        'placeholder' => 'Numero massimo di login errati che un utente può effettuare prima che venga bloccato fuori dal sistema. Imposta 0 per registrare i login errati senza bloccare l\'utente.',
    ],
    'security_msg3' => [
        'label' => 'Numero di minuti per i quali l\'utente verrà bannato dal sistema dopo aver raggiunto il limite di login errati.',
        'placeholder' => 'Numero di minuti per i quali l\'utente verrà bannato dal sistema dopo aver raggiunto il limite di login errati.',
    ],
    'max_attempt' => [
        'label' => 'Numero tentativi di login per utente/host',
        'placeholder' => 'Numero tentativi di login per utente/host',
    ],
    'rating-msg1' => [
        'label' => 'Valutazione massima che può essere fornita. Per esempio, se selezionato 5, il minimo possibile sarà 1 ed il massimo 5.',
        'placeholder' => 'Valutazione massima che può essere fornita. Per esempio, se selezionato 5, il minimo possibile sarà 1 ed il massimo 5.',
    ],
    'enter_no_of_days' => [
        'label' => 'Inserisci il numero di giorno',
        'placeholder' => 'Inserisci il numero di giorno',
    ],
    'template-types' => [
        'label' => 'Tipi di modelli',
        'placeholder' => 'Tipi di modelli',
    ],
    'close-workflow' => [
        'label' => 'Chiusura workflow ticket',
        'placeholder' => 'Chiusura workflow ticket',
    ],
    'template' => [
        'label' => 'Modello',
        'placeholder' => 'Modello',
    ],
    'rating_label' => [
        'label' => 'Etichetta valutazione',
        'placeholder' => 'Etichetta valutazione',
    ],
    'display_order' => [
        'label' => 'Ordinamento',
        'placeholder' => 'Ordinamento',
    ],
    'rating_scale' => [
        'label' => 'Scala di valutazione',
        'placeholder' => 'Scala di valutazione',
    ],
    'rating_area' => [
        'label' => 'Area di valutazione',
        'placeholder' => 'Area di valutazione',
    ],
    'modify' => [
        'label' => 'Modifica',
        'placeholder' => 'Modifica',
    ],
    'rating_name' => [
        'label' => 'Nome valutazione',
        'placeholder' => 'Nome valutazione',
    ],
    'add_user_to_this_organization' => [
        'label' => 'Aggiungi utente a questa organizzazione',
        'placeholder' => 'Aggiungi utente a questa organizzazione',
    ],
    'Tickets_of' => [
        'label' => 'Ticket di',
        'placeholder' => 'Ticket di',
    ],
    'security' => [
        'label' => 'Sicurezza',
        'placeholder' => 'Sicurezza',
    ],
    'security_settings' => [
        'label' => 'Impostazioni di sicurezza',
        'placeholder' => 'Impostazioni di sicurezza',
    ],
    'lockouts' => [
        'label' => 'Tentativi',
        'placeholder' => 'Tentativi',
    ],
    'security_settings_saved_successfully' => [
        'label' => 'Impostazioni di sicurezza salvate correttamente',
        'placeholder' => 'Impostazioni di sicurezza salvate correttamente',
    ],
    'manage_status' => [
        'label' => 'Gestisci stato',
        'placeholder' => 'Gestisci stato',
    ],
    'notifications' => [
        'label' => 'Notifiche',
        'placeholder' => 'Notifiche',
    ],
    'auto_close_workflow' => [
        'label' => 'Auto chiusura workflow',
        'placeholder' => 'Auto chiusura workflow',
    ],
    'close_ticket_workflow_settings' => [
        'label' => 'Impostazioni chiusura workflow ticket',
        'placeholder' => 'Impostazioni chiusura workflow ticket',
    ],
    'successfully_saved_your_settings' => [
        'label' => 'Impostazioni salvate correttamente',
        'placeholder' => 'Impostazioni salvate correttamente',
    ],

    /*
      |------------------------------------------------
      |   Notification Settings Pages
      |------------------------------------------------
     */
    'notification_settings' => [
        'label' => 'Impostazioni notifiche',
        'placeholder' => 'Impostazioni notifiche',
    ],
    'delete_noti' => [
        'label' => 'Cancellare tutte le notifiche lette?',
        'placeholder' => 'Cancellare tutte le notifiche lette?',
    ],
    'noti_msg1' => [
        'label' => 'Numero giorni di notifiche da cancellare',
        'placeholder' => 'Numero giorni di notifiche da cancellare',
    ],
    'noti_msg2' => [
        'label' => 'Puoi inserire il numero di giorni di log del database da eliminare e la cronologia delle notifiche verrà eliminata dal giorno specificato.',
        'placeholder' => 'Puoi inserire il numero di giorni di log del database da eliminare e la cronologia delle notifiche verrà eliminata dal giorno specificato.',
    ],
    'del_all_read' => [
        'label' => 'Cancella',
        'placeholder' => 'Cancella',
    ],
    'You_have_deleted_all_the_read_notifications' => [
        'label' => 'Hai cancellato tutte le notifiche lette',
        'placeholder' => 'Hai cancellato tutte le notifiche lette',
    ],
    'view_all_notifications' => [
        'label' => 'Visualizza tutte le notifiche',
        'placeholder' => 'Visualizza tutte le notifiche',
    ],
    /*
      |------------------------------------------------
      |   Error Pages
      |------------------------------------------------
     */
    'not_found' => [
        'label' => 'Non trovato',
        'placeholder' => 'Non trovato',
    ],
    'oops_page_not_found' => [
        'label' => 'Oops! Pagina non trovata',
        'placeholder' => 'Oops! Pagina non trovata',
    ],
    'we_could_not_find_the_page_you_were_looking_for' => [
        'label' => 'Impossibile trovare la pagina che stavi cercando',
        'placeholder' => 'Impossibile trovare la pagina che stavi cercando',
    ],
    'internal_server_error' => [
        'label' => 'Errore interno del server',
        'placeholder' => 'Errore interno del server',
    ],
    'be_right_back' => [
        'label' => 'Torna indietro',
        'placeholder' => 'Torna indietro',
    ],
    'sorry' => [
        'label' => 'Spiacente',
        'placeholder' => 'Spiacente',
    ],
    'we_are_working_on_it' => [
        'label' => 'Ci stiamo lavorando',
        'placeholder' => 'Ci stiamo lavorando',
    ],
    'category' => [
        'label' => 'Categoria',
        'placeholder' => 'Categoria',
    ],
    'addcategory' => [
        'label' => 'Aggiungi categoria',
        'placeholder' => 'Aggiungi categoria',
    ],
    'allcategory' => [
        'label' => 'Tutte le categorie',
        'placeholder' => 'Tutte le categorie',
    ],
    'article' => [
        'label' => 'Articolo',
        'placeholder' => 'Articolo',
    ],
    'articles' => [
        'label' => 'Articoli',
        'placeholder' => 'Articoli',
    ],
    'addarticle' => [
        'label' => 'Aggiungi articolo',
        'placeholder' => 'Aggiungi articolo',
    ],
    'allarticle' => [
        'label' => 'Tutti gli articoli',
        'placeholder' => 'Tutti gli articoli',
    ],
    'pages' => [
        'label' => 'Pagine',
        'placeholder' => 'Pagine',
    ],
    'addpages' => [
        'label' => 'Aggiungi pagine',
        'placeholder' => 'Aggiungi pagine',
    ],
    'allpages' => [
        'label' => 'Tutte le pagine',
        'placeholder' => 'Tutte le pagine',
    ],
    'widgets' => [
        'label' => 'Widgets',
        'placeholder' => 'Widgets',
    ],
    'widget-settings' => [
        'label' => 'Impostazioni widget',
        'placeholder' => 'Impostazioni widget',
    ],
    'sidewidget1' => [
        'label' => 'Widget laterale 1',
        'placeholder' => 'Widget laterale 1',
    ],
    'sidewidget2' => [
        'label' => 'Widget laterale 2',
        'placeholder' => 'Widget laterale 2',
    ],
    'comments' => [
        'label' => 'Commenti',
        'placeholder' => 'Commenti',
    ],
    'comments-list' => [
        'label' => 'Lista commenti',
        'placeholder' => 'Lista commenti',
    ],
    'settings' => [
        'label' => 'Impostazioni',
        'placeholder' => 'Impostazioni',
    ],
    'parent' => [
        'label' => 'Parente',
        'placeholder' => 'Parente',
    ],
    'description' => [
        'label' => 'Descrizione',
        'placeholder' => 'Descrizione',
    ],
    'enter_the_description' => [
        'label' => 'Inserisci la descrizione',
        'placeholder' => 'Inserisci la descrizione',
    ],
    'publish' => [
        'label' => 'Pubblica',
        'placeholder' => 'Pubblica',
    ],
    'publish_immediately' => [
        'label' => 'Pubblica immediatamente',
        'placeholder' => 'Pubblica immediatamente',
    ],
    'published' => [
        'label' => 'Pubblicato',
        'placeholder' => 'Pubblicato',
    ],
    'draft' => [
        'label' => 'Bozza',
        'placeholder' => 'Bozza',
    ],
    'create_a_category' => [
        'label' => 'Crea una categoria',
        'placeholder' => 'Crea una categoria',
    ],
    'add' => [
        'label' => 'Aggiungi',
        'placeholder' => 'Aggiungi',
    ],
    'social' => [
        'label' => 'Social',
        'placeholder' => 'Social',
    ],
    'social-widget-settings' => [
        'label' => 'Impostazioni widget sociali',
        'placeholder' => 'Impostazioni widget sociali',
    ],
    'comment' => [
        'label' => 'Commento',
        'placeholder' => 'Commento',
    ],
    'not_published' => [
        'label' => 'Non pubblicato',
        'placeholder' => 'Non pubblicato',
    ],
    'numberofelementstodisplay' => [
        'label' => 'Numero elementi da visualizzare',
        'placeholder' => 'Numero elementi da visualizzare',
    ],
    // ======================================
    'slug' => [
        'label' => 'Slug',
        'placeholder' => 'Slug',
    ],
    'read_more' => [
        'label' => 'Continua a leggere',
        'placeholder' => 'Continua a leggere',
    ],
    'view_all' => [
        'label' => 'Vedi tutto',
        'placeholder' => 'Vedi tutto',
    ],
    'categories' => [
        'label' => 'Categorie',
        'placeholder' => 'Categorie',
    ],
    'need_more_support' => [
        'label' => 'Necessita più supporto',
        'placeholder' => 'Necessita più supporto',
    ],
    'if_you_did_not_find_an_answer_please_raise_a_ticket_describing_the_issue' => [
        'label' => 'Se non hai trovato una risposta, per favore apri un ticket con la descrizione del problema',
        'placeholder' => 'Se non hai trovato una risposta, per favore apri un ticket con la descrizione del problema',
    ],
    'have_a_question?_type_your_search_term_here' => [
        'label' => 'Hai una domanda? Cerca qui un argomento...',
        'placeholder' => 'Hai una domanda? Cerca qui un argomento...',
    ],
    'search' => [
        'label' => 'Cerca',
        'placeholder' => 'Cerca',
    ],
    'frequently_asked_questions' => [
        'label' => 'Domande frequenti',
        'placeholder' => 'Domande frequenti',
    ],
    'leave_a_reply' => [
        'label' => 'Lascia una risposta',
        'placeholder' => 'Lascia una risposta',
    ],
    'post_message' => [
        'label' => 'Messaggio post',
        'placeholder' => 'Messaggio post',
    ],
    /*
      |--------------------------------------------------------------------------------------
      |  Client Panel [English(en)]
      |--------------------------------------------------------------------------------------
      | The following language lines are used in all Agent Panel related issues to translate
      | some words in view to English. You are free to change them to anything you want to
      | customize your views to better match your application.
      |
     */
    'home' => [
        'label' => 'Home',
        'placeholder' => 'Home',
    ],
    'submit_a_ticket' => [
        'label' => 'Invia un Ticket',
        'placeholder' => 'Invia un Ticket',
    ],
    'my_profile' => [
        'label' => 'Mio profilo',
        'placeholder' => 'Mio profilo',
    ],
    'log_out' => [
        'label' => 'Logout',
        'placeholder' => 'Logout',
    ],
    'forgot_password' => [
        'label' => 'Password dimenticata',
        'placeholder' => 'Password dimenticata',
    ],
    'create_account' => [
        'label' => 'Crea un account',
        'placeholder' => 'Crea un account',
    ],
    'you_are_here' => [
        'label' => 'Sei qui',
        'placeholder' => 'Sei qui',
    ],
    'have_a_ticket' => [
        'label' => 'Hai un ticket',
        'placeholder' => 'Hai un ticket',
    ],
    'check_ticket_status' => [
        'label' => 'Controlla stato ticket',
        'placeholder' => 'Controlla stato ticket',
    ],
    'choose_a_help_topic' => [
        'label' => 'Scegli un area di supporto',
        'placeholder' => 'Scegli un area di supporto',
    ],
    'ticket_status' => [
        'label' => 'Stato del ticket',
        'placeholder' => 'Stato del ticket',
    ],
    'post_comment' => [
        'label' => 'Pubblica un commento',
        'placeholder' => 'Pubblica un commento',
    ],
    'plugin' => [
        'label' => 'Plugin',
        'placeholder' => 'Plugin',
    ],
    'edit_profile' => [
        'label' => 'Modifica profilo',
        'placeholder' => 'Modifica profilo',
    ],
    'Send' => [
        'label' => 'INVIA',
        'placeholder' => 'INVIA',
    ],
    'no_article' => [
        'label' => 'Nessun articolo',
        'placeholder' => 'Nessun articolo',
    ],
    'profile_settings' => [
        'label' => 'Impostazioni profilo',
        'placeholder' => 'Impostazioni profilo',
    ],
    'please_fill_all_required_feilds' => [
        'label' => 'Per favore compila tutti i campi richiesti.',
        'placeholder' => 'Per favore compila tutti i campi richiesti.',
    ],
    'successfully_replied' => [
        'label' => 'Replicato con successo',
        'placeholder' => 'Replicato con successo',
    ],
    'please_fill_some_data' => [
        'label' => 'Per favore inserisci qualche dato!',
        'placeholder' => 'Per favore inserisci qualche dato!',
    ],
    'there_is_no_such_ticket_number' => [
        'label' => 'Numero ticket non trovato',
        'placeholder' => 'Numero ticket non trovato',
    ],
    "email_didn't_match_with_ticket_number" => [
        'label' => 'Email non corrispondente al numero di ticket',
        'placeholder' => 'Email non corrispondente al numero di ticket',
    ],
    'we_have_sent_you_a_link_by_email_please_click_on_that_link_to_view_ticket' => [
        'label' => 'Ti abbiamo inviato un link per mail, per favore cliccaci per visualizzare il ticket',
        'placeholder' => 'Ti abbiamo inviato un link per mail, per favore cliccaci per visualizzare il ticket',
    ],
    'no_records_on_publish_time' => [
        'label' => 'Nessun data nella data indicata',
        'placeholder' => 'Nessun data nella data indicata',
    ],
    'your_details_send_to_system' => [
        'label' => 'I tuoi dettagli sono stati inviati al sistema',
        'placeholder' => 'I tuoi dettagli sono stati inviati al sistema',
    ],
    'your_details_can_not_send_to_system' => [
        'label' => 'I tuoi dettagli non sono stati inviati al sistema',
        'placeholder' => 'I tuoi dettagli non sono stati inviati al sistema',
    ],
    'your_comment_posted' => [
        'label' => 'Commento pubblicato',
        'placeholder' => 'Commento pubblicato',
    ],
    'sorry_not_processed' => [
        'label' => 'Non processato',
        'placeholder' => 'Non processato',
    ],
    'password_was_not_updated' => [
        'label' => 'Password non aggiornata',
        'placeholder' => 'Password non aggiornata',
    ],
    'sorry_your_ticket_token_has_expired_please_try_to_resend_the_ticket_link_request' => [
        'label' => 'Token del ticket scaduto! Per favore prova ad inviare nuovamente il link di richiesta ticket',
        'placeholder' => 'Token del ticket scaduto! Per favore prova ad inviare nuovamente il link di richiesta ticket',
    ],
    'sorry_you_are_not_allowed_token_expired' => [
        'label' => 'Non sei autorizzato. Token scaduto!',
        'placeholder' => 'Non sei autorizzato. Token scaduto!',
    ],
    'thank_you_for_your_rating' => [
        'label' => 'Grazie per la valutazione!',
        'placeholder' => 'Grazie per la valutazione!',
    ],
    'your_ticket_has_been' => [
        'label' => 'Il tuo ticket è stato ',
        'placeholder' => 'Il tuo ticket è stato ',
    ],
    'failed_to_send_email_contact_administrator' => [
        'label' => "Invio mail fallito, per favore contatta l'amministratore",
        'placeholder' => "Invio mail fallito, per favore contatta l'amministratore",
    ],
    /*
     * |---------------------------------------------------------------------------------------
     * |API settings
     * |----------------------------------------------------------------------------------
     * |The following lanuage line used to get english traslation of api settings in admin panel
     * |
     * |
     */
    'webhooks' => [
        'label' => 'Webhooks',
        'placeholder' => 'Webhooks',
    ],
    'enter_url_to_send_ticket_details' => [
        'label' => "Inserisci l'URL per inviare i dettagli del ticket",
        'placeholder' => "Inserisci l'URL per inviare i dettagli del ticket",
    ],
    'api_settings' => [
        'label' => 'Impostazioni API',
        'placeholder' => 'Impostazioni API',
    ],
    /*
     * -----------------------------------------------------------------------------
     * Error log and debugging settings
     * --------------------------------------------------------------------------
     *
     */
    'error-debug' => [
        'label' => 'Log errori e debug',
        'placeholder' => 'Log errori e debug',
    ],
    'debug-options' => [
        'label' => 'Opzioni di debug',
        'placeholder' => 'Opzioni di debug',
    ],
    'view-logs' => [
        'label' => 'Visualizza log errori',
        'placeholder' => 'Visualizza log errori',
    ],
    'not-authorised-error-debug' => [
        'label' => "Non sei autorizzato ad accedere all'URL",
        'placeholder' => "Non sei autorizzato ad accedere all'URL",
    ],
    'error-debug-settings' => [
        'label' => 'Impostazioni errori e debug',
        'placeholder' => 'Impostazioni errori e debug',
    ],
    'debugging' => [
        'label' => 'Modalità debug',
        'placeholder' => 'Modalità debug',
    ],
    'bugsnag-debugging' => [
        'label' => 'Invia rapporti di crash per aiutare Ladybird a migliorare Faveo',
        'placeholder' => 'Invia rapporti di crash per aiutare Ladybird a migliorare Faveo',
    ],
    'error-debug-settings-saved-message' => [
        'label' => 'Le tue impostazioni sono state salvate correttamente',
        'placeholder' => 'Le tue impostazioni sono state salvate correttamente',
    ],
    'error-debug-settings-error-message' => [
        'label' => 'Non hai effettuato modifiche nelle impostazioni.',
        'placeholder' => 'Non hai effettuato modifiche nelle impostazioni.',
    ],
    'error-logs' => [
        'label' => 'Log errori',
        'placeholder' => 'Log errori',
    ],
    /* ---------------------------------------------------------------------------------------
     * Latest update 16-06-2016
     * -----------------------------------------------------------------------------------
     */
    'that_email_is not_available_in_this_system' => [
        'label' => 'Quella mail non è disponibile nel sistema',
        'placeholder' => 'Quella mail non è disponibile nel sistema',
    ],
    'use_subject' => [
        'label' => 'Usa soggetto',
        'placeholder' => 'Usa soggetto',
    ],
    'reopen' => [
        'label' => 'riaperti',
        'placeholder' => 'riaperti',
    ],
    'invalid_attempt' => [
        'label' => 'Tentativo non valido',
        'placeholder' => 'Tentativo non valido',
    ],
    /* ---------------------------------------------------------------------------------------
     * Latest update 27-07-2016
     * -----------------------------------------------------------------------------------
     */
    'queue' => [
        'label' => 'Coda',
        'placeholder' => 'Coda',
    ],
    'queues' => [
        'label' => 'Code',
        'placeholder' => 'Code',
    ],
    /*     * -------------------------------------------------------------------------------------------------
     * OTP  messages body to send to user while registering, resetting passwords
     * --------------------------------------------------------------------------------------------------
     */
    'hello' => [
        'label' => 'Ciao',
        'placeholder' => 'Ciao',
    ],
    'reset-link-msg' => ",\r\nQuesto è il link per resettare la password.\r\n",
    'otp-for-your' => ",\r\nOTP per il tuo",
    'account-verification-is' => [
        'label' => 'verifica account è',
        'placeholder' => 'verifica account è',
    ],
    'extra-text' => ".\r\nPuoi collegarti per verificare il tuo via OTP o semplicamente cliccare sul link che ti abbiamo spedito per email.",
    'otp-not-sent' => [
        'label' => 'Stiamo riscontrando qualche problema nell\'invio del codice OTP, per favore riprova più tardi.',
        'placeholder' => 'Stiamo riscontrando qualche problema nell\'invio del codice OTP, per favore riprova più tardi.',
    ],
    /*     * -------------------------------------------------------------------------------------------
     * Ticket number settings 03-08-2016
     * ------------------------------------------------------------------------------------------
     */
    'format' => [
        'label' => 'Formato',
        'placeholder' => 'Formato',
    ],
    'ticket-number-format' => [
        'label' => 'Questa opzione è usata per generare il numero dei ticket. Usa il simbolo cancelletto (`#`) per usare numeri ed il simbolo del dollaro (‘$’) per usare lettere. Tutti gli altri caratteri saranno mantenuti inalterati. ',
        'placeholder' => 'Questa opzione è usata per generare il numero dei ticket. Usa il simbolo cancelletto (`#`) per usare numeri ed il simbolo del dollaro (‘$’) per usare lettere. Tutti gli altri caratteri saranno mantenuti inalterati. ',
    ],
    'ticket-number-type' => [
        'label' => 'Scegli la sequenza dalla quale derivare il numero dei ticket. Il sistema usa una sequenza incrementale e casuale di default',
        'placeholder' => 'Scegli la sequenza dalla quale derivare il numero dei ticket. Il sistema usa una sequenza incrementale e casuale di default',
    ],
    /*     * ----------------------------------------------------------------------------------------------------
     * Social media integration
     * ---------------------------------------------------------------------------------------------------------
     */
    'client_id' => [
        'label' => 'Client ID',
        'placeholder' => 'Client ID',
    ],
    'client_secret' => [
        'label' => 'Client segreto',
        'placeholder' => 'Client segreto',
    ],
    'redirect' => [
        'label' => 'URL di reindirizzamento',
        'placeholder' => 'URL di reindirizzamento',
    ],
    'details' => [
        'label' => 'Dettagli',
        'placeholder' => 'Dettagli',
    ],
    'social-media' => [
        'label' => 'Social media',
        'placeholder' => 'Social media',
    ],
    /*     * ----------------------------------------------------------------------------------------------
     * Report
     * ----------------------------------------------------------------------------------------------
     */
    'Report' => [
        'label' => 'RESOCONTO',
        'placeholder' => 'RESOCONTO',
    ],
    'select' => [
        'label' => 'Seleziona',
        'placeholder' => 'Seleziona',
    ],
    'generate' => [
        'label' => 'Genera',
        'placeholder' => 'Genera',
    ],
    'day' => [
        'label' => 'Giorno',
        'placeholder' => 'Giorno',
    ],
    'week' => [
        'label' => 'Settimana',
        'placeholder' => 'Settimana',
    ],
    'month' => [
        'label' => 'Mese',
        'placeholder' => 'Mese',
    ],
    'Currnet_In_Progress' => [
        'label' => 'IN CORSO',
        'placeholder' => 'IN CORSO',
    ],
    'Total_Created' => [
        'label' => 'TOTALE CREATI',
        'placeholder' => 'TOTALE CREATI',
    ],
    'Total_Reopened' => [
        'label' => 'TOTALE RIAPERTI',
        'placeholder' => 'TOTALE RIAPERTI',
    ],
    'Total_Closed' => [
        'label' => 'TOTALE CHIUSI',
        'placeholder' => 'TOTALE CHIUSI',
    ],
    'tab' => [
        'comments' => [
            'icon' => 'bi bi-chat',
            'label' => 'Commenti',
            'placeholder' => 'Commenti',
        ],
    ],
    'tabular' => [
        'label' => 'Tabulato',
        'placeholder' => 'Tabulato',
    ],
    'reopened' => [
        'label' => 'Riaperti',
        'placeholder' => 'Riaperti',
    ],
    /* ---------------------------------------------------------------------------------------
     * Ticket Priority
     * -----------------------------------------------------------------------------------
     */
    'ticket_priority' => [
        'label' => 'Priorità ticket',
        'placeholder' => 'Priorità ticket',
    ],
    'priority_desc' => [
        'label' => 'Descrizione',
        'placeholder' => 'Descrizione',
    ],
    'priority_urgency' => [
        'label' => 'Urgenza priorità',
        'placeholder' => 'Urgenza priorità',
    ],
    'priority_id' => [
        'label' => 'ID priorità',
        'placeholder' => 'ID priorità',
    ],
    'priority_color' => [
        'label' => 'Colore',
        'placeholder' => 'Colore',
    ],
    'ispublic' => [
        'label' => "E' pubblica",
        'placeholder' => "E' pubblica",
    ],
    'is_default' => [
        'label' => 'Di default',
        'placeholder' => 'Di default',
    ],
    'create_ticket_priority' => [
        'label' => 'Crea priorità ticket',
        'placeholder' => 'Crea priorità ticket',
    ],
    'agent_notes' => [
        'label' => 'Note agente',
        'placeholder' => 'Note agente',
    ],
    'select_priority' => [
        'label' => 'Seleziona priorità',
        'placeholder' => 'Seleziona priorità',
    ],
    'normal' => [
        'label' => 'Normale',
        'placeholder' => 'Normale',
    ],
    'make-default-priority' => [
        'label' => 'Rendi priorità predefinita',
        'placeholder' => 'Rendi priorità predefinita',
    ],
    'priority_successfully_created' => [
        'label' => 'Priorità creata con successo',
        'placeholder' => 'Priorità creata con successo',
    ],
    'priority_successfully_updated' => [
        'label' => 'Priorità aggiornata con successo',
        'placeholder' => 'Priorità aggiornata con successo',
    ],
    'delete_successfully' => [
        'label' => 'Cancellata correttamente',
        'placeholder' => 'Cancellata correttamente',
    ],
    'user_priority_status' => [
        'label' => 'Stato priorità utente',
        'placeholder' => 'Stato priorità utente',
    ],
    'current' => [
        'label' => 'Corrente:',
        'placeholder' => 'Corrente:',
    ],
    'active_user_can_select_the_priority_while_creating_ticket' => [
        'label' => 'Gli utenti attivi possono seleziona la priorità durante la creazione del ticket',
        'placeholder' => 'Gli utenti attivi possono seleziona la priorità durante la creazione del ticket',
    ],

    /* --------------------------------------------------------------------------------------------
     * Approval Updated
     * --------------------------------------------------------------------------------------------
     */
    'approval' => [
        'label' => 'Approvati',
        'placeholder' => 'Approvati',
    ],
    'approval_tickets' => [
        'label' => 'Ticket approvati',
        'placeholder' => 'Ticket approvati',
    ],
    'approve' => [
        'label' => 'Approva',
        'placeholder' => 'Approva',
    ],
    'approval_request' => [
        'label' => 'Richiesta approvazione',
        'placeholder' => 'Richiesta approvazione',
    ],
    'approvel_ticket_list' => [
        'label' => 'Lista ticket approvati',
        'placeholder' => 'Lista ticket approvati',
    ],
    'approval_settings' => [
        'label' => 'Impostazioni approvazione',
        'placeholder' => 'Impostazioni approvazione',
    ],
    'close_all_ticket_for_approval' => [
        'label' => 'Chiudi tutti i ticket approvati',
        'placeholder' => 'Chiudi tutti i ticket approvati',
    ],
    'approval_settings-created-successfully' => [
        'label' => 'Impostazioni di approvazione create correttamente',
        'placeholder' => 'Impostazioni di approvazione create correttamente',
    ],

    /* --------------------------------------------------------------------------------------------
     * Followup Updated
     * --------------------------------------------------------------------------------------------
     */
    'followup' => [
        'label' => 'Followup',
        'placeholder' => 'Followup',
    ],
    'followup_tickets' => [
        'label' => 'Ticket di followup',
        'placeholder' => 'Ticket di followup',
    ],
    'followup_Notification' => [
        'label' => 'Notifiche followup',
        'placeholder' => 'Notifiche followup',
    ],

    /*
      *--------------------------------------------------------------------------------------------
      *Updated 6-9-2016
      *---------------------------------------------------------------------------------------
      */
    'not-available' => [
        'label' => 'Non disponibile',
        'placeholder' => 'Non disponibile',
    ],
    /* --------------------------------------------------------------------------------------------
     * User Module
     * --------------------------------------------------------------------------------------------
     */
    'agent_report' => [
        'label' => 'Resoconto agente',
        'placeholder' => 'Resoconto agente',
    ],
    'assign_tickets' => [
        'label' => 'Ticket assegnati',
        'placeholder' => 'Ticket assegnati',
    ],
    'delete_agent' => [
        'label' => 'Cancella agente',
        'placeholder' => 'Cancella agente',
    ],
    'delete_user' => [
        'label' => 'Cancella utente',
        'placeholder' => 'Cancella utente',
    ],
    'confirm_deletion' => [
        'label' => 'Conferma cancellazione',
        'placeholder' => 'Conferma cancellazione',
    ],
    'delete_all_content' => [
        'label' => 'Cancella tutti i contenuti',
        'placeholder' => 'Cancella tutti i contenuti',
    ],
    'agent_profile' => [
        'label' => 'Profilo agente',
        'placeholder' => 'Profilo agente',
    ],
    'change_role_to_admin' => [
        'label' => 'Cambia ruolo in admin',
        'placeholder' => 'Cambia ruolo in admin',
    ],
    'change_role_to_user' => [
        'label' => 'Cambia ruolo in utente',
        'placeholder' => 'Cambia ruolo in utente',
    ],
    'change_role_to_agent' => [
        'label' => 'Cambia ruolo in agente',
        'placeholder' => 'Cambia ruolo in agente',
    ],
    'role_change' => [
        'label' => 'Cambio ruolo',
        'placeholder' => 'Cambio ruolo',
    ],
    'password_generator' => [
        'label' => 'Genera password',
        'placeholder' => 'Genera password',
    ],
    'depertment' => [
        'label' => 'Dipartimento',
        'placeholder' => 'Dipartimento',
    ],
    'duetoday' => [
        'label' => 'In ritardo oggi',
        'placeholder' => 'In ritardo oggi',
    ],
    'today-due_tickets' => [
        'label' => 'In scadenza oggi',
        'placeholder' => 'In scadenza oggi',
    ],
    'password_change_successfully' => [
        'label' => 'Password cambiata con successo',
        'placeholder' => 'Password cambiata con successo',
    ],
    'role_change_successfully' => [
        'label' => 'Ruolo modificato con successo',
        'placeholder' => 'Ruolo modificato con successo',
    ],
    'user_delete_successfully' => [
        'label' => 'Utente cancellato correttamente',
        'placeholder' => 'Utente cancellato correttamente',
    ],
    'agent_delete_successfully' => [
        'label' => 'Agente cancellato correttamente',
        'placeholder' => 'Agente cancellato correttamente',
    ],
    'select_another_agent' => [
        'label' => 'Seleziona un altro agente',
        'placeholder' => 'Seleziona un altro agente',
    ],
    'agent_delete_successfully_and_ticket_assign_to_another_agent' => [
        'label' => 'Agente cancellato e ticket assegnato ad altro agente',
        'placeholder' => 'Agente cancellato e ticket assegnato ad altro agente',
    ],
    'deleted_user' => [
        'label' => 'Utente cancellato',
        'placeholder' => 'Utente cancellato',
    ],
    'deleted_user_directory' => [
        'label' => 'Cartella utente cancellata',
        'placeholder' => 'Cartella utente cancellata',
    ],
    'restore' => [
        'label' => 'Ripristina',
        'placeholder' => 'Ripristina',
    ],
    'user_restore_successfully' => [
        'label' => 'Utente ripristinato correttamente',
        'placeholder' => 'Utente ripristinato correttamente',
    ],

    /*** updates 28-11-2016***/
    'apply' => [
        'label' => 'Applica',
        'placeholder' => 'Applica',
    ],

    /* updates 2-12-2016 * */
    'sort-by' => [
        'label' => 'Ordina per',
        'placeholder' => 'Ordina per',
    ],
    'created-at' => [
        'label' => 'Creato a',
        'placeholder' => 'Creato a',
    ],
    'or' => [
        'label' => 'o',
        'placeholder' => 'o',
    ],
    'activate' => [
        'label' => 'Attiva',
        'placeholder' => 'Attiva',
    ],
    'assign-ticket' => [
        'label' => 'Assegna ticket',
        'placeholder' => 'Assegna ticket',
    ],
    'can-not-inactive-group' => [
        'label' => 'Impossibile disattivare il gruppo con agenti assegnati. Per favore riassegna gli agenti ad un altro gruppo e riprova.',
        'placeholder' => 'Impossibile disattivare il gruppo con agenti assegnati. Per favore riassegna gli agenti ad un altro gruppo e riprova.',
    ],
    'internal-note-has-been-added' => [
        'label' => 'Note interne aggiunte al ticket',
        'placeholder' => 'Note interne aggiunte al ticket',
    ],
    'active-users' => [
        'label' => 'Utenti attivi',
        'placeholder' => 'Utenti attivi',
    ],
    'deleted-users' => [
        'label' => 'Utenti cancellati',
        'placeholder' => 'Utenti cancellati',
    ],
    'view-option' => [
        'label' => 'Visualizza opzioni',
        'placeholder' => 'Visualizza opzioni',
    ],
    'accoutn-not-verified' => [
        'label' => 'Account non verificato',
        'placeholder' => 'Account non verificato',
    ],
    'enabled' => [
        'label' => 'Abilitato',
        'placeholder' => 'Abilitato',
    ],
    'user-account-is-deleted' => [
        'label' => 'Questo account è stato cancellato.',
        'placeholder' => 'Questo account è stato cancellato.',
    ],
    'restore-user' => [
        'label' => 'Ripristina account utente',
        'placeholder' => 'Ripristina account utente',
    ],
    'delete-account-caution-info' => [
        'label' => 'Questo account potrebbe avere ancora ticket aperti nel sistema.',
        'placeholder' => 'Questo account potrebbe avere ancora ticket aperti nel sistema.',
    ],
    'reply-can-not-be-empty' => [
        'label' => 'La risposta non può essere vuota.',
        'placeholder' => 'La risposta non può essere vuota.',
    ],

    // update 18-12-2016
    'account-created-contact-admin-as-we-were-not-able-to-send-opt' => [
        'label' => 'Il tuo account è stato creato correttamente. Per favore contatta l\'amministratore in quanto non siamo stati in grado di spedire un codice OTP.',
        'placeholder' => 'Il tuo account è stato creato correttamente. Per favore contatta l\'amministratore in quanto non siamo stati in grado di spedire un codice OTP.',
    ],
    // update 19-12-2016
    'only-agents' => [
        'label' => 'Utenti agenti',
        'placeholder' => 'Utenti agenti',
    ],
    'only-users' => [
        'label' => 'Utenti clienti',
        'placeholder' => 'Utenti clienti',
    ],
    'banned-users' => [
        'label' => 'Utenti bannati',
        'placeholder' => 'Utenti bannati',
    ],
    'inactive-users' => [
        'label' => 'Utenti inattivi',
        'placeholder' => 'Utenti inattivi',
    ],
    'all-users' => [
        'label' => 'Tutti gli utenti',
        'placeholder' => 'Tutti gli utenti',
    ],
    // update 21-12-2016
    'selected-user-is-already-the-owner' => [
        'label' => 'L\'utente selezionato è già il proprietario del ticket.',
        'placeholder' => 'L\'utente selezionato è già il proprietario del ticket.',
    ],
];
