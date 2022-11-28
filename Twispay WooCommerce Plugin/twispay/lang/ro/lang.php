<?php
/**
 * Twispay Language Configurator
 *
 * Twispay general language handler for everything
 *
 * @package  Twispay/Language
 * @category Admin/Front
 * @author   Twispay
 */

/* Configuration panel from Administrator */
$tw_lang['no_woocommerce_f'] = 'Twispay necesită pluginul WooCommerce pentru a funcționa normal. Activează-l sau instalează-l de';
$tw_lang['no_woocommerce_s'] = 'aici';
$tw_lang['configuration_title'] = 'Configurație';
$tw_lang['configuration_edit_notice'] = 'Configurația a fost editată cu succes.';
$tw_lang['configuration_subtitle'] = 'Setări generale Twispay.';
$tw_lang['live_mode_label'] = 'Mod live';
$tw_lang['live_mode_desc'] = 'Selectează "Da" dacă dorești să folosești gateway-ul de plată în modul Live sau "Nu" dacă dorești să îl utilizezi în modul Staging.';
$tw_lang['staging_id_label'] = 'Staging Site ID';
$tw_lang['staging_id_desc'] = 'Introdu Site ID-ul pentru modul Staging. Poți obține unul de';
$tw_lang['staging_key_label'] = 'Staging Private Key';
$tw_lang['staging_key_desc'] = 'Introdu Private Key-ul pentru modul Staging. Poți obține unul de';
$tw_lang['live_id_label'] = 'Live Site ID';
$tw_lang['live_id_desc'] = 'Introdu Site ID-ul pentru modul Live. Poți obține unul de';
$tw_lang['live_key_label'] = 'Live Private Key';
$tw_lang['live_key_desc'] = 'Introdu Private Key-ul pentru modul Live. Poți obține unul de';
$tw_lang['s_t_s_notification_label'] = 'Adresă URL de notificare server-to-server';
$tw_lang['s_t_s_notification_desc'] = 'Introdu această adresă URL în contul tău Twispay.';
$tw_lang['r_custom_thankyou_label'] = 'Redirecționare la pagina personalizată de Thank You';
$tw_lang['r_custom_thankyou_desc_f'] = 'Dacă dorești să afișezi pagina personalizată de Thank You, configureaz-o aici. Poți crea o pagină personalizată nouă de';
$tw_lang['r_custom_thankyou_desc_s'] = 'aici';
$tw_lang['suppress_email_label'] = 'Dezactivează e-mailurile implicite WooCommerce de confirmare a plății';
$tw_lang['suppress_email_desc'] = 'Opțiunea de a dezactiva comunicarea trimisă de sistemul de e-commerce, pentru a o configura din interfața de comerciant Twispay.';
$tw_lang['configuration_save_button'] = 'Salvează modificările';
$tw_lang['live_mode_option_true'] = 'Da';
$tw_lang['live_mode_option_false'] = 'Nu';
$tw_lang['get_all_wordpress_pages_default'] = 'Mod implicit';
$tw_lang['contact_email_o'] = 'E-mail de contact (Opțional)';
$tw_lang['contact_email_o_desc'] = 'Acest e-mail va fi folosit pe pagina de eroare de plată.';


/* Transaction list from Administrator */
$tw_lang['transaction_title'] = 'Lista de tranzacții';
$tw_lang['transaction_list_search_title'] = 'Caută comandă';
$tw_lang['transaction_list_all_views'] = 'Toate';
$tw_lang['transaction_list_refund_title'] = 'Tranzacție de restituire';
$tw_lang['transaction_list_recurring_title'] = 'Anulează recurența acestei comenzi';
$tw_lang['transaction_list_id'] = 'ID';
$tw_lang['transaction_list_id_cart'] = 'Numărul comenzii';
$tw_lang['transaction_list_customer_name'] = 'Numele clientului';
$tw_lang['transaction_list_transactionId'] = 'ID-ul tranzacției';
$tw_lang['transaction_list_status'] = 'Status';
$tw_lang['transaction_list_checkout_url'] = 'Checkout URL';
$tw_lang['transaction_list_refund_ptitle'] = 'Tranzacție de restituire a plății';
$tw_lang['transaction_list_refund_subtitle'] = 'Următoarea tranzație de plată va fi restituită:';
$tw_lang['transaction_list_confirm_title'] = 'Confirm';
$tw_lang['transaction_error_refund'] = 'Restituirea nu a putut fi procesată.';
$tw_lang['transaction_error_recurring'] = 'Plata recurentă nu a putut fi procesată.';
$tw_lang['transaction_success_refund'] = 'Restituirea a fost procesată cu succes. Reîncarcă pagina în câteva secunde pentru a vedea actualizarea.';
$tw_lang['transaction_success_recurring'] = 'Comandă recurentă procesată cu succes.';
$tw_lang['transaction_list_recurring_ptitle'] = 'Anulează o comandă recurentă';
$tw_lang['transaction_list_recurring_subtitle'] = 'Următoarea plată recurentă va fi anulată:';
$tw_lang['transaction_sync_finished'] = 'Sincronizarea abonamentelor terminata.';


