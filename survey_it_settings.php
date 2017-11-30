<?php

function survey_it_settings() { //funkce na stránce pluginu
   global $wpdb;

   if ($_POST['survey_it_settings_s'] == 'Change settings') {
      $name = 'max_opt_c';
      if(isset($_POST[$name])){update_option( $name, $_POST[$name]);}

      $name = 'max_check_c';
      if(isset($_POST[$name])){update_option( $name, $_POST[$name]);}

      $name = 'alt_opt_n';
      if(isset($_POST[$name])){update_option( $name, $_POST[$name]);}

      $name = 'vote_b';
      if(isset($_POST[$name])){update_option( $name, $_POST[$name]);}

      $name = 'max_r_alt';
      if(isset($_POST[$name])){update_option( $name, $_POST[$name]);}

      $name = 'max_s_c_m';
      if(isset($_POST[$name])){update_option( $name, $_POST[$name]);}

      $name = 'max_s_c_r';
      if(isset($_POST[$name])){update_option( $name, $_POST[$name]);}

      $name = 'show_r';
      if(isset($_POST[$name])){update_option( $name, true);}else{update_option( $name, false);}

    }

   ?>
   		<div class="wrap" >
   			<h1>Survey it! settings <button name="reset" class="reset page-title-action" value="reset" >Reset to default</button> </h1>
   		</div>

   		<br>

   		<form method="post" id="survey_it_wp_options" name="survey_it_wp_options" >
   			<label>Maximal count of options in survey</label><br>
	    	<input min="2" max="999" type="number" name="max_opt_c" class="options_c" value="<?php echo get_option( 'max_opt_c' ); ?>" ><br>

	    	<label>Maximal count of checkable options in survey</label><br>
	    	<input min="1" max="999" type="number" name="max_check_c" class="options_c"  value="<?php echo get_option( 'max_check_c' ); ?>" ><br>

	    	<label>Alternative option name/text</label><br>
	    	<input type="text" name="alt_opt_n" class="options_c"  value="<?php echo get_option( 'alt_opt_n' ); ?>" ><br>

	    	<label>Vote button text</label><br>
	    	<input type="text" name="vote_b" class="options_c"  value="<?php echo get_option( 'vote_b' ); ?>"  ><br>

	    	<label>Maximal count of shown responses to aletrnaive option</label><br>
	    	<input  min="0" max="999"  type="number" name="max_r_alt" class="options_c" value="<?php echo get_option( 'max_r_alt' ); ?>" ><br>

	    	<label>Count of surveys per page on main page</label><br>
	    	<input  min="1" max="999"  type="number" name="max_s_c_m" class="options_c" value="<?php echo get_option( 'max_s_c_m' ); ?>" ><br>

	    	<label>Count of surveys per page on results page</label><br>
	    	<input  min="1" max="499"  type="number" name="max_s_c_r" class="options_c" value="<?php echo get_option( 'max_s_c_r' ); ?>" ><br>

	    	<label>Show survey results after voting</label><br>
	    	<input type='checkbox' name='show_r' class='tick_em_all' <?php if(get_option( 'show_r' )){echo 'checked';} ?> ><br><br>

	    	<input type="submit" name="survey_it_settings_s" value="Change settings" class="button button-primary button-large survey_it_settings_s">
   		</form>
   <?php
}

?>