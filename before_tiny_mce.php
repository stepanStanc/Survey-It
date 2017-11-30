<?php
	global $wpdb;

    echo "<script type='text/javascript'>
        		window.survey_it_ids = new Array();
        		window.survey_it_names = new Array(); \n
                window.survey_it_slugs = new Array(); \n" ;

    $table_name = $wpdb->prefix . "surveys";
	$slugs = $wpdb->get_col( "SELECT name_slug FROM $table_name ORDER BY id DESC ");//získá všechna slug jména naket z wpdb anket - od nejnovějšího po nejstarší
	$ids = $wpdb->get_col( "SELECT id FROM $table_name ORDER BY id DESC ");//získá všechna id z wpdb anket - od nejnovějšího po nejstarší
    $names = $wpdb->get_col( "SELECT name FROM $table_name ORDER BY id DESC ");

	$count = 0;

    foreach($ids as $key => $id){
    	$name = $names[$key];
        $slug = $slugs[$key];
        echo "window.survey_it_ids[{$count}] = '{$id}'; \n";
        echo "window.survey_it_names[{$count}] = '{$name}'; \n";
        echo "window.survey_it_slugs[{$id}] = '{$slug}'; \n";
        $count++;
    }

    echo '</script>';
	
?>