<?php

 function survey_result_shortcode( $atts ) {
 	global $wpdb;

 	$a = shortcode_atts( //shortcodové hodnoty
		array(
			'slug-name' => 'test',
			'id' => '1',
		), $atts );

 	// ************** výpis ankety z databáze

 	$id = $a['id']; //vrátí hodnotu která je zadaná jako hodnota "id" - [survey slug-name="slovo" id="3" ] = vrátí "3"
 	$table_name = $wpdb->prefix . 'surveys';
 	$data = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = $id", ARRAY_A ); //načte tabulku vybrané ankety jako pole

 	return survey_results_fc($data); 
 }

 function survey_results_fc($data){ //funkce pro výpis výsledků ankety - v oddělené funkci kvůli výpisu po hlasování (dvojí použití)

 	if(!empty($data['name'])){ //pokud má anketa název vypíše se (anketa lze vytvořit pouze s názvem)

			$name = $data['name']; //získá název aktuální ankety
			$slug = $data['name_slug']; //získá slug název aktuální ankety
			$opt_arr = $data['opt_array']; //získá pole možností aktuální ankety
			$vote_arr = $data['vote_array']; //získá pole hlasů aktuální ankety
			$opt_answ = $data['opt_answ']; //získá počet možných hlasů aktuální ankety
			$vote_other = $data['vote_other']; //získá alertnativní odpovědi

			$options = explode("-/separator/-", $opt_arr); // znovu vytvoří pole
			$votes = explode("-/separator/-", $vote_arr); // znovu vytvoří pole

			if(empty($vote_other)){ //načte pole alt.hlasů pokud existují pokud ne založí prázdné pole aby se správně sečetly hlasy
				$vote_other_arr = array();
			}else{
				$vote_other_arr = explode("-/separator/-", $vote_other);
			}

			$vote_count = NULL; //vynulování minulého

			foreach($votes as $vote_key => $vote){ //spočítá hlasy 
	  			$vote_count = $vote_count + $vote; //počet hlasů
	  		}

	  		$alt_votes = count($vote_other_arr); //spočíta alt. hlasy
	  		$vote_count += $alt_votes; //přičte alt.hlasy

	  		if(!($vote_count==0)){ //pokud nejsou hlasy nevypíše se

				if($vote_count==1){
		  			$v = "{$vote_count} vote";
		  		}else{
		  			$v = "{$vote_count} votes";
		  		}

				$survey_top = 
				"		<div class='survey_results' >
						<h2 class='survey-name'> {$name} ({$v}) </h2>   
		  				<table>";
	  			
	  			$table_html = array();

		  		foreach($options as $opt_key => $option){ //postupně přidá všechny možnosti do stringu "opt_string"
		  			$vote = $votes[$opt_key];
		  			if($option != '/#/alt_option/#/'){ //pokud je to alt. možnost běžnému uživateli se nevypíše

			  			if($vote==0){ 
			  				$vote_percentage = "0%";
			  			}else{
			  				$vote_percentage = 100*($vote / $vote_count); //kolik procen celkového poču hlasů je současný hlas
			  				$vote_percentage = round($vote_percentage,0) . "%"; //zaokrouhlení a přidáníprocenta
			  			}

			  			$table_columns = "<tr><th class='survey_result_opt_name' > {$option} </th>  <th><b> <div class='survey_bar' >{$vote_percentage}</div></b> </th> </tr>"; //použít js na vynásobení procent a šířky barevného sloupce zobrazující množství

			  			array_push($table_html, $table_columns);   
		  			}else{

		  				if($alt_votes == 0){ 
			  				  $vote_percentage = "0%";
				  		  }else{
				  			  $vote_percentage = 100*($alt_votes / $vote_count); //kolik procent celkového poču hlasů je současný hlas
				  			  $vote_percentage = round($vote_percentage,0) . "%"; //zaokrouhlení a přidáníprocenta
				  		  }

				  		  $alt = get_option( 'alt_opt_n' );

				  		  $table_columns = "<tr><th class='survey_result_opt_name' > <b>{$alt}</b> </th>  <th><b> <div class='survey_bar' >{$vote_percentage}</div></b> </th> </tr>"; //použít js na vynásobení procent a šířky barevného sloupce zobrazující množství

				  		  array_push($table_html, $table_columns);  
		  			}
		  		}

		  		$table_html = implode("\n", $table_html);


		  		$survey_bottom ='
		  					  	</table>
	  					  	</div>';

	  			$survey_result = $survey_top . $table_html . $survey_bottom;
			
	  	}else{
	  		$survey_result = "<h2 class='survey-name'> {$name} (has no votes yet)</h2>"; //jméno ankety odkazuje na editaci ankety 
	  	}
		  		
	  	return $survey_result;
	   
 	}else{ //pokud anketa není v db vypíše chybová hláška
 	   return "<p class='survey_warning' >Survey doesn't exist</p>";
 	}

 }

?>