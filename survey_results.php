<?php

function survey_results() {
global $wpdb;
	//loading data for table

	if(isset($_GET['survey_pagination'])){

   	  	$p = get_option( 'max_s_c_r' );//příspěvků na stránce

	    //loading data for table
	   	$table_name = $wpdb->prefix . "surveys";

	   	$limit = (($_GET['survey_pagination'])-1)*$p . "," . $p; //první hodnota je začátek výpisu a druhá je počet výsledků

	   	$all_surveys_c = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name"); //počet všech anket
	   	$ids = $wpdb->get_col( "SELECT id FROM $table_name ORDER BY id DESC LIMIT $limit");//získá všechna id z wpdb anket
	   	$surveys_count = count($ids);


	   	//********************** GENERUJE STRÁNKOVÁNÍ

		$adress = get_site_url(); //načte adresu webu na kterém je plugin					
	    $paging_adress = $adress . "/wp-admin/admin.php?page=survey_results&survey_pagination="; //odkaz pro stránkvání bez čísla stránky

	    $c_page = $_GET['survey_pagination']; //současná strana
	    $page_c = ceil($all_surveys_c/$p); //počet stran

	    $l_page = $paging_adress . $page_c; //poslední strana
	    $f_page = $paging_adress . 1; //první strana
	    $p_page = $paging_adress . (($c_page==1) ? $c_page : (($_GET['survey_pagination'])-1)); //předchozí strana
	    $n_page = $paging_adress . (($c_page==$page_c) ? $c_page : (($_GET['survey_pagination'])+1)); //další strana
  
  	// start of table
   if(is_admin()){
   ?>

   		<div id="screen-meta-links" >
	   		<div id="screen-options-link-wrap" class="screen-meta-toggle">
					<a class="setting_survey_it_link" href="<?php echo get_site_url() . '/wp-admin/options-general.php?page=survey_it_settings';?>" > Survey it! settings </a>
			</div>
		</div>
	<?php } ?>
	
	<div class="wrap">
		<h1>Survey results! </h1>
		<div class="tablenav top" >
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
		<ul class="subsubsub">
	    <?php
	   //******************* start of table	and form for table	
	   if($surveys_count>0){ //pokud existují nějaké ankety

			    //************************actions inside table
				$adress = get_site_url(); //načte adresu webu na kterém je plugin

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

		  			$edit_adress = $adress . "/wp-admin/admin.php?page=survey_it&survey_id={$id}&survey_action=edit";//adresa pro úpravu přes get

		  			$vote_count = NULL; //vynulování minulého

		  			foreach($votes as $vote){ //spočítá hlasy 
			  			$vote_count += $vote; //počet hlasů
			  		}

			  		$alt_votes = count($vote_other_arr); //spočíta alt. hlasy

			  		$vote_count += $alt_votes; //přičte alt.hlasy

			  		if(!($vote_count==0)){ //pokud nejsou hlasy nevypíše se

			  			?>
			  				<div class="tablenav top" >
									<?php 

										echo "<h2 title='{$opt_answ} votes allowed per user ' >";

										if($vote_count==1){
								  			$v = "{$vote_count} vote";
								  		}else{
								  			$v = "{$vote_count} votes";
								  		}

										echo "<a href='{$edit_adress}'> <strong> {$name} </strong> </a> ({$v})"; //jméno ankety odkazuje na editaci ankety 
									?>
								</h2>
							</div>
			  				<table class="wp-list-table widefat fixed striped posts" >
						 		<thead>
							 		<tr>
							 			<th class="manage-column ">Option</th>
							 			<th class="manage-column ">Votes</th>
							 		</tr>
						 		 </thead>
			  			<?php

				  		foreach($options as $opt_key => $option){ //postupně přidá všechny možnosti do stringu "opt_string"
				  			$vote = $votes[$opt_key];

				  			//**************************** výpis alt. možnosti ******************************

				  			if($option=='/#/alt_option/#/'){ //pokud je to alternarivní možnost tak se to napíše

				  				  $alt = get_option( 'alt_opt_n' );
								  $option = "<b>{$alt}</b>"; //název možnosti

								  echo  "<tr><th> {$option} </th><th>"; //název možnosti a začátek řádku

								  if($alt_votes == 0){ 
					  				  $vote_percentage = "0%";
						  		  }else{
						  			  $vote_percentage = 100*($alt_votes / $vote_count); //kolik procent celkového poču hlasů je současný hlas
						  			  $vote_percentage = round($vote_percentage,0) . "%"; //zaokrouhlení a přidáníprocenta
						  		  }

						  		  echo  "{$alt_votes} that is: <b><div class='survey_bar' >{$vote_percentage}</div> </b> <br>"; //použít js na vynásobení procent a šířky barevného sloupce zobrazující množství

								  $all_opt_votes = array(); //vynuluje minulé a předpřipraví pole

								  foreach($vote_other_arr as $opt_key => $alt_opt){ //pro kařdou alternativní možnost v DB (hodnota podle které se porovnává)

								  	  $count = 0; //vynulování minulé (počítá se od nuly protože každý přičte minimálně sebe)

								  	  foreach($vote_other_arr as $opt_key_check => $alt_opt_c){ //pro kařdou alternativní možnost v DB (porovnávací hodnota)

								  	  		if(options_compare($alt_opt, $alt_opt_c)){ //customizované porovnání jestli jsou shodné

								  	  				$last = end(array_keys($all_opt_votes)); //poslední klíč v poli (název je ukládán jako klíč)

								  	  				if(options_compare($last, $alt_opt_c)){ //pokud se poslední hodnota v poli shoduje s aktuální hodnotou tak poslední smaže
								  	  					unset($all_opt_votes[$last]);
								  	  				}

								  	  				$count++; //přičte (další shodná hodnota)
								  	  				$all_opt_votes[$alt_opt_c] = $count; //klíč v poli "$all_opt_votes" je text a hodnota je kolikrát je tento text v poli (shodný s jiným)
								  	  	
								  	  				unset($vote_other_arr[$opt_key]); 	    //unsetnutí použitých hodnot z pole
								  	  				unset($vote_other_arr[$opt_key_check]); //unsetnutí použitých hodnot z pole

								  	  		}
								  	   }
								  }

								  arsort($all_opt_votes); //seřadí hlasy od největšího po nejmenší (podle počtu)
								  $all_opt_votes = array_slice($all_opt_votes, 0, get_option( 'max_r_alt' )); //nastaví počet itemů v poli (omezení výsledků)  -  15 nejhlasovanějších možností

								  foreach($all_opt_votes as $opt_text => $opt_count){ //výpis každé možnosti a počtu navrhnutí

								  		$opt_text = transliterateString($opt_text); //znormalizuje znaky
									  	$opt_text = stripslashes($opt_text); //smaže lomítka ze stringu
									  	$opt_text = strtolower($opt_text); //zmenší všechny písmena
									  	$opt_text = ucfirst($opt_text); //zvětší první (toto formátování (a výše) je pro jednotnost hodnoty)

									  	echo "<b>{$opt_text}</b> was suggested <b>{$opt_count} time/s</b>  <br> \n"; // <br> pro odřádkování a "\n" pro hezký kód
								  }

								  echo "</th> </tr>"; //konec řádku

							//**************************** konec výpisu alt. možnosti ******************************

							}else{ //pokud není alternarivní možnost vypíše se klasická statistika

					  			if($vote==0){ 
					  				$vote_percentage = "0%";
					  			}else{
					  				$vote_percentage = 100*($vote / $vote_count); //kolik procen celkového poču hlasů je současný hlas
					  				$vote_percentage = round($vote_percentage,0) . "%"; //zaokrouhlení a přidáníprocenta
					  			}

					  			echo  "<tr><th> {$option} </th>  <th>{$vote} that is: <b> <div class='survey_bar' >{$vote_percentage}</div> </b> </th> </tr>"; //použít js na vynásobení procent a šířky barevného sloupce zobrazující množství
				  			}
				  		
				  		}

				  		?>

						  		<tfoot>
							  		<tr>
							 			<th class="manage-column ">Option</th>
							 			<th class="manage-column ">Votes</th>
							 		</tr>
							 	</tfoot>
						  	</table>
			  			<?php
				  	}else{
				  		echo "<h2><a href='{$edit_adress}'> <strong> {$name} </strong> </a> (has no votes yet)</h2>"; //jméno ankety odkazuje na editaci ankety 
				  	}
		  		}
			  		
	}else{
		echo '<br> <p class="survey_warning" >There are no surveys yet </p>';	
	}
	//end of table

	}


}

?>