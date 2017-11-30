<?php

function main_page() { //funkce na stránce pluginu
   global $wpdb;

   if(!(isset($_GET['survey_id'])AND(isset($_GET['survey_action'])))){ //pokud je kliknuto na odkaz pluginem generovaný odkaz

   	  //***************** vynulování možností pro editaci nebo vytváření

   	  if(isset($_GET['survey_pagination'])){

		   	$options = NULL;
		   	session_unset();  
		   	session_destroy();

		   	//****************************** práce s checkboxy

		    $opt = $_POST['opt'];//získání zaškrtnutých checkboxů

	    	if((!empty($opt))AND(!($_POST['to_do_select']=="NULL"))){
		    	foreach ($opt as $id) { //získá id zaškrtnutého
		    			// *******mazání
			     		if($_POST['to_do_select']=="delete"){
			     			$table_name = $wpdb->prefix . "surveys";
   							$wpdb->delete( $table_name, array( 'id' => $id )); //mazání dané řádky z DB
			     		}
			     		// *******další akce zde
			    }
	    	}

	    	//************************ konec práce s checkboxy

		   	$p = get_option( 'max_s_c_m' );//příspěvků na stránce

		    //loading data for table
		   	$table_name = $wpdb->prefix . "surveys";

		   	$limit = (($_GET['survey_pagination'])-1)*$p . "," . $p; //první hodnota je začátek výpisu a druhá je počet výsledků

		   	$all_surveys_c = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name"); //počet všech anket
		   	$ids = $wpdb->get_col( "SELECT id FROM $table_name ORDER BY id DESC LIMIT $limit");//získá  id z wpdb anket
		   	$surveys_count = count($ids);


		   	//********************** GENERUJE STRÁNKOVÁNÍ

			$adress = get_site_url(); //načte adresu webu na kterém je plugin					
		    $paging_adress = $adress . "/wp-admin/admin.php?page=survey_it&survey_pagination="; //odkaz pro stránkvání bez čísla stránky

		    $c_page = $_GET['survey_pagination']; //současná strana
		    $page_c = ceil($all_surveys_c/$p); //počet stran

		    $l_page = $paging_adress . $page_c; //poslední strana
		    $f_page = $paging_adress . 1; //první strana
		    $p_page = $paging_adress . (($c_page==1) ? $c_page : (($_GET['survey_pagination'])-1)); //předchozí strana
		    $n_page = $paging_adress . (($c_page==$page_c) ? $c_page : (($_GET['survey_pagination'])+1)); //další strana


		    if(is_admin()){
		   ?>

		   		<div id="screen-meta-links" >
			   		<div id="screen-options-link-wrap" class="screen-meta-toggle">
							<a class="setting_survey_it_link" href="<?php echo get_site_url() . '/wp-admin/options-general.php?page=survey_it_settings';?>" > Survey it! settings </a>
					</div>
				</div>
			<?php } ?>
		   		<div class="wrap">
		   			<h1>Survey it! <a href="<?php bloginfo('wpurl'); echo "/wp-admin/admin.php?page=new_survey"; ?>" class="page-title-action">Create survey</a> </h1>
		   			<ul class="subsubsub">
					    <?php
					  		echo "<li class='all'>Total <span class='count'>({$all_surveys_c})</span>"; //výpis počtu anket
					  		echo "</li></ul>"; 
					   //******************* start of table	and form for table	
					   if($surveys_count>0){ //pokud existují nějaké ankety
					    ?>
					<form method='post' name="do" >    
						<div class="tablenav top" >
								<select name="to_do_select" >
								  <option value="NULL" >Choose action for selected</option>
								  <option value="delete">Delete selected from DB</option>
								</select>
								<input type="submit" value="Do it!" onclick="submit_chosen_ones()" class="button tagadd" />
								<div class="tablenav-pages">
									<span class="pagination-links">
										<a href="<?php echo $f_page; ?>">
											<span> « </span>
										</a>
										<a href="<?php echo $p_page; ?>">
											<span> ‹ </span>
										</a>

										<span><?php echo "{$c_page} of $page_c"; ?></span>
										
										<a href="<?php echo $n_page ?>">
											<span> › </span>
										</a>
										<a href="<?php echo $l_page; ?>">
											<span> » </span>
										</a>
									</span>
								</div>
						</div>

						<table class="wp-list-table widefat fixed striped posts" >
					 		<thead>
						 		<tr>
						 			<td id='cb' class='manage-column column-cb check-column' >
						 				<input type='checkbox' name='tick_em_all' class='tick_em_all' >
						 			</th>
						 			<th class='manage-column column-title column-primary ' >Name</th>
						 			<th class="manage-column ">Options</th>
						 			<th class="manage-column ">Votes per person</th>
						 			<th class="manage-column ">Votes total</th>
						 			<th class="manage-column ">Most voted option</th>
						 		</tr>
					 		 </thead>
							  <?php
							  
							  		//************************actions inside table

							  		foreach($ids as $key => $id){

							  			$data = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = $id", ARRAY_A ); //načte řádek z DB
							  			$name = $data['name']; //získá název aktuální ankety
							  			$slug = $data['name_slug']; //získá slug název aktuální ankety
							  			$opt_arr = $data['opt_array']; //získá pole možností aktuální ankety
							  			$vote_arr = $data['vote_array']; //získá pole hlasů aktuální ankety
							  			$opt_answ = $data['opt_answ']; //získá počet možných hlasů aktuální ankety
										$vote_other = $data['vote_other']; //získá alertnativní odpovědi

							  			$options = explode("-/separator/-", $opt_arr); // znovu vytvoří pole
							  			$votes = explode("-/separator/-", $vote_arr); // znovu vytvoří pole

							  			if(empty($vote_other)){ //načte pole alt.hlasů pokud existují pokud ne založí prázdné pole
							  				$vote_other_arr = array();
							  			}else{
							  				$vote_other_arr = explode("-/separator/-", $vote_other);
							  			}
							  			
								  		echo "<tr><td> <input type='checkbox' name='opt[]' value='{$id}'></td>"; //checkbox s id příslušné možnosti

								  		$edit_adress = $adress . "/wp-admin/admin.php?page=survey_it&survey_id={$id}&survey_action=edit";//adresa pro úpravu přes get
								  		$delete_adress = $adress . "/wp-admin/admin.php?page=survey_it&survey_id={$id}&survey_action=delete&survey_pagination={$c_page}";//adresa pro úmazání článku přes get
								  		?>

								  		<th class='title column-title has-row-actions column-primary page-title' >
						  		 			<a href='<?php echo $edit_adress; ?>'> <strong> <?php echo $name; ?> </strong> </a>
						  		 			<div class='row-actions' >
						  		 				<span class='edit'>
							  		 				<a href='<?php echo $edit_adress; ?>'>
							  		 					Edit
							  		 				</a> | 
						  		 				</span>

						  		 				<span class='trash'>
							  		 				<a href='<?php echo $delete_adress; ?>'>
							  		 					Delete
							  		 				</a> | 
						  		 				</span>
						  		 			</div>
							  			  </th>

								  		<?php //jméno ankety odkazuje na editaci ankety

								  		$opt_string = null; //vynuluje minulý

								  		foreach($options as $option){ //postupně přidá všechny možnosti do stringu "opt_string"
								  			if($option=='/#/alt_option/#/'){ //pokud je to alternarivní možnost tak se to napíše
								  				$alt = get_option( 'alt_opt_n' );
											  	$option = "<b>{$alt}</b>";
											}
								  			$opt_string .= "{$option}, ";
								  		}

			    						$opt_string = rtrim($opt_string, ', '); //  odstraní poslední čárku
			    						$opt_string = wp_trim_words( $opt_string, 20, '...' ); //wp funkce na omezení stringu na x (20) slov případné zakončení třemi tečkami (pokud je delší než zadaný počet slov)
								  		echo "<th >{$opt_string}</th>";
								  		echo "<th >{$opt_answ}</th>";

								  		$vote_count = null; //vynuluje minulý
								  		$most_votes = array(0,0); //předpřipraví pole pro porovnávání
								  		foreach($votes as $vote_key => $vote){ //spočítá hlasy a zjistí nejvyšší počet hlasů
								  			$vote_count += $vote;

								  			if($vote>$most_votes["1"]){ //pokud se počet hlasů rovná víc než aktuální tak se za něj prohodí a uloží se i jeho klíč
								  				$most_votes = array($vote_key,$vote);
								  			}
								  		}

								  		$alt_votes = count($vote_other_arr); //spočíta alt. hlasy

			  							$vote_count += $alt_votes; //přičte alt.hlasy

								  		$most_votes_key = $most_votes[0]; //klíč nejvíce hlasů = klíč možnosti s nejvtším počtem hlasů
								  		$most_votes = $most_votes[1];  //vytáhne počet hlasů
								  		$most_voted_opt = $options[$most_votes_key]; //získá možnost podle klíče

								  		if($most_votes==1){ 									//jeden hlas
								  			$v = "{$most_voted_opt} <b>with</b> {$most_votes} vote";
								  		}elseif($most_votes==0){ 								//žádné hlasy
								  			$v = "none votes yet";
								  		}else{ 													//více hlasů
								  			$v = "{$most_voted_opt} <b>with</b> {$most_votes} votes";
								  		}

								  		echo "<th >{$vote_count}</th>";
								  		echo "<th >{$v}</th>";
								  		echo "</tr>";
			   						}


							  ?>
						  	<tfoot>
						  		<tr>
						  			<td id='cb' class='manage-column column-cb check-column' >
						 				<input type='checkbox' name='tick_em_all' class='tick_em_all' >
						 			</th>
						 			<th class="manage-column column-title column-primary " >Name</th>
						 			<th class="manage-column ">Options</th>
						 			<th class="manage-column ">Votes per person</th>
						 			<th class="manage-column ">Votes total</th>
						 			<th class="manage-column ">Most voted option</th>
						 		</tr>
						 	</tfoot>
					  	</table><br>
				  	</form>
				    <?php
				    }else{
				    	echo '<br> <p class="survey_warning" >There are no surveys yet </p>';	
				    }
				    //****************************** end of table

				    ?>
		   		</div>
		   <?php
		}
	}elseif($_GET['survey_action']=="edit"){ //pokud se má editovat

		include 'edit_survey.php'; //script pro editování jedné zvolené(GET) ankety

	}elseif($_GET['survey_action']=="delete"){ //pokud se má mazat

		$del_id = $_GET['survey_id']; //získá id mazané ankety
		
		$table_name = $wpdb->prefix . "surveys";
	   	$wpdb->delete( $table_name, array( 'id' => $del_id )); //mazání dané řádky z DB

	   	$adress = get_site_url(); //načte adresu webu na kterém je plugin	
	   	$paging_adress = $adress . "/wp-admin/admin.php?page=survey_it&survey_pagination="; //odkaz pro stránkvání bez čísla stránky

	    $c_page = $_GET['survey_pagination']; //současná strana
	    $c_page_l =  $paging_adress . $c_page;

	   		
   			
	   	echo "<script>
	   			window.location.href = '{$c_page_l}'; 
				document.URL = '{$c_page_l}';
	   		</script>";


	}		
}

?>