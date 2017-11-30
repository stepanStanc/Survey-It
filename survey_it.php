<?php
/**
* Plugin Name: Survey it!
* Plugin URI: none
* Description: Create your own survey and put it on your site.
* Version: 0.1
* Author: Štěpán Štanc
* Author URI: none
* License: GPL2
**/ 

 session_start(); // spuštění sessionu (platí pro včechny includy)

 include "functions.php"; //customizované funkce a funkce pro akce a shortcody

 include "main_page.php"; //hlavní stránka pluginu ve správě WP - výpis anket + je v ní include na editaci ankety
 include "survey_shortcode.php"; //shortcode funkce - anketa
 include "survey_result_shortcode.php"; //shortcode funkce - výsledky ankety
 include "new_survey.php"; //submenu s vytváření anket
 include "survey_results.php"; //submenu s výsledky anket
 include "survey_it_settings.php"; //submenu nastavení
 include "activation_deactivation.php"; //funkce pro zapínání a vypínání pluginu (vytvoření/smazání tabulek)
 include "survey_widgets.php"; //sidebary s anketou a s výsledky ankety

 register_activation_hook( __FILE__, 'install_surveys_plugin' ); //při spuštení pluginu
 register_deactivation_hook( __FILE__, 'uninstall_surveys_plugin' ); //při deaktivování pluginu

 add_shortcode( 'survey', 'survey_shortcode' ); //přidá survey_shortcode ve funkci "survey_shortcode" pod názvem survey
 add_shortcode( 'survey-result', 'survey_result_shortcode' ); //přidá shortcode 

 add_action( 'admin_menu', 'create_menu_survey'); //přidá menu a submenu
 add_action( 'wp_enqueue_scripts', 'add_survey_it_script'); //přidá do normálního stránky script a style a jquery
 add_action( 'admin_enqueue_scripts' , 'add_survey_it_admin_script'); //pro adminitrátorské prostředí WP
 add_action( 'init', 'survey_it_buttons' ); //přidá TinyMCE tlačítek
 add_action( 'before_wp_tiny_mce' , 'survey_it_before_wp_tiny_mce'); //dostání php kódu před TinyMCE pluginu
 add_action( 'widgets_init', 'register_survey_widgets' ); //přidá widgety

 add_action( 'wp_ajax_survey_it_add_option', 'survey_it_add_option' );                    //přidá zpracování ajaxu - přidávání možností
 add_action( 'wp_ajax_delete_selected_survey_options', 'delete_selected_survey_options' );//přidá zpracování ajaxu - mazání zvolených možností
 add_action( 'wp_ajax_survey_it_add_alt_opt', 'survey_it_add_alt_opt' );                  //přidá zpracování ajaxu - přidání alt možnosti
 add_action( 'wp_ajax_survey_it_edit_opt', 'survey_it_edit_opt' );                        //přidá zpracování ajaxu - zvolení možnosti na editaci
 add_action( 'wp_ajax_survey_it_editing_opt', 'survey_it_editing_opt' );                  //přidá zpracování ajaxu - editace možnosti
 add_action( 'wp_ajax_survey_it_add_survey', 'survey_it_add_survey' );                    //přidá zpracování ajaxu - vytvoření ankety
 add_action( 'wp_ajax_survey_it_change_survey', 'survey_it_change_survey' );              //přidá zpracování ajaxu - editace ankety
 add_action( 'wp_ajax_survey_it_opt_order_change', 'survey_it_opt_order_change' );        //přidá zpracování ajaxu - měnění pořadí itemů v tabulce

 add_action( 'wp_ajax_survey_it_ajax_voting', 'survey_it_ajax_voting_callback' ); 		 //přidá zpracování ajaxu(pro přihlášené uživatele) - hlasování
 add_action( 'wp_ajax_nopriv_survey_it_ajax_voting', 'survey_it_ajax_voting_callback' ); //přidá zpracování ajaxu(nonpriv pro nepřihlášené uživatele) - hlasování

 add_action( 'wp_ajax_survey_it_ajax_voting_check', 'survey_it_ajax_voting_check_callback' ); 		 //přidá zpracování ajaxu(pro přihlášené uživatele) - kontrola hlasování
 add_action( 'wp_ajax_nopriv_survey_it_ajax_voting_check', 'survey_it_ajax_voting_check_callback' ); //přidá zpracování ajaxu(nonpriv pro nepřihlášené uživatele) - kontrola hlasování

?>