/* Transaction log from Administrator */
$tw_lang['transaction_log_title'] = 'Jurnal de tranzacții';
$tw_lang['transaction_log_no_log'] = 'Nicio intrare înregistrată încă';
$tw_lang['transaction_log_subtitle'] = 'Jurnal de tranzacții în formă brută';


/* Administrator Dashboard left-side menu */
$tw_lang['menu_main_title'] = 'Twispay';
$tw_lang['menu_configuration_tab'] = 'Configurație';
$tw_lang['menu_transaction_tab'] = 'Lista tranzacțiilor';
$tw_lang['menu_transaction_log_tab'] = 'Jurnal de tranzacții';


/* Woocommerce settings Twispay tab */
$tw_lang['ws_title'] = 'Twispay';
$tw_lang['ws_description'] = 'Invită-ți clienții să folosească gateway-ul de plată Twispay.';
$tw_lang['ws_enable_disable_title'] = 'Activează/Dezactivează';
$tw_lang['ws_enable_disable_label'] = 'Activează plățile Twispay';
$tw_lang['ws_title_title'] = 'Titlu';
$tw_lang['ws_title_desc'] = 'Controlează titlul pe care îl vede clientul în timpul efectuării plății.';
$tw_lang['ws_description_title'] = 'Descriere';
$tw_lang['ws_description_desc'] = 'Controlează descrierea pe care clientul o vede în timpul efectuării plății.';
$tw_lang['ws_description_default'] = 'O integrare, mai multe metode de plată. Twispay vă permite să acceptați plăți practic de oriunde în lume, printr-o multitudine de metode de plată.';
$tw_lang['ws_enable_methods_title'] = 'Activează căile de expediere';
$tw_lang['ws_enable_methods_desc'] = 'Dacă Twispay este disponibil numai pentru anumite căi de expediere, configurează-le de aici. Lasă necompletat pentru a activa toate căile.';
$tw_lang['ws_enable_methods_placeholder'] = 'Selectează căile de expediere';
$tw_lang['ws_vorder_title'] = 'Acceptă comenzile virtuale';
$tw_lang['ws_vorder_desc'] = 'Acceptă Twispay în cazul comenzilor virtuale';


/* Order Recieved Confirmation title */
$tw_lang['order_confirmation_title'] = 'Mulțumim. Tranzacția ta a fost aprobată.';


/* Twispay Processor( Redirect page to Twispay ) */
$tw_lang['twispay_processor_error_general'] = 'Nu ai permisiunea de a accesa acest fișier.';
$tw_lang['twispay_processor_error_no_item'] = 'Comanda nu are nici un produs.';
$tw_lang['twispay_processor_error_more_items'] = 'Comenzile cu abonamente nu pot sa contina mai mult de un produs.';
$tw_lang['twispay_processor_error_missing_configuration'] = 'Lipsa fisier de configurare pentru plugin.';


/* Validation LOG insertor */
$tw_lang['log_ok_string_decrypted'] = '[RESPONSE]: Decriptare efectuata cu succes.';
$tw_lang['log_ok_response_data'] = '[RESPONSE]: Data: ';
$tw_lang['log_ok_status_complete'] = '[RESPONSE]: Status complet-ok';
$tw_lang['log_ok_status_refund'] = '[RESPONSE]: Status refund-ok pentru comanda cu ID-ul: ';
$tw_lang['log_ok_status_failed'] = '[RESPONSE]: Status failed pentru comanda cu ID-ul: ';
$tw_lang['log_ok_status_hold'] = '[RESPONSE]: Status on-hold pentru comanda cu ID-ul: ';
$tw_lang['log_ok_status_uncertain'] = '[RESPONSE]: Status uncertain pentru comanda cu ID-ul: ';
$tw_lang['log_ok_validating_complete'] = '[RESPONSE]: Validare cu succes pentru comanda cu ID-ul: ';

