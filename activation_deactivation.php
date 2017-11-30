<?php

 function install_surveys_plugin() {
	global $wpdb;

	$table_name = $wpdb->prefix . 'surveys';
	$table_name_v = $wpdb->prefix . 'votes';

	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
	  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
	  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
	  `name_slug` varchar(255) COLLATE utf8_czech_ci NOT NULL,
	  `opt_answ` tinyint(3) NOT NULL DEFAULT '1',
	  `opt_array` text COLLATE utf8_czech_ci NOT NULL,
	  `vote_array` text COLLATE utf8_czech_ci NOT NULL,
	  `vote_other` text COLLATE utf8_czech_ci NOT NULL,
	  UNIQUE KEY `id` (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1;" ;

	$sql_v = "CREATE TABLE IF NOT EXISTS $table_name_v (
	  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
	  `survey_id` mediumint(9) NOT NULL,
	  `user_hash` text COLLATE utf8_czech_ci NOT NULL,
	  UNIQUE KEY `id` (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1;";
	
	$wpdb->query($sql);
	$wpdb->query($sql_v);

	//default settings
	add_option( 'max_opt_c' , 20 ); //max počet možností v anketě
	add_option( 'max_check_c' , 5 ); //max počet zaškrtnutelných checkboxů
	add_option( 'alt_opt_n' , 'Another' ); //text v alt možnosti
	add_option( 'vote_b' , 'Vote!' ); //text v tlačítku hlasovat
	add_option( 'max_r_alt' , 15 ); //počat odpovědí na alt. možnost
	add_option( 'max_s_c_m' , 10 ); //stránkování hlavní stránky
	add_option( 'max_s_c_r' , 5 ); //stránkování hlavní stránky
	add_option( 'show_r' , true ); //zobrazit výsledky
 }

 

 function uninstall_surveys_plugin() {
 	global $wpdb;

    $table_name = $wpdb->prefix . 'surveys';
    $table_name_v = $wpdb->prefix . 'votes';

	$wpdb->query("DROP TABLE IF EXISTS $table_name");
	$wpdb->query("DROP TABLE IF EXISTS $table_name_v");
 }

?>