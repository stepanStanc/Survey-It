<?php

  /********************************************************************************************************************************************************************
  **************************************************************** SHORTCODY A AKCE NAPOJUJÍCÍ FUNKCE *****************************************************************
  *********************************************************************************************************************************************************************/

  function create_menu_survey() {
      if( current_user_can('editor') || current_user_can('author') || current_user_can('administrator') ){
          add_menu_page('Survey it!', 'Survey it!', 'manage_options', 'survey_it', 'main_page',plugin_dir_url( __FILE__ ) . 'icon.png');    //funkce vyvolaná na stránku: "main_page"   ze souboru "main_page.php"
          add_submenu_page('survey_it', 'New surevy!', 'New survey!', 'manage_options', 'new_survey', 'new_survey');                        //funkce vyvolaná na stránku: "new_survey"    ze souboru "new_survey.php"
          add_submenu_page('survey_it', 'Survey results!', 'Survey results!', 'manage_options', 'survey_results', 'survey_results');        //funkce vyvolaná na stránku: "survey_results" ze souboru "survey_results.php"
          add_options_page('Survey it! settings', 'Survey it! settings', 'manage_options', 'survey_it_settings', 'survey_it_settings');     //nastavení Survey it
      }
  }

  function add_survey_it_script() { 
      wp_register_script( "survey_it_script", plugin_dir_url( __FILE__ ) . 'js/script.js', array('jquery') ); //!!!! musí být zaregistrován  kvůli ajaxu
      wp_localize_script( 'survey_it_script', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));   

      wp_enqueue_script('jquery'); 
      
      wp_enqueue_script( 'survey_it_script', plugin_dir_url( __FILE__ ) . 'js/script.js');
      wp_enqueue_style(  'survey_it_style', plugin_dir_url( __FILE__ ) . 'css/style.css');
  }

  function add_survey_it_admin_script( ) { 
      wp_enqueue_script( 'jquery');
      wp_enqueue_script( 'jquery-ui-sortable'); // jQuery User Interface (jquery efekty) - měnění pořadí
      wp_enqueue_script( 'survey_it_admin_script', plugin_dir_url( __FILE__ ) . 'js/wp-admin-script.js');
      wp_enqueue_style(  'survey_it_admin_style', plugin_dir_url( __FILE__ ) . 'css/wp-admin-style.css');
  }

  function survey_it_buttons() {
      add_filter("mce_external_plugins", "survey_it_plugin_scripts");
      add_filter("mce_buttons", "survey_it_buttons_editor");
  }

  function survey_it_plugin_scripts($plugin_array){
      $plugin_array["survey_it"] =  plugin_dir_url(__FILE__) . "js/tiny_script.js"; //js tiny soubor
      return $plugin_array;
  }

  function survey_it_buttons_editor($buttons){
      array_push($buttons, "add_survey"); //tiny buttony
      array_push($buttons, "add_survey_results"); //tiny buttony
      return $buttons;
  }

  function survey_it_before_wp_tiny_mce() { //předávání php kódu před Tiny editor
      include "before_tiny_mce.php"; //kód co se vygeneruje před tiny pro vkládání shortcodu
  }

  function register_survey_widgets() {
      register_widget( 'Survey_Widget' );
      register_widget( 'Survey_Results_Widget' );
  }

  /********************************************************************************************************************************************************************
  **************************************************************** VLASTNÍ FUNKCE *************************************************************************************
  *********************************************************************************************************************************************************************/

  function unserialize_checkbox_form($str) { //vytvoří znovu post data ze sringu generovaným  jQuery funkcí serialize (výsledek bude očíslovaný postupně od 0 do x)
    $returndata = array();
    $strArray = explode("&", $str);
    $i = 0;
    foreach ($strArray as $item) {
        $array = explode("=", $item);
        $val =  $array[1];
        $returndata[$i] = $val;
        $i++;
    }

    return $returndata;
  }

  function options_compare($opt_1, $opt_2) { // funkce detekující shodu dvou stringů(vzájemně - první s druhým a druhý s prvním zprůměruje) (nejlépe funguje pro AJ.) - Př. ("orange" se shoduje s "ORANGES")  

     $opt_1 = transliterateString($opt_1); //pro zpřesnění "similar_text" a "levenshtein" - přemění speciální znaky na normální
     $opt_2 = transliterateString($opt_2); //pro zpřesnění "similar_text" a "levenshtein" - přemění speciální znaky na normální

  	 $opt_1 = strtolower($opt_1); //pro zpřesnění "similar_text" a "levenshtein" - vše malé
  	 $opt_2 = strtolower($opt_2); //pro zpřesnění "similar_text" a "levenshtein" - vše malé

  	 $opt_1 = preg_replace('/\s+/', ' ',$opt_1); //pro zpřesnění "similar_text" a "levenshtein" - smaže skupiny mezer ze stringu (zůstane vždy max. jedna mezera)
  	 $opt_2 = preg_replace('/\s+/', ' ',$opt_2); //pro zpřesnění "similar_text" a "levenshtein" - smaže skupiny mezer ze stringu (zůstane vždy max. jedna mezera)

     $opt_1 = stripslashes($opt_1); //pro zpřesnění "similar_text" a "levenshtein" - smaže lomítka ze stringu
     $opt_2 = stripslashes($opt_2); //pro zpřesnění "similar_text" a "levenshtein" - smaže lomítka ze stringu

     $opt_1 = rtrim($opt_1," "); //odstraní mezeru z konce stringu (pokud existuje) - pro zpřesnění "$plural"
     $opt_2 = rtrim($opt_2," "); //odstraní mezeru z konce stringu (pokud existuje) - pro zpřesnění "$plural"

     $opt_1 = str_replace("'", "", str_replace('"', "",$opt_1)); //odstraní uvozovky - pro zpřesnění "$plural" a "similar_text" a "levenshtein"
     $opt_2 = str_replace("'", "", str_replace('"', "",$opt_2)); //odstraní uvozovky - pro zpřesnění "$plural" a "similar_text" a "levenshtein"

  	 $plural = (substr($opt_1, -1) == "s")OR(substr($opt_2, -1) == "s"); //pro detekci množného čísla - při pokročilejší detekci začíná být značně složitější hlavně u jiných jazyků než Aj.

	   //******************* porovnání s první možností ***************************
  	 similar_text($opt_1,$opt_2, $similar_text_result); 	    //čím větší tím lepší shoda
  	 $levenshtein_result =levenshtein($opt_1,$opt_2,0,10,10); //čím menší tím lepší shoda

  	 if ($plural) { //pokud je množné číslo na konci stringu
  	 	 $result_1 = ($similar_text_result - $levenshtein_result) + 10; // +10 přidává shodu jestli je hodnota v množném čísle 
  	 }else{
  	 	 $result_1 = ($similar_text_result - $levenshtein_result); //pokud levenshtein detekuje prohozené nebo chybějící písmeno tak zmenší slimilar_text
  	 }

  	 //******************* porovnání s druhou možností ****************************
  	 similar_text($opt_2,$opt_1, $similar_text_result); 	    //čím větší tím lepší shoda
  	 $levenshtein_result =levenshtein($opt_2,$opt_1,0,10,10); //čím menší tím lepší shoda

  	 if ($plural) { //pokud je množné číslo na konci stringu
  	 	 $result_2 = ($similar_text_result - $levenshtein_result) + 10; // +10 přidává shodu jestli je hodnota v množném čísle 
  	 }else{
  	 	 $result_2 = ($similar_text_result - $levenshtein_result); //pokud levenshtein detekuje prohozené nebo chybějící písmeno tak zmenší slimilar_text
  	 }

  	 //****************** výsledek **************************

  	 $result = ($result_1 + $result_2) / 2; 
     //propojení obou výsledků (za potřebí protože obě funkce ("similar_text" a "levenshtein") porovnávají hodnoty 1. ku 2. ne vzájemně)
  	 
  	 if($result>79){ //pokud je shoda lepší než 80 tak se vrátí pravda(shodné)
  	 	$result = true;
  	 }else{
  	 	$result = false;
  	 }		 

  	 return $result; //vrací "true" pokud jsou shodné pokud ne tak "false"
  }

  function transliterateString($txt) { //mění speciální znaky na normální 
    $transliterationTable = array('á' => 'a', 'Á' => 'A', 'à' => 'a', 'À' => 'A', 'ă' => 'a', 'Ă' => 'A', 'â' => 'a', 'Â' => 'A', 'å' => 'a', 'Å' => 'A', 'ã' => 'a', 'Ã' => 'A', 'ą' => 'a', 'Ą' => 'A', 'ā' => 'a', 'Ā' => 'A', 'ä' => 'ae', 'Ä' => 'AE', 'æ' => 'ae', 'Æ' => 'AE', 'ḃ' => 'b', 'Ḃ' => 'B', 'ć' => 'c', 'Ć' => 'C', 'ĉ' => 'c', 'Ĉ' => 'C', 'č' => 'c', 'Č' => 'C', 'ċ' => 'c', 'Ċ' => 'C', 'ç' => 'c', 'Ç' => 'C', 'ď' => 'd', 'Ď' => 'D', 'ḋ' => 'd', 'Ḋ' => 'D', 'đ' => 'd', 'Đ' => 'D', 'ð' => 'dh', 'Ð' => 'Dh', 'é' => 'e', 'É' => 'E', 'è' => 'e', 'È' => 'E', 'ĕ' => 'e', 'Ĕ' => 'E', 'ê' => 'e', 'Ê' => 'E', 'ě' => 'e', 'Ě' => 'E', 'ë' => 'e', 'Ë' => 'E', 'ė' => 'e', 'Ė' => 'E', 'ę' => 'e', 'Ę' => 'E', 'ē' => 'e', 'Ē' => 'E', 'ḟ' => 'f', 'Ḟ' => 'F', 'ƒ' => 'f', 'Ƒ' => 'F', 'ğ' => 'g', 'Ğ' => 'G', 'ĝ' => 'g', 'Ĝ' => 'G', 'ġ' => 'g', 'Ġ' => 'G', 'ģ' => 'g', 'Ģ' => 'G', 'ĥ' => 'h', 'Ĥ' => 'H', 'ħ' => 'h', 'Ħ' => 'H', 'í' => 'i', 'Í' => 'I', 'ì' => 'i', 'Ì' => 'I', 'î' => 'i', 'Î' => 'I', 'ï' => 'i', 'Ï' => 'I', 'ĩ' => 'i', 'Ĩ' => 'I', 'į' => 'i', 'Į' => 'I', 'ī' => 'i', 'Ī' => 'I', 'ĵ' => 'j', 'Ĵ' => 'J', 'ķ' => 'k', 'Ķ' => 'K', 'ĺ' => 'l', 'Ĺ' => 'L', 'ľ' => 'l', 'Ľ' => 'L', 'ļ' => 'l', 'Ļ' => 'L', 'ł' => 'l', 'Ł' => 'L', 'ṁ' => 'm', 'Ṁ' => 'M', 'ń' => 'n', 'Ń' => 'N', 'ň' => 'n', 'Ň' => 'N', 'ñ' => 'n', 'Ñ' => 'N', 'ņ' => 'n', 'Ņ' => 'N', 'ó' => 'o', 'Ó' => 'O', 'ò' => 'o', 'Ò' => 'O', 'ô' => 'o', 'Ô' => 'O', 'ő' => 'o', 'Ő' => 'O', 'õ' => 'o', 'Õ' => 'O', 'ø' => 'oe', 'Ø' => 'OE', 'ō' => 'o', 'Ō' => 'O', 'ơ' => 'o', 'Ơ' => 'O', 'ö' => 'oe', 'Ö' => 'OE', 'ṗ' => 'p', 'Ṗ' => 'P', 'ŕ' => 'r', 'Ŕ' => 'R', 'ř' => 'r', 'Ř' => 'R', 'ŗ' => 'r', 'Ŗ' => 'R', 'ś' => 's', 'Ś' => 'S', 'ŝ' => 's', 'Ŝ' => 'S', 'š' => 's', 'Š' => 'S', 'ṡ' => 's', 'Ṡ' => 'S', 'ş' => 's', 'Ş' => 'S', 'ș' => 's', 'Ș' => 'S', 'ß' => 'SS', 'ť' => 't', 'Ť' => 'T', 'ṫ' => 't', 'Ṫ' => 'T', 'ţ' => 't', 'Ţ' => 'T', 'ț' => 't', 'Ț' => 'T', 'ŧ' => 't', 'Ŧ' => 'T', 'ú' => 'u', 'Ú' => 'U', 'ù' => 'u', 'Ù' => 'U', 'ŭ' => 'u', 'Ŭ' => 'U', 'û' => 'u', 'Û' => 'U', 'ů' => 'u', 'Ů' => 'U', 'ű' => 'u', 'Ű' => 'U', 'ũ' => 'u', 'Ũ' => 'U', 'ų' => 'u', 'Ų' => 'U', 'ū' => 'u', 'Ū' => 'U', 'ư' => 'u', 'Ư' => 'U', 'ü' => 'ue', 'Ü' => 'UE', 'ẃ' => 'w', 'Ẃ' => 'W', 'ẁ' => 'w', 'Ẁ' => 'W', 'ŵ' => 'w', 'Ŵ' => 'W', 'ẅ' => 'w', 'Ẅ' => 'W', 'ý' => 'y', 'Ý' => 'Y', 'ỳ' => 'y', 'Ỳ' => 'Y', 'ŷ' => 'y', 'Ŷ' => 'Y', 'ÿ' => 'y', 'Ÿ' => 'Y', 'ź' => 'z', 'Ź' => 'Z', 'ž' => 'z', 'Ž' => 'Z', 'ż' => 'z', 'Ż' => 'Z', 'þ' => 'th', 'Þ' => 'Th', 'µ' => 'u', 'а' => 'a', 'А' => 'a', 'б' => 'b', 'Б' => 'b', 'в' => 'v', 'В' => 'v', 'г' => 'g', 'Г' => 'g', 'д' => 'd', 'Д' => 'd', 'е' => 'e', 'Е' => 'E', 'ё' => 'e', 'Ё' => 'E', 'ж' => 'zh', 'Ж' => 'zh', 'з' => 'z', 'З' => 'z', 'и' => 'i', 'И' => 'i', 'й' => 'j', 'Й' => 'j', 'к' => 'k', 'К' => 'k', 'л' => 'l', 'Л' => 'l', 'м' => 'm', 'М' => 'm', 'н' => 'n', 'Н' => 'n', 'о' => 'o', 'О' => 'o', 'п' => 'p', 'П' => 'p', 'р' => 'r', 'Р' => 'r', 'с' => 's', 'С' => 's', 'т' => 't', 'Т' => 't', 'у' => 'u', 'У' => 'u', 'ф' => 'f', 'Ф' => 'f', 'х' => 'h', 'Х' => 'h', 'ц' => 'c', 'Ц' => 'c', 'ч' => 'ch', 'Ч' => 'ch', 'ш' => 'sh', 'Ш' => 'sh', 'щ' => 'sch', 'Щ' => 'sch', 'ъ' => '', 'Ъ' => '', 'ы' => 'y', 'Ы' => 'y', 'ь' => '', 'Ь' => '', 'э' => 'e', 'Э' => 'e', 'ю' => 'ju', 'Ю' => 'ju', 'я' => 'ja', 'Я' => 'ja');
    return str_replace(array_keys($transliterationTable), array_values($transliterationTable), $txt);
  }


  /********************************************************************************************************************************************************************
  **************************************************************** AJAX PRO EDITACI ANKETY ****************************************************************************
  *********************************************************************************************************************************************************************/

  //********************** ZMĚNĚNÍ ANKETY

  function survey_it_change_survey() {
    global $wpdb; // this is how you get access to the database

    $id = $_POST['survey_id'];

    edit_survey($id);
    manage_new_survey_options(false,NULL,NULL);

    wp_die(); // zapotřebí pro správnou funkčnost
  }

  function edit_survey($id) {

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
    }

    $order = count($options);

    //inserting data into database
   if((!empty($name))AND($order>=2)) { //pokud je zadané jméno a existují alespoň možnosti
    global $wpdb; 

    $opt_count = count($options); //spočítá možnosti

    $table_name = $wpdb->prefix . "surveys";
      $opt_array = implode("-/separator/-", $options); //formátování pole pro uložení do db - získání zpět => $options = explode("-/separator/-", $opt_array);

      $vote_array = array(); //připraví pole

      $vote_other = null; //vymaže všechny

      for($i=0; $i < $opt_count; $i++){ //vygeneruje pole nul podle počtu možnosti pro připravení na počítání hlasů
        $vote_array[] = "0"; 
      }

      $vote_array = implode("-/separator/-", $vote_array);//vytvoří string pro DB

      $wpdb->update($table_name, array(
         "name" => $name,
         "name_slug" => $slug_name,
         "opt_answ" => $opt_answ,
         "opt_array" => $opt_array,
         "vote_array" => $vote_array,
         "vote_other" => $vote_other,
      ),array( 'id' => $id ) );

      $table_name_v = $wpdb->prefix . "votes";

      $ids = $wpdb->get_col( "SELECT id FROM $table_name_v WHERE survey_id = $id");//získá všechny ověření hlasů pro tuto anketu

      foreach($ids as $id){ //všechny smaže
         $wpdb->delete( $table_name_v, array( 'id' => $id ) );
      }

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