<?php

function new_survey() {
global $wpdb;
  
  	// form for creating survey
  if(is_admin()){
   ?>

   		<div id="screen-meta-links" >
	   		<div id="screen-options-link-wrap" class="screen-meta-toggle">
					<a class="setting_survey_it_link" href="<?php echo get_site_url() . '/wp-admin/options-general.php?page=survey_it_settings';?>" > Survey it! settings </a>
			</div>
		</div>
	<?php } ?>
	
  	<form method="post" id="survey_main" class="survey_main" >
  	  <label>Survey name :</label><br>
	  <input type="text" name="name" class="survey-name-input name main_survey_input" ><br>
	  <label>Allowed votes per person :</label><br>
	  <select name="opt_answ" class="opt_answ" >
             <?php
             	for($i=1; $i <= get_option( 'max_check_c' ); $i++){
				      echo " <option value='{$i}' > {$i} </option> ";
			    }
             ?>
      </select><br>
     </form>

     <form method="post" id="survey_option" class="survey_option" >
  	  	<label>Option (max. <?php echo get_option( 'max_opt_c' ); ?>) :</label><br>
	    <input type="text" name="option" class="opt_survey_input" >
	 </form>

	 <div class='options_table' id='options_table' > <!-- tabulka možností generovaná ajaxem (elementy uvnitř se nahradí jakmile se spustí ajax) -->
	 	<?php
		 	manage_new_survey_options(false,NULL,NULL);
		 ?>
	 </div> 

	 <div class="tablenav bottom" >
	 	<input type="submit" name="create_survey" value="Create survey" class="button button-primary button-large create_survey" form="survey_main">
	 </div>

	<?php

}

/********************************************************************************************************************************************************************
**************************************************************** AJAX ***********************************************************************************************
*********************************************************************************************************************************************************************/

//*************************************** PŘIDÁNÍ MOŽNOSTI

function survey_it_add_option() {
  
  manage_new_survey_options(false,NULL,NULL);

  wp_die(); // zapotřebí pro správnou funkčnost
}

//******************************************* PŘIDÁNÍ ALTERNATIVNÍ MOŽNOSTI

function survey_it_add_alt_opt(){

  manage_new_survey_options(true,NULL,NULL);

  wp_die(); // zapotřebí pro správnou funkčnost
}

//***************************************** MAZÁNÍ VYBRANÉ MOŽNOSTI

function delete_selected_survey_options() { //smaže zaškrtnuté možnosti z pole
 
   $opt = unserialize_checkbox_form($_POST["checked"]);

   manage_new_survey_options(false,$opt,NULL);
  
   wp_die(); // zapotřebí pro správnou funkčnost
}

//********************************************* ZVOLENÍ MOŽNOSTI NA EDITOVÁNÍ

function survey_it_edit_opt() {

	manage_new_survey_options(false,NULL,$_POST["edit_id"]);

	wp_die(); // zapotřebí pro správnou funkčnost
}

//*************************************** EDITOVÁNÍ MOŽNOSTI

function survey_it_editing_opt() {

	$option_edit = $_POST['option_edit'];
	$key_edit = $_POST['key_edit'];



	$options = $_SESSION["options"];

	$options[$key_edit] = $option_edit;

	$_SESSION["options"] = $options;



	manage_new_survey_options(false,NULL,NULL);

	wp_die(); // zapotřebí pro správnou funkčnost
}

//********************************* VYTVOŘENÍ ANKETY

function survey_it_add_survey() {
  global $wpdb; // this is how you get access to the database

  create_new_survey();
  manage_new_survey_options(false,NULL,NULL); //musí zde být pokud by anketa nebsahovala potřebné data

  wp_die(); // zapotřebí pro správnou funkčnost
}

//********************************* DRAG & DROP MĚNĚNÍ POŘADÍ - SORTABLE

function survey_it_opt_order_change() {

	$rows = array();

	$rows_arr = unserialize_checkbox_form($_POST["changed"]);
	$options = $_SESSION["options"]; //načte možnosti

	foreach ($rows_arr as $i => $value) {

		if($value=='/#/alt_option/#/'){ //pokud je alt možnost tak je to poslední hodnota v poli
			$value=count($options)-1;
		}
	  
	    $new_order[$i] = $options[$value]; //přiřadí nové pořadí aktuálním možnostem

	}

	$new_order = array_values($new_order); //seřadí klíče aby šly popořadě 1 - n

	$_SESSION["options"] = $new_order; //uloží možnosti

	manage_new_survey_options(false,NULL,NULL);

	wp_die(); // zapotřebí pro správnou funkčnost
}

