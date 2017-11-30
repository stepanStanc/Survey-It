<?php

 function survey_shortcode( $atts ) {
 	global $wpdb;

 	$a = shortcode_atts( //shortcodové hodnoty
		array(
			'slug-name' => 'test',
			'id' => '1',
		), $atts );

 	// ************** výpis ankety z databáze

 	$id = $a['id'];

 	return "<div class='survey' id='{$id}'> </div>"; //připraví pro ajax

 }

/********************************************************************************************************************************************************************
**************************************************************** AJAX ***********************************************************************************************
*********************************************************************************************************************************************************************/

//************************** HLASOVÁNÍ

 function survey_it_ajax_voting_callback() {
 	global $wpdb;

 	$user_info = $_POST['user_info'];

 	$client_id = ajax_voting_data($user_info); 

 	$id = $_POST['id'];

 	$table_name_v = $wpdb->prefix . "votes";
 	$table_name = $wpdb->prefix . "surveys";

	$data = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = $id", ARRAY_A ); //načte tabulku vybrané ankety jako pole

	$vote = unserialize_checkbox_form($_POST['vote']);
	$vote_other_value = $_POST['option_other'];

	submit_votes($id,$data,$vote,$vote_other_value);

	$wpdb->insert($table_name_v, array(
	   "survey_id" => $id,
	   "user_hash" => $client_id
	));

	if(get_option( 'show_r' )){
		echo survey_results_fc($data);
	}

	wp_die(); 
 }

 //************************ KONTROLA JESTLI UŽIVATEL HLASOVAL

 function survey_it_ajax_voting_check_callback() {
 	global $wpdb;

 	$user_info = $_POST['user_info'];


 	$client_id = "'". ajax_voting_data($user_info) ."'";

 	$id = $_POST['id'];

 	$table_name_v = $wpdb->prefix . "votes";

 	$voted = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name_v WHERE survey_id = $id AND user_hash = $client_id " );

 	if($voted > 0){
 		$table_name = $wpdb->prefix . "surveys";
 		$data = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = $id", ARRAY_A ); //načte tabulku vybrané ankety jako pole

 		if(get_option( 'show_r' )){
			echo survey_results_fc($data);
		}
 	}else{
 		echo load_survey_fc($id);
 	}

 	wp_die(); 
 }

 //****************************** IDENTIFIKÁTOR UŽIVATELE

 function ajax_voting_data($user_info) { 

 	if (!isset($_SERVER['HTTP_X_FORWARDED_FOR'])) { //získání IP adresy

    	$client_ip = '/#/'.$_SERVER['REMOTE_ADDR'].'/#/';
	} else {

	    $client_ip =  '/#/'.$_SERVER['HTTP_X_FORWARDED_FOR'].'/#/'; 
	}

	$client_ip = preg_replace('/\./','-', $client_ip); //přepíše tečky v ip na pomlčky

 	return hash('sha256',$client_ip . $user_info); //vrátí hash ip a dat od js(user_ifo)
 }

