<?php
/**
 * Twispay Language Configurator
 *
 * Twispay general language handler for everything
 *
 * @package  Twispay/Language
 * @category Admin/Front
 * @version  1.0.1
 */

// Configuration panel from Administrator
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

// Transaction list from Administrator
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

// Transaction log from Administrator
$tw_lang['transaction_log_title'] = 'Jurnal de tranzacții';
$tw_lang['transaction_log_no_log'] = 'Nicio intrare înregistrată încă';
$tw_lang['transaction_log_subtitle'] = 'Jurnal de tranzacții în formă brută';

// Administrator Dashboard left-side menu
$tw_lang['menu_main_title'] = 'Twispay';
$tw_lang['menu_configuration_tab'] = 'Configurație';
$tw_lang['menu_transaction_tab'] = 'Lista tranzacțiilor';
$tw_lang['menu_transaction_log_tab'] = 'Jurnal de tranzacții';

// Woocommerce settings Twispay tab
$tw_lang['ws_title'] = 'Twispay';
$tw_lang['ws_description'] = 'Invită-ți clienții să folosească gateway-ul de plată Twispay.';
$tw_lang['ws_enable_disable_title'] = 'Activează/Dezactivează';
$tw_lang['ws_enable_disable_label'] = 'Activează plățile Twispay';
$tw_lang['ws_title_title'] = 'Titlu';
$tw_lang['ws_title_desc'] = 'Controlează titlul pe care îl vede clientul în timpul efectuării plății.';
$tw_lang['ws_description_title'] = 'Descriere';
$tw_lang['ws_description_desc'] = 'Controlează descrierea pe care clientul o vede în timpul efectuării plății.';
$tw_lang['ws_description_default'] = 'Plătește cu Twispay.';
$tw_lang['ws_enable_methods_title'] = 'Activează căile de expediere';
$tw_lang['ws_enable_methods_desc'] = 'Dacă Twispay este disponibil numai pentru anumite căi de expediere, configurează-le de aici. Lasă necompletat pentru a activa toate căile.';
$tw_lang['ws_enable_methods_placeholder'] = 'Selectează căile de expediere';
$tw_lang['ws_vorder_title'] = 'Acceptă comenzile virtuale';
$tw_lang['ws_vorder_desc'] = 'Acceptă Twispay în cazul comenzilor virtuale';

// Order Recieved Confirmation title
$tw_lang['order_confirmation_title'] = 'Mulțumim. Tranzacția ta a fost aprobată.';

// Twispay Processor( Redirect page to Twispay )
$tw_lang['twispay_processor_error'] = 'Nu ai permisiunea de a accesa acest fișier.';

// Validation LOG insertor
$tw_lang['log_s_decrypted'] = '[RESPONSE] string decriptat: ';
$tw_lang['log_empty_external'] = 'externalOrderId gol';
$tw_lang['log_order_already_validated'] = '[RESPONSE-ERROR] Comandă deja validată, ID-ul comenzii %s';
$tw_lang['log_empty_status'] = 'Status nul';
$tw_lang['log_empty_identifier'] = 'Identificator nul';
$tw_lang['log_empty_transaction'] = 'TransactionID nul';
$tw_lang['log_general_error'] = '[RESPONSE-ERROR] ';
$tw_lang['log_general_response_data'] = '[RESPONSE] Data: ';
$tw_lang['log_wrong_status'] = '[RESPONSE-ERROR] Status greșit (%s)';
$tw_lang['log_status_complete'] = '[RESPONSE] Status complet-ok';
$tw_lang['log_validating_complete'] = '[RESPONSE] Validarea comenzii: %s';
$tw_lang['log_decryption_error'] = '[ERROR] Decriptarea nu a funcționat.';
$tw_lang['log_openssl'] = 'opensslResult: ';
$tw_lang['log_decrypted_string'] = 'string decriptat: ';

// Wordpress Administrator Order Notice
$tw_lang['wa_order_status_notice'] = 'Plata Twispay a fost finalizată cu succes';
$tw_lang['wa_order_refunded_notice'] = 'Managerul site-ului a apăsat cu succes butonul de restituire';
$tw_lang['wa_order_cancelled_notice'] = 'Managerul site-ului a apăsat cu succes butonul de anulare';

// Others
$tw_lang['general_error_title'] = 'S-a petrecut o eroare:';
$tw_lang['general_error_desc_f'] = 'Plata nu a putut fi procesată. Te rog [try again] sau';
$tw_lang['general_error_desc_try_again'] = 'încearcă din nou';
$tw_lang['general_error_desc_s'] = 'contactează administratorul site-ului';
$tw_lang['general_error_invalid_key'] = 'Plata nu a putut fi procesată. Cheie de siguranță nevalidă.';
$tw_lang['general_error_invalid_order'] = 'Plata nu a putut fi procesată. Comanda nu există.';
$tw_lang['general_error_invalid_private'] = 'Plata nu a putut fi procesată. Cheie privată nevalidă.';