/********************************************************************************************************************************************************************
**************************************************************** FUNKCE GENERUJÍCÍ OBSAH STRÁNKY ********************************************************************
*********************************************************************************************************************************************************************/

//***************** VYTVÁŘENÍ A PRÁCE S MOŽNOSTMI

function manage_new_survey_options($alt, $opt, $edit_id){ // true/false pro alternativní možnost, opt null/pole pro použití(mazání), null/id článku pro edtitaci
  global $options; //založení globální proměné pro další práci s hodnotami
		  	
  if(isset($_SESSION["options"])){ //pokud neexistuje session s možnostmi vytvoří se pro ní pole pokud existuje tak se její data vloží do proměné options
		$options = $_SESSION["options"];
   }else{
		$options = array() ;
   }

  if(!empty($_POST['option'])){ //získání názvu možnosti z formluáře
  	  $option = $_POST['option'];
  }	 

  if($alt==true){
  		$option = '/#/alt_option/#/';
  }

  if(count($options)>=get_option( 'max_opt_c' )){ //omezení počtu možností v anketě
  	$option = NULL;
  	echo "<p class='survey_warning' >Survey has maximal count of options</p>";
  }

  //*******************adding option***************************************

  if(isset($option)){ //pokud byla napsaná nějaká nová možnost přidaná tak se do pole možností 
	  	if((end($options)=='/#/alt_option/#/')AND($option=='/#/alt_option/#/')){ //nacpe aletrnatvní možnost na konec pole pokud už je na konci pole tak se nepřidá
	  		$option = NULL;
	  	}elseif(end($options)=='/#/alt_option/#/'){ 
	  		array_pop($options);
	  		array_push($options, $option);
	  		array_push($options, '/#/alt_option/#/');
	  	}else{
	  		array_push($options, $option);
	  	}
  }

  if(!empty($opt)) { //pokud je nějaký zaškrtnutý smaže se
    	foreach ($opt as $value) { //získá id zaškrtnutého
	     		unset($options[$value]);//smaže možnost dané hodnoty
	    }

	    $options = array_values($options); //seřadí klíče aby šly popořadě
  }

  $_SESSION["options"] = $options; //uložení rozšířeného/původního pole do session

  if(!empty($_SESSION["options"])){ //pokud nexistuje žádná možnost v anketě - nezobrazí se tabulka
		?>
	<div class="tablenav top" >
	 	<input type="submit" value="Add option" class="button tagadd" form="survey_option">
		<input type="submit" name="action" value="Add alternative option" form="check_action" class="button tagadd add_alt_opt" title="Allows user to write own answer" />
		<input type="submit" name="action" value="Delete selected" form="check_action" class="button tagadd delete_selected_opts" />
	</div>

	<form method='post' id='check_action' name='check_action' class='check_action' >
 	<table class="wp-list-table widefat fixed striped posts sortable_table_survey_it" id="sortable_table_survey_it" >
 		<thead>
	 		<tr>
	 			<th>
	 				<input type='checkbox' name='tick_em_all' class='tick_em_all' >
	 			</th>
	 			<th>ID</th>
	 			<th>Name</th>
	 		</tr>
 		 </thead>
		  <?php
		  	}

		  	if(isset($options)){ // jednoduché zobrazení možností které byly přidány
		  		foreach($options as $key => $value){

		  			  if($value == '/#/alt_option/#/'){ //pokud je to alt možnost tak se zakáže editace a přidá se speciální hodnota do pole row pro identifikaci v AJAXU - sortable
		  			  	$alt = get_option( 'alt_opt_n' );
		  			  	$row = "<tr id='row-/#/alt_option/#/' style='border:solid grey 1px !important;' class='unsortable' >";
		  			  	$opt_name = "<th> <b>{$alt}</b> </th>";
		  			  }else{
		  			  	$row = "<tr id='row-{$key}' style='border:solid grey 1px !important;'>"; //u normální možnosti - se uloží do id řady klíč hodnoty
		  			  	$opt_name = "<th> {$value} |  <a class='edit_this_opt' id='{$key}' style='cursor:pointer;' >Edit</a> </th>"; //u normální možnosti -  se přidá možnost editace pomocí klíče
		  			  }

					  $order = $key+1;	//pro výpis pořadí které nezačíná nulou
					  echo $row; //pro měnění pořadí
					  echo "<th> <input type='checkbox' name='opt[]' value='{$key}'></th>"; //checkbox s hodnotami příslušné možnosti
					  echo "<th> {$order} </th>"; // upravené pořadové číslo možnosti	

					  //**************Editing option
					  if(isset($edit_id)){		
						   if($edit_id==$key){
						     		echo "<th></form>
						     			 <form method='post' id='option_editation' class='option_editation' >
										    <input type='text' name='option_edit' value='{$value}' class='option_edit' >
										    <input type='text' name='key_edit' value='{$key}' class='key_edit' style='visibility: hidden;' >
										    <input type='submit' name='action' value='Submit editation' form='option_editation' class='button tagadd option_editation_submit' />
										 </form>

						     		     <form method='post' id='check_action' class='check_action' ></th>";
						   }else{
								echo $opt_name;//název možnosti
						   }
					  }else{
							echo $opt_name;//název možnosti
					  }	

					  echo "</tr>";
					}
		  	}

		  	if(!empty($_SESSION["options"])){ //pokud nexistuje žádná možnost v anketě - nezobrazí se tabulka
		  ?>
	  	<tfoot>
	  		<tr>
	 			<th>
	 				<input type='checkbox' name='tick_em_all' class='tick_em_all' >
	 			</th>
	 			<th>ID</th>
	 			<th>Name</th>
	 		</tr>
	  	</tfoot>
  	</table>
  	</form>
  	<?php }else{
  		echo '<input type="submit" value="Add option" class="button tagadd" form="survey_option">';//přidá zpět talčítko
  	}
}