//*************************************** VÝPIS ANKETY

 function load_survey_fc( $id ) {
 	global $wpdb;
 	
 	$table_name = $wpdb->prefix . 'surveys';

 	$data = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = $id", ARRAY_A ); //načte tabulku vybrané ankety jako pole

	 	if(!empty($data['name'])){ //pokud má anketa název vypíše se (anketa lze vytvořit pouze s názvem)

		 	$name = $data['name'];
		 	$slug = $data['name_slug'];
		 	$opt_answ = $data['opt_answ'];

		 	if(!empty($opt_vote)){ //založí nebo načte pole
		 		$alt_votes = explode("-/separator/-", $opt_vote);
		 	}else{
		 		$alt_votes = array();
		 	}
		 	
		 	$options = explode("-/separator/-", $data['opt_array']); // znovu vytvoří pole
		 	$vote_a = explode("-/separator/-", $data['vote_array']); // znovu vytvoří pole
		 	
		 	// ********************* začátek formuláře *********************
		 	$table_top = 
		 	"	<p id='opt_answ' >{$opt_answ}</p>
		 		<h2 class='survey-name' >{$name} </h2>
		 		<form method='post' id='survey_form' class='survey_form'>"; //vložení vrchní části formuláře do proměné aby se dalana vrátit
		 			
		 		 		$checkboxes_html = array("\n"); //pole checkboxu "\n" pro odřádkování

		 				if(isset($options)){ 

					  		foreach($options as $key => $name){ //vygeneruje všechny body ankety v poli
					  		  if($name=='/#/alt_option/#/'){ //pokud je to alternarivní možnost tak se to napíše
					  		  	  $alt = get_option( 'alt_opt_n' );
								  $name = "<b>{$alt}</b>";
								  $key = 'alt'; //pro detekci
							  }

				  			  $checkbox_html =  "<input type='checkbox' name='vote[]' value='{$key}'> <label class='survey_option_name' > {$name} </label> <br>"; 
							  array_push($checkboxes_html, $checkbox_html); //přidává surové HTML do pole (jednotlivé checkboxy)
							}
					  	}

					  	$checkboxes_html = implode("\n", $checkboxes_html); //vytvoří string "\n" pro odřádkování

		 	$vote_b = get_option( 'vote_b' );

		 	$table_bottom = 
				"
				</form>
				 <input type='text' name='option_other' class='option_other' >
		 		 <input type='submit' value='{$vote_b}' class='confirm_survey' form='survey_form'><br>"; //spodní část formuláře

		 	// ********************* konec formuláře *********************
		  
	   return $table_top . $checkboxes_html . $table_bottom ;
 	}else{ //pokud anketa není v db vypíše chybová hláška
 	   return "<p class='survey_warning' >Survey doesn't exist</p>";
 	}

 }

 //****************************** ZÁPIS DO DB

 function submit_votes($id,$data,$vote,$vote_other_value) {
	global $wpdb;

	if(!empty($data['name'])){ //pokud má anketa název vypíše se (anketa lze vytvořit pouze s názvem)

		$name = $data['name'];
		$slug = $data['name_slug'];
		$opt_answ = $data['opt_answ'];
		$opt_vote = $data['vote_other'];

		if(!empty($opt_vote)){ //založí nebo načte pole
			$alt_votes = explode("-/separator/-", $opt_vote);
		}else{
			$alt_votes = array();
		}
		
		$options = explode("-/separator/-", $data['opt_array']); // znovu vytvoří pole
		$vote_a = explode("-/separator/-", $data['vote_array']); // znovu vytvoří pole
		$opt_count = count($options); //zpočítá pole (počet hodnot) - počítá od jedné

		  if(!empty($vote)) { //pokud je nějaký zaškrtnutý
			 	global $wpdb;

			 	//******************** alternativní možnost ***********************

			 	 if(in_array("alt", $vote)){//pokud je alt. možnost zaškrtnutá (in_array - se ptá jestli existuje v poli)

		 	  	 	 if(!empty($vote_other_value)){ //získání alt. odpovědi

					    array_push($alt_votes, $vote_other_value); //přidá hlasy do pole hlasů
					    $vote_other = implode("-/separator/-", $alt_votes); //vytvoří text z pole

					  }else{

					  	unset($vote_other);

					  }

					  $opt_count = $opt_count-1; //snížení počtu odpovědí o alt. možnost
					  array_pop($vote);
				  }

			 	//******************** přičítání normálních možností ***********************

			    for($i=0; $i < $opt_count; $i++){ //podle počtu možností v anketě se automaticky vygenerují očíslované proměné
				      ${"vote_" . $i} = "0"; //vylní všechny nulou

				      foreach($vote as $value) {

					       if($i==$value) { //pokud se hodnota(pořadové číslo hlasu) rovná hodnotě zaškrtnutého políčka tak se vyplní jednička (jeden hlas)
					         	${"vote_" . $i} = "1";
						   }

					  }
			    }

				for($i=0; $i < $opt_count; $i++){ //vygenerují se proměné pro wpdb příkaz
				     ${"vote_opt_" . $i} = $vote_a[$i] + ${"vote_" . $i}; //pokud možnost je zaškrtnutá tak se přičte k aktuálnímu počtu zaškrtnutých pokud ne nic se nestane
				}

			    foreach($vote_a as $k => $v) {
		           $vote_arr[] = ${"vote_opt_" . $k}; //vtlačí proměné do pole - zkrácená verze "array_push()"
		        }

			   //*************************** zápis do DB **************************

		        if(!empty($vote_other_value)){ //získání alt. odpovědi

				    foreach($options as $opt_key => $option){ //porovná jestli se odpověď shoduje nějakou z možností pokud ano přičte jí k normálním možnostem a odpověď smaže

				  		if(options_compare($vote_other_value, $option)){ 
				  			$vote_arr[$opt_key] += 1;

				  			unset($vote_other);
				  		}

			  		}
			  		
		  		}

				$vote_array = implode("-/separator/-", $vote_arr); //imploduje pro db

			    $table_name = $wpdb->prefix . "surveys";
			    
			    if(!isset($vote_other)){
					$wpdb->update($table_name, array(
					   "vote_array" => $vote_array,
					),array( 'id' => $id ) );
				}else{

					$wpdb->update($table_name, array(
					   "vote_array" => $vote_array,
					   "vote_other" => $vote_other
					),array( 'id' => $id ) );
				}
					

			}
		
		}
 }



?>