$tw_lang['log_error_validating_failed'] = '[RESPONSE-ERROR]: Validare esuată pentru comanda cu ID-ul: ';
$tw_lang['log_error_decryption_error'] = '[RESPONSE-ERROR]: Decriptarea nu a funcționat.';
$tw_lang['log_error_invalid_order'] = '[RESPONSE-ERROR]: Comanda nu există.';
$tw_lang['log_error_wrong_status'] = '[RESPONSE-ERROR]: Status greșit: ';
$tw_lang['log_error_empty_status'] = '[RESPONSE-ERROR]: Status nul';
$tw_lang['log_error_empty_identifier'] = '[RESPONSE-ERROR]: Identificator nul';
$tw_lang['log_error_empty_external'] = '[RESPONSE-ERROR]: ExternalOrderId gol';
$tw_lang['log_error_empty_transaction'] = '[RESPONSE-ERROR]: TransactionID nul';
$tw_lang['log_error_empty_response'] = ' [RESPONSE-ERROR]: Răspunsul primit este nul.';
$tw_lang['log_error_invalid_private'] = '[RESPONSE-ERROR]: Cheie privată nevalidă.';
$tw_lang['log_error_invalid_key'] = '[RESPONSE-ERROR]: Cheie de identificare a comenzii nevalidă.';
$tw_lang['log_error_openssl'] = '[RESPONSE-ERROR]: opensslResult: ';


/* Subscriptions section */
$tw_lang['subscriptions_sync_label'] = 'Sincronizeaza abonamentele';
$tw_lang['subscriptions_sync_desc'] = 'Sincronizeaza starea locala cu starea de pe server a tuturor abonamentelor.';
$tw_lang['subscriptions_sync_button'] = 'Sincronizeaza';
$tw_lang['subscriptions_log_ok_set_status'] = '[RESPONSE]: Starea de pe server setata pentru comanda cu ID-ul: ';
$tw_lang['subscriptions_log_error_set_status'] = '[RESPONSE-ERROR]: Eroare la setarea starii pentru comanda ci ID-ul: ';
$tw_lang['subscriptions_log_error_get_status'] = '[RESPONSE-ERROR]: Eroare la extragerea starii de pe server pentru comanda cu ID-ul:A';
$tw_lang['subscriptions_log_error_call_failed'] = '[RESPONSE-ERROR]: Eroare la apelarea server-ului: ';
$tw_lang['subscriptions_log_error_http_code'] = '[RESPONSE-ERROR]: Cod HTTP neasteptat: ';


/* Wordpress Administrator Order Notice */
$tw_lang['wa_order_status_notice'] = 'Plata Twispay a fost finalizată cu succes';
$tw_lang['wa_order_refunded_notice'] = 'Managerul site-ului a apăsat cu succes butonul de restituire';
$tw_lang['wa_order_cancelled_notice'] = 'Managerul site-ului a apăsat cu succes butonul de anulare';
$tw_lang['wa_order_failed_notice'] = 'Plata Twispay a fost finalizată cu eroare';
$tw_lang['wa_order_hold_notice'] = 'Plata Twispay este in asteptare';


/* Others */
$tw_lang['general_error_title'] = 'S-a petrecut o eroare:';
$tw_lang['general_error_desc_f'] = 'Plata nu a putut fi procesată. Te rog';
$tw_lang['general_error_desc_try_again'] = ' încearcă din nou';
$tw_lang['general_error_desc_or'] = ' sau';
$tw_lang['general_error_desc_contact'] = ' contactează';
$tw_lang['general_error_desc_s'] = ' administratorul site-ului.';
$tw_lang['general_error_hold_notice'] = ' Plata este in asteptare.';
$tw_lang['general_error_invalid_key'] = ' Cheie de siguranță nevalidă.';
$tw_lang['general_error_invalid_order'] = ' Comanda nu există.';
$tw_lang['general_error_invalid_private'] = ' Cheie privată nevalidă.';


/* JSON decoding/encoding errors */
$tw_lang['JSON_ERROR_DEPTH'] = 'Adancimea maxima a stivei a fost depasita.';
$tw_lang['JSON_ERROR_STATE_MISMATCH'] = 'JSON invalid sau deformat.';
$tw_lang['JSON_ERROR_CTRL_CHAR'] = 'Eroare la caracterul de control, posibil sa nu fie codificat corect.';
$tw_lang['JSON_ERROR_SYNTAX'] = 'Eroare de sintaxa.';
$tw_lang['JSON_ERROR_UTF8'] = 'Caractere UTF-8 deformate, posibil sa nu fie codificate corect.';
$tw_lang['JSON_ERROR_RECURSION'] = 'Una sau mai multe referinte recursive in valorile care trebuie codificate.';
$tw_lang['JSON_ERROR_INF_OR_NAN'] = 'Una sau mai multe valoru NAN sau INF in valorile care trebuie codificate.';
$tw_lang['JSON_ERROR_UNSUPPORTED_TYPE'] = 'A fost trimisa o valoare de un tip ce nu poate fi codificat.';
$tw_lang['JSON_ERROR_INVALID_PROPERTY_NAME'] = 'A fost trimis un nume de proprietate ce nu poate fi codificat.';
$tw_lang['JSON_ERROR_UTF16'] = 'Caractere UTF-16 deformate, posibil sa nu fie codificate corect.';
$tw_lang['JSON_ERROR_UNKNOWN'] = 'Eroare necunoscuta.';

$tw_lang['default_description'] = 'Plateste cu Twispay';
