jQuery(document).ready(function($){

	tick_em_all(); //platí pro všechny adminské stránky

	/*********************************************************************************************************************************************************************
	****************************************************************** OPAKOVANÉ FUNKCE **********************************************************************************
	**********************************************************************************************************************************************************************/

	//******************************* DRAG & DROP MĚNĚNÍ POŘADÍ - SORTABLE

	function opt_order_change(){ //musí se přidat ke všem funkcím co mnění obsah(znovunačítají tabulku)

		var tbody = $( "#options_table #sortable_table_survey_it tbody"); //pro naprosto přesnou cestu

		tbody.sortable({ // jquery ui měnění pořadí prvků v ul li nebo tabulce

			  axis: "y", //typ posouvání x/y - defaultní je do všech směrů
			  cursor: "row-resize", //cursor při posouvání
			  items: "tr:not(.unsortable)", //zakáže posouvání itemů s class unsortable
			  update: function (event, ui) { //akce po změně

			        var changed = $(this).sortable('serialize'); //získá data změny

			        $( "tr").each(function( index ) { //pokud obsahuje alt. možnost tak se přidá na konec stringu možností ("sortable('serialize')" neodesílá itemy které mají zakázané posouvání)
			        	
			        	if($( this ).attr("class")=="unsortable"){
			        		changed += '&row[]=/#/alt_option/#/';
			        	}
					  
					});

			        $.ajax({
			            type: 'POST',
			            url: ajaxurl,
			            data: {
				            action : 'survey_it_opt_order_change',
				            changed : changed,
				        },
				        success : function( response ) {
							$(".options_table").html(response);
							set_survey_sizes();
							opt_order_change();
							tick_em_all();
						}

		        	});
		      }

		});

		$( "#options_table" ).disableSelection() //vypne výběr
	}

	//************************************ NASTAVENÍ ŠÍŘKY PRVKŮ U EDITACE ANKETY
	//u ajaxu kvůli potřebě opakovat s ajaxem

	function set_survey_sizes(){ //funkce pro nastavení velkostí prvků pro vytváření tabulky na základě velikosti okna

		var width = document.body.clientWidth;

		if(width > 720){ //porovnání veloksti displeje

			window.custom_w = (width / 4); //čtvrtinová šířka okna v PX
		}else{
			window.custom_w = (width / 3); //třetinová šířka okna v PX
		}


		$( "th" ).each(function( index ) { //nastavení šířky tabulky

			//attr() - první hodnota název atributu (style,id,class...) druhá je obsah atributu --- pokud má zadanou jen první hodnotu tak vrací její obsah (styl,classu,id...)
			style = 'width:' + window.custom_w*0.7 + 'px !important';	        	
	    	$( this ).attr('style', style );//políčka tabulky

		});


		style = 'width:' + window.custom_w*2.1 + 'px !important;'; //nastavení cusrsoru zábrání jeho sekání (zustáváni jako editovací)
    	$( '#options_table #sortable_table_survey_it' ).attr('style', style );//tabulka

    	style = 'width:' + window.custom_w*1.5 + 'px !important';
    	$( '.main_survey_input' ).attr('style', style );				   	  //input pro název ankety

    	style = 'width:' + window.custom_w*0.8 + 'px !important';
    	$( '.opt_survey_input' ).attr('style', style);						  //input pro název možnosti
	}

	function tick_em_all(){ //zaškrtávání všech checkboxů

		$('table input[name=tick_em_all]').change(function() {

		    var checkboxes = $('table').find(':checkbox');

		    if($(this).is(':checked')) {
		        checkboxes.prop('checked', true);
		    } else {
		        checkboxes.prop('checked', false);
		    }

		});
	}

	function endsWith(str, suffix) { //kontroluje jestli string končí určitým stringem
	    return str.indexOf(suffix, str.length - suffix.length) !== -1; //indexof kontroluje jestli obsahuje lengh získádelku testovaného a tense vybere jako hodnota na kterou se ptá
	}


  /********************************************************************************************************************************************************************
  **************************************************************** AJAX PRO VYTVÁŘENÍ A EDITACI ANKET *****************************************************************
  *********************************************************************************************************************************************************************/

	if((endsWith(window.location.href,"page=new_survey"))||(endsWith(window.location.href,"survey_action=edit"))){ //pokud je stránka editace nebo vytváření ankety

		opt_order_change(); //načtení pro normální stránku - pustí se jednou/refreshi
		set_survey_sizes(); //při načtení dokumentu

		//*************************************** PŘIDÁNÍ MOŽNOSTI
		
		$('.survey_option').on('submit', function (e) { 

		      e.preventDefault(); //zabrání refreshi stránky

		      //var form_data = $('.from_name').serialize(); - smáčknutí všech dat z formuláře do jedné proměné
		      
			  $.ajax({ 
					url : ajaxurl, //odkazuje na "wp-admin/admin-ajax.php" které ajax nasměruje na všechny adminské stránky
					type : 'post', //forma přenosu dat
					data : {
						action : 'survey_it_add_option', //php funkce přidaná akcí (název akce)
						option: $('.survey_option').find('input').val()
					},
					success : function( response ) { //po spracování dat v PHP se vloží do DIVu (options_table) přijatá data
						$(".options_table").html(response);
						set_survey_sizes();
						opt_order_change();
						tick_em_all();
					}
			 });

		     $('.survey_option').children('input').val(''); //vyprázdní inputy

	    });

	    //******************************************* PŘIDÁNÍ ALTERNATIVNÍ MOŽNOSTI

	    $('#options_table').on('click', '.add_alt_opt', function(event) {  

	    	event.preventDefault();
		      
			$.ajax({
					url : ajaxurl,
					type : 'post',
					data : {
						action : 'survey_it_add_alt_opt'
					},
					success : function( response ) {
						$(".options_table").html(response);
						set_survey_sizes();
						opt_order_change();
						tick_em_all();
					}
			});
	    });

	    //***************************************** MAZÁNÍ VYBRANÉ MOŽNOSTI

	    $('#options_table').on('click', '.delete_selected_opts', function(event) {  

	    	event.preventDefault();

	    	var checked = $('#options_table .check_action').serialize();
		      
			$.ajax({
					url : ajaxurl,
					type : 'post',
					data : {
						action : 'delete_selected_survey_options',
						checked : checked
					},
					success : function( response ) {
						$(".options_table").html(response);
						set_survey_sizes();
						opt_order_change();
						tick_em_all();
					}
			});
	    });

	    //********************************************* ZVOLENÍ MOŽNOSTI NA EDITOVÁNÍ

	    $('#options_table').on('click', '.edit_this_opt', function(event) {  

	    	event.preventDefault();

	    	var edit_id = $( this ).attr("id");
		      
			$.ajax({
					url : ajaxurl,
					type : 'post',
					data : {
						action : 'survey_it_edit_opt',
						edit_id : edit_id
					},
					success : function( response ) {
						$(".options_table").html(response);
						set_survey_sizes();
						opt_order_change();
						tick_em_all();
					}
			});
	    });

	    //*************************************** EDITOVÁNÍ MOŽNOSTI

	    $('#options_table').on('click', '.option_editation_submit', function(event) {  

	    	event.preventDefault();

	    	var option_edit = $('#options_table .option_edit').val();
	    	var key_edit    = $('#options_table .key_edit').val();
		      
			$.ajax({
					url : ajaxurl,
					type : 'post',
					data : {
						action : 'survey_it_editing_opt',
						option_edit : option_edit,
						key_edit : key_edit
					},
					success : function( response ) {
						$(".options_table").html(response);
						set_survey_sizes();
						opt_order_change();
						tick_em_all();
						$( "#options_table").enableSelection() //vypne výběr aby to nevypadalo divně
					}
			});
	    });

	    //********************************* VYTVOŘENÍ ANKETY

	    $('.create_survey').on('click', function(event) {  

	    	event.preventDefault();

	    	var name = $('.name').val();
	    	var opt_answ = $('.opt_answ').val();
		      
			$.ajax({
					url : ajaxurl,
					type : 'post',
					data : {
						action : 'survey_it_add_survey',
						name : name,
						opt_answ : opt_answ
					},
					success : function( response ) {
						$(".options_table").html(response);
						set_survey_sizes();
						opt_order_change();
						tick_em_all();

						if($(".options_table .survey_warning").html() == 'data_sent'){ //pokud byla data odeslána PHP vygeneruje odstavec s obsahem 'data_sent'
							go_to_main_page();
						}

					}
			});
	    });

		//********************************* EDITACE ANKETY

	    $('.change_survey').on('click', function(event) {  

	    	event.preventDefault();

	    	var name = $('.name').val();
	    	var opt_answ = $('.opt_answ').val();
		      
			$.ajax({
					url : ajaxurl,
					type : 'post',
					data : {
						action : 'survey_it_change_survey',
						name : name,
						opt_answ : opt_answ,
						survey_id : $( '.change_survey' ).attr("id") //získá id ankety 
					},
					success : function( response ) {

						$(".options_table").html(response);
						set_survey_sizes();
						opt_order_change();
						tick_em_all();

						if($(".options_table .survey_warning").html() == 'data_sent'){ //pokud byla data odeslána PHP vygeneruje odstavec s obsahem 'data_sent'
							go_to_main_page();
						}

					}
			});
	    });

	    window.onresize = function(event) { //při změně velikosti okna
		    set_survey_sizes();
		};
	}

	/*********************************************************************************************************************************************************************
	****************************************************************** PŘESMĚROVÁNÍ **************************************************************************************
	**********************************************************************************************************************************************************************/

	if(endsWith(window.location.href,"page=survey_it")){
		go_to_main_page();
	}

	if(endsWith(window.location.href,"page=survey_results")){

		window.location.href = window.location.origin + "/wp-admin/admin.php?page=survey_results&survey_pagination=1"; 
		window.location.href = location.protocol + '//' + location.host + "/wp-admin/admin.php?page=survey_results&survey_pagination=1";
		document.URL = window.location.origin + "/wp-admin/admin.php?page=survey_results&survey_pagination=1";
		document.URL = location.protocol + '//' + location.host + "/wp-admin/admin.php?page=survey_results&survey_pagination=1";

	}

	function go_to_main_page() { //přesměruje na hlavní stránku pluginu
		window.location.href = window.location.origin + "/wp-admin/admin.php?page=survey_it&survey_pagination=1"; 
		window.location.href = location.protocol + '//' + location.host + "/wp-admin/admin.php?page=survey_it&survey_pagination=1";
		document.URL = window.location.origin + "/wp-admin/admin.php?page=survey_it&survey_pagination=1";
		document.URL = location.protocol + '//' + location.host + "/wp-admin/admin.php?page=survey_it&survey_pagination=1"; 
	}

	/*********************************************************************************************************************************************************************
	****************************************************************** OSTATNÍ *******************************************************************************************
	**********************************************************************************************************************************************************************/
	
	 $('.survey_bar').each(function( index ) {
	    var width = $( this ).html();
	    if(width=="0%"){
	    	$( this ).css("background-color", "transparent");
	    	$( this ).width("10%");
	    }else{
	    	$( this ).width(width);
	    }
		
	});

	if(endsWith(window.location.href,"page=survey_it_settings")){

		$('.reset').on('click', function(event) { 

			$("input[name=max_opt_c]").val('20');
			$("input[name=max_check_c]").val('5');
			$("input[name=alt_opt_n]").val('Another');
			$("input[name=vote_b]").val('Vote!');
			$("input[name=max_r_alt]").val('15');
			$("input[name=max_s_c_m]").val('10');
			$("input[name=max_s_c_r]").val('5');
			$("input[name=show_r]").prop('checked', true);

			$( '.survey_it_settings_s' ).trigger('click');

		});

	} 
	
});