//********************************* VYTVOŘENÍ ANKETY

function create_new_survey() {

	   if(isset($_SESSION["options"])){ //pokud neexistuje session s možnostmi vytvoří se pro ní pole pokud existuje tak se její data vloží do proměné options
			$options = $_SESSION["options"];
	   }else{
			$options = array() ;
	   }

	  if(!empty($_POST['name'])){ //získání názvu ankety z formluáře
	    $name = $_POST['name'];
	  }

	  if(isset($_POST['opt_answ'])){ //získání počtu zaškrtnutelných odpovědí z formluáře
	    $opt_answ = $_POST['opt_answ'];
	  }

	  if(isset($name)){ // vytvoření pro kódu přívětivého názvu pro použití u vkládání anket do článků aby uživatel věděl jakou anketu použil
	  	$slug_name = sanitize_title($name); //vytvoří název nového slugu
	  	//$wpdb->get_var( "SELECT name_slug FROM $table_name ORDER BY id DESC LIMIT 1" ); //načte poslední vytvořený slug z DB
	  }

	  $order = count($options);

	  //inserting data into database
	 if((!empty($name))AND($order>=2)) { //pokud je zadané jméno a existují alespoň možnosti
	 	global $wpdb; 

	 	$opt_count = count($options); //spočítá možnosti

		$table_name = $wpdb->prefix . "surveys";
	    $opt_array = implode("-/separator/-", $options); //formátování pole pro uložení do db - získání zpět => $options = explode("-/separator/-", $opt_array);

	    $vote_array = array(); //připraví pole

	    for($i=0; $i < $opt_count; $i++){ //vygeneruje pole nul podle počtu možnosti pro připravení na počítání hlasů
	    	$vote_array[] = "0"; 
	    }

	    $vote_array = implode("-/separator/-", $vote_array);//vytvoří string pro DB

		$wpdb->insert($table_name, array(
		   "name" => $name,
		   "name_slug" => $slug_name,
		   "opt_answ" => $opt_answ,
		   "opt_array" => $opt_array,
		   "vote_array" => $vote_array,
		));

	    session_unset();  //zničí pole
	    session_destroy(); //zničí pole

	    echo "<p class='survey_warning' style='display:none;' >data_sent</p>"; //pro jednoduché předání dat AJAXU

	 }elseif(empty($name)){
	 	echo "<p class='survey_warning' >Survey has to have name</p>"; //pokud nemá název
	 } elseif($order<=2){
	 	echo "<p class='survey_warning' >Survey has to have at least 2 options</p>"; //pokud v poli nejsou 2 a více hodnot
	 }
  
}

?>