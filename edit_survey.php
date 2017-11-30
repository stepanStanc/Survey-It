<?php

	/***************************************************************** editace zvolené ankety
	*
	******************************tento soubor je includovan do "main_page.php" **********************
	*
	*/

	//načtení ankety

	$id = $_GET['survey_id'];

	$table_name = $wpdb->prefix . "surveys";
	$data = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = $id", ARRAY_A ); //načte řádek z DB

	$name = $data['name']; //získá název aktuální ankety
	$slug = $data['name_slug']; //získá slug název aktuální ankety
	$opt_arr = $data['opt_array']; //získá pole možností aktuální ankety
	$vote_arr = $data['vote_array']; //získá pole hlasů aktuální ankety
	$opt_answ = $data['opt_answ']; //získá počet možných hlasů aktuální ankety

	$options_db = explode("-/separator/-", $opt_arr); // znovu vytvoří pole

	$_SESSION["options"] = $options_db;


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
	  <input type="text" name="name" class="survey-name-input name main_survey_input" value="<?php echo $name; ?>" ><br>
	  <label>Allowed votes per person :</label><br>
	  <select name="opt_answ" class="opt_answ" >
	  		 <?php
	  		 echo "<option value='{$opt_answ}' > {$opt_answ} </option> "; 
	  		 for($i=1; $i <= get_option( 'max_check_c' ); $i++){
				      echo " <option value='{$i}' > {$i} </option> ";
			    }
             ?>
      </select><br>
     </form>

     <form method="post" id="survey_option" class="survey_option" >
  	  	<label>Option (max. 20) :</label><br>
	    <input type="text" name="option" class="opt_survey_input" >
	 </form>

	 <div class='options_table' id='options_table' > <!-- tabulka možností generovaná ajaxem (elementy uvnitř se nahradí jakmile se spustí ajax) -->
	 	<?php
		 	manage_new_survey_options(false,NULL,NULL);
		 ?>
	 </div> 

	 <div class="tablenav bottom" >
	 	<p class="survey_warning" title="Users/visitors who voted in this survey will be again able to vote" >Warning! If you change survey all current votes will be deleted! </p>	
	 	<input type="submit" name="change_survey" value="Change survey" class="button button-primary button-large change_survey" form="survey_main" id="<?php echo $id; ?>" ><br>
	 </div>

	<?php

	//funkce jsou v souboru "functions.php" jelikož tato část PHP se spustí jedině při refreshi (je načtená přes GET)
	//ostatní funkce jsou v souboru "new_survey.php" - editace má stejné funkce jako vytváření anket
	
?>