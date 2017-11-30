jQuery(document).ready(function($){

	if ( $( ".survey" ).length ) { //pokud je na stránce anketa

		user_info = window.user_info;
		
		$('.survey').on('change', 'input[type=checkbox]', function(event) {
			var opt_answ = document.getElementById("opt_answ").innerHTML; //získá z DB poč zaškrtnutelných odpovědí (povolených)
			
		    if ($('input[type=checkbox]:checked').length > opt_answ) { //kontroluje jestli je daný počet zaškrtnutý pokud je tak zakáže další zaškrtnutí
		        $(this).prop('checked', false);
		    }

		    if($("input:checkbox[value=alt]").prop( "checked" ) == true){ //pokud je alternativní možnost zaškrtnutá otevře se text area jinak se skyrje
		    	$('.option_other').show();
		    }else{
		    	$('.option_other').hide();
		    }

		});


		$('.survey').on('keypress', 'input[type=text]', function(event) { //simuluje potvrzení ankety (na enter) - text input není ve formuláři
		    if (event.which == 13) {
		        event.preventDefault();
		        $(".survey .confirm_survey").trigger('click');
		    }
		});



		var font = (function () { //detekce typy fontu na zájladě jeho délky v objektových funkcích
		
		//https://remysharp.com/2008/07/08/how-to-detect-if-a-font-is-installed-only-using-javascript

		//creative commons a MIT licence

		    var test_string = 'mmmmmmmmmwwwwwww';
		    var test_font = '"Arial"'; //font s co největší podporou
		    var notInstalledWidth = 0;
		    var testbed = null;
		    var guid = 0;
		    
		    return {
		        // must be called when the dom is ready
		        setup : function () {
		            if ($('#fontInstalledTest').length) return; //pokud již existuje kontrolní string - nic neudělá

		            $('head').append('<' + 'style> #fontInstalledTest, #fontTestBed {position: absolute;left: -9999px; top: 0;visibility: hidden; } #fontInstalledTest {font-size: 50px!important;font-family: ' + test_font + ';}</' + 'style>'); //přidá kontrolní styl
		            
		            $('body').append('<div id="fontTestBed"></div>').append('<span id="fontInstalledTest" class="fonttest">' + test_string + '</span>'); //připojí k body skrytý kontrolní string
		            testbed = $('#fontTestBed'); //načte pozici kontrolního stringu
		            notInstalledWidth = $('#fontInstalledTest').width(); //vytvoří kontrolní šířku
		        },
		        
		        font_compare : function(font) {
		            guid++; //pro originální id kontrolního stringu
		        
		            var style = '<' + 'style id="fonttestStyle">#fonttest' + guid + ' {font-size: 50px!important;font-family: ' + font + ', ' + test_font + ';}<' + '/style>'; //přidá testovaný font a pak kontrolní
		            
		            $('head').find('#fonttestStyle').remove().end().append(style);//přepíše styl na kontrolu
		            testbed.empty().append('<span id="fonttest' + guid + '" class="fonttest">' + test_string + '</span>'); //vloží kontrolní string
		                        
		            return (testbed.find('span').width() != notInstalledWidth); //pokud je kontrolovaný string odlišný od kontrolního tak vrátí true pokud není false
		        }
		    };
		})();

		
		h_w = screen.width + "x" + screen.height; //výška a šířka obrazovky
		os_v = navigator.appVersion.match(/\(([^)]+)\)/)[1]; //verze opreačního systému - (match získává první hodnotu v závorkách stringu)
		var del = os_v.indexOf(';'); 
		os_v = os_v.substring(0, del != -1 ? del : os_v.length); //odstraní vše po středníku = verze os pokud existuje středník
		touch = navigator.maxTouchPoints; //dotykových bodů obrazovky 
		cpu_c = navigator.hardwareConcurrency; //jader procesoru - nepodporuje IE,Edge
		platform = navigator.platform; //platforma - (win32,win64 atd.)
		lang = navigator.languages ? navigator.languages[0]: (navigator.language || navigator.userLanguage); //vrátí jeden jazyk
		var del = lang.indexOf('-'); 
		lang = lang.substring(0, del != -1 ? del : lang.length); //odstraní vše po pomlčce = nechá "en" z "en-GB" nebo "en-US" atd.
		c_d = screen.colorDepth; //barevná hlobka obrazovky
		timezone = (new Date()).getTimezoneOffset()/60; //získá časové pásmo

		function mobile_check() { // true smartphone/false PC nebo notebook
		  var check = false;
		  (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true})(navigator.userAgent||navigator.vendor||window.opera);
		  return check;
		}

		if(mobile_check()==false){ //android nepodporuje kontrolu fontů (sám přepisuje fonty jeho vlastní) - zrychlí načítání pro mobily


			font.setup(); //spustí tester fontů
			var font_check = new Array(); //připraví pole

			var googlefonts = {"ABeeZee" : "ABeeZee","Abel" : "Abel","Abril Fatface" : "Abril+Fatface","Aclonica" : "Aclonica","Acme" : "Acme","Actor" : "Actor","Adamina" : "Adamina","Advent Pro" : "Advent+Pro","Aguafina Script" : "Aguafina+Script","Akronim" : "Akronim","Aladin" : "Aladin","Aldrich" : "Aldrich","Alegreya" : "Alegreya","Alegreya SC" : "Alegreya+SC","Alex Brush" : "Alex+Brush","Alfa Slab One" : "Alfa+Slab+One","Alice" : "Alice","Alike" : "Alike","Alike Angular" : "Alike+Angular","Allan" : "Allan","Allerta" : "Allerta","Allerta Stencil" : "Allerta+Stencil","Allura" : "Allura","Almendra" : "Almendra","Almendra Display" : "Almendra+Display","Almendra SC" : "Almendra+SC","Amarante" : "Amarante","Amaranth" : "Amaranth","Amatic SC" : "Amatic+SC","Amethysta" : "Amethysta","Anaheim" : "Anaheim","Andada" : "Andada","Andika" : "Andika","Angkor" : "Angkor","Annie Use Your Telescope" : "Annie+Use+Your+Telescope","Anonymous Pro" : "Anonymous+Pro","Antic" : "Antic","Antic Didone" : "Antic+Didone","Antic Slab" : "Antic+Slab","Anton" : "Anton","Arapey" : "Arapey","Arbutus" : "Arbutus","Arbutus Slab" : "Arbutus+Slab","Architects Daughter" : "Architects+Daughter","Archivo Black" : "Archivo+Black","Archivo Narrow" : "Archivo+Narrow","Arimo" : "Arimo","Arizonia" : "Arizonia","Armata" : "Armata","Artifika" : "Artifika","Arvo" : "Arvo","Asap" : "Asap","Asset" : "Asset","Astloch" : "Astloch","Asul" : "Asul","Atomic Age" : "Atomic+Age","Aubrey" : "Aubrey","Audiowide" : "Audiowide","Autour One" : "Autour+One","Average" : "Average","Average Sans" : "Average+Sans","Averia Gruesa Libre" : "Averia+Gruesa+Libre","Averia Libre" : "Averia+Libre","Averia Sans Libre" : "Averia+Sans+Libre","Averia Serif Libre" : "Averia+Serif+Libre","Bad Script" : "Bad+Script","Balthazar" : "Balthazar","Bangers" : "Bangers","Basic" : "Basic","Battambang" : "Battambang","Baumans" : "Baumans","Bayon" : "Bayon","Belgrano" : "Belgrano","Belleza" : "Belleza","BenchNine" : "BenchNine","Bentham" : "Bentham","Berkshire Swash" : "Berkshire+Swash","Bevan" : "Bevan","Bigelow Rules" : "Bigelow+Rules","Bigshot One" : "Bigshot+One","Bilbo" : "Bilbo","Bilbo Swash Caps" : "Bilbo+Swash+Caps","Bitter" : "Bitter","Black Ops One" : "Black+Ops+One","Bokor" : "Bokor","Bonbon" : "Bonbon","Boogaloo" : "Boogaloo","Bowlby One" : "Bowlby+One","Bowlby One SC" : "Bowlby+One+SC","Brawler" : "Brawler","Bree Serif" : "Bree+Serif","Bubblegum Sans" : "Bubblegum+Sans","Bubbler One" : "Bubbler+One","Buda" : "Buda","Buenard" : "Buenard","Butcherman" : "Butcherman","Butterfly Kids" : "Butterfly+Kids","Cabin" : "Cabin","Cabin Condensed" : "Cabin+Condensed","Cabin Sketch" : "Cabin+Sketch","Caesar Dressing" : "Caesar+Dressing","Cagliostro" : "Cagliostro","Calligraffitti" : "Calligraffitti","Cambo" : "Cambo","Candal" : "Candal","Cantarell" : "Cantarell","Cantata One" : "Cantata+One","Cantora One" : "Cantora+One","Capriola" : "Capriola","Cardo" : "Cardo","Carme" : "Carme","Carrois Gothic" : "Carrois+Gothic","Carrois Gothic SC" : "Carrois+Gothic+SC","Carter One" : "Carter+One","Caudex" : "Caudex","Cedarville Cursive" : "Cedarville+Cursive","Ceviche One" : "Ceviche+One","Changa One" : "Changa+One","Chango" : "Chango","Chau Philomene One" : "Chau+Philomene+One","Chela One" : "Chela+One","Chelsea Market" : "Chelsea+Market","Chenla" : "Chenla","Cherry Cream Soda" : "Cherry+Cream+Soda","Cherry Swash" : "Cherry+Swash","Chewy" : "Chewy","Chicle" : "Chicle","Chivo" : "Chivo","Cinzel" : "Cinzel","Cinzel Decorative" : "Cinzel+Decorative","Clicker Script" : "Clicker+Script","Coda" : "Coda","Coda Caption" : "Coda+Caption","Codystar" : "Codystar","Combo" : "Combo","Comfortaa" : "Comfortaa","Coming Soon" : "Coming+Soon","Concert One" : "Concert+One","Condiment" : "Condiment","Content" : "Content","Contrail One" : "Contrail+One","Convergence" : "Convergence","Cookie" : "Cookie","Copse" : "Copse","Corben" : "Corben","Courgette" : "Courgette","Cousine" : "Cousine","Coustard" : "Coustard","Covered By Your Grace" : "Covered+By+Your+Grace","Crafty Girls" : "Crafty+Girls","Creepster" : "Creepster","Crete Round" : "Crete+Round","Crimson Text" : "Crimson+Text","Croissant One" : "Croissant+One","Crushed" : "Crushed","Cuprum" : "Cuprum","Cutive" : "Cutive","Cutive Mono" : "Cutive+Mono","Damion" : "Damion","Dancing Script" : "Dancing+Script","Dangrek" : "Dangrek","Dawning of a New Day" : "Dawning+of+a+New+Day","Days One" : "Days+One","Delius" : "Delius","Delius Swash Caps" : "Delius+Swash+Caps","Delius Unicase" : "Delius+Unicase","Della Respira" : "Della+Respira","Denk One" : "Denk+One","Devonshire" : "Devonshire","Didact Gothic" : "Didact+Gothic","Diplomata" : "Diplomata","Diplomata SC" : "Diplomata+SC","Domine" : "Domine","Donegal One" : "Donegal+One","Doppio One" : "Doppio+One","Dorsa" : "Dorsa","Dosis" : "Dosis","Dr Sugiyama" : "Dr+Sugiyama","Droid Sans" : "Droid+Sans","Droid Sans Mono" : "Droid+Sans+Mono","Droid Serif" : "Droid+Serif","Duru Sans" : "Duru+Sans","Dynalight" : "Dynalight","EB Garamond" : "EB+Garamond","Eagle Lake" : "Eagle+Lake","Eater" : "Eater","Economica" : "Economica","Electrolize" : "Electrolize","Elsie" : "Elsie","Elsie Swash Caps" : "Elsie+Swash+Caps","Emblema One" : "Emblema+One","Emilys Candy" : "Emilys+Candy","Engagement" : "Engagement","Englebert" : "Englebert","Enriqueta" : "Enriqueta","Erica One" : "Erica+One","Esteban" : "Esteban","Euphoria Script" : "Euphoria+Script","Ewert" : "Ewert","Exo" : "Exo","Expletus Sans" : "Expletus+Sans","Fanwood Text" : "Fanwood+Text","Fascinate" : "Fascinate","Fascinate Inline" : "Fascinate+Inline","Faster One" : "Faster+One","Fasthand" : "Fasthand","Federant" : "Federant","Federo" : "Federo","Felipa" : "Felipa","Fenix" : "Fenix","Finger Paint" : "Finger+Paint","Fjalla One" : "Fjalla+One","Fjord One" : "Fjord+One","Flamenco" : "Flamenco","Flavors" : "Flavors","Fondamento" : "Fondamento","Fontdiner Swanky" : "Fontdiner+Swanky","Forum" : "Forum","Francois One" : "Francois+One","Freckle Face" : "Freckle+Face","Fredericka the Great" : "Fredericka+the+Great","Fredoka One" : "Fredoka+One","Freehand" : "Freehand","Fresca" : "Fresca","Frijole" : "Frijole","Fruktur" : "Fruktur","Fugaz One" : "Fugaz+One","GFS Didot" : "GFS+Didot","GFS Neohellenic" : "GFS+Neohellenic","Gabriela" : "Gabriela","Gafata" : "Gafata","Galdeano" : "Galdeano","Galindo" : "Galindo","Gentium Basic" : "Gentium+Basic","Gentium Book Basic" : "Gentium+Book+Basic","Geo" : "Geo","Geostar" : "Geostar","Geostar Fill" : "Geostar+Fill","Germania One" : "Germania+One","Gilda Display" : "Gilda+Display","Give You Glory" : "Give+You+Glory","Glass Antiqua" : "Glass+Antiqua","Glegoo" : "Glegoo","Gloria Hallelujah" : "Gloria+Hallelujah","Goblin One" : "Goblin+One","Gochi Hand" : "Gochi+Hand","Gorditas" : "Gorditas","Goudy Bookletter 1911" : "Goudy+Bookletter+1911","Graduate" : "Graduate","Grand Hotel" : "Grand+Hotel","Gravitas One" : "Gravitas+One","Great Vibes" : "Great+Vibes","Griffy" : "Griffy","Gruppo" : "Gruppo","Gudea" : "Gudea","Habibi" : "Habibi","Hammersmith One" : "Hammersmith+One","Hanalei" : "Hanalei","Hanalei Fill" : "Hanalei+Fill","Handlee" : "Handlee","Hanuman" : "Hanuman","Happy Monkey" : "Happy+Monkey","Headland One" : "Headland+One","Henny Penny" : "Henny+Penny","Herr Von Muellerhoff" : "Herr+Von+Muellerhoff","Holtwood One SC" : "Holtwood+One+SC","Homemade Apple" : "Homemade+Apple","Homenaje" : "Homenaje","IM Fell DW Pica" : "IM+Fell+DW+Pica","IM Fell DW Pica SC" : "IM+Fell+DW+Pica+SC","IM Fell Double Pica" : "IM+Fell+Double+Pica","IM Fell Double Pica SC" : "IM+Fell+Double+Pica+SC","IM Fell English" : "IM+Fell+English","IM Fell English SC" : "IM+Fell+English+SC","IM Fell French Canon" : "IM+Fell+French+Canon","IM Fell French Canon SC" : "IM+Fell+French+Canon+SC","IM Fell Great Primer" : "IM+Fell+Great+Primer","IM Fell Great Primer SC" : "IM+Fell+Great+Primer+SC","Iceberg" : "Iceberg","Iceland" : "Iceland","Imprima" : "Imprima","Inconsolata" : "Inconsolata","Inder" : "Inder","Indie Flower" : "Indie+Flower","Inika" : "Inika","Irish Grover" : "Irish+Grover","Istok Web" : "Istok+Web","Italiana" : "Italiana","Italianno" : "Italianno","Jacques Francois" : "Jacques+Francois","Jacques Francois Shadow" : "Jacques+Francois+Shadow","Jim Nightshade" : "Jim+Nightshade","Jockey One" : "Jockey+One","Jolly Lodger" : "Jolly+Lodger","Josefin Sans" : "Josefin+Sans","Josefin Slab" : "Josefin+Slab","Joti One" : "Joti+One","Judson" : "Judson","Julee" : "Julee","Julius Sans One" : "Julius+Sans+One","Junge" : "Junge","Jura" : "Jura","Just Another Hand" : "Just+Another+Hand","Just Me Again Down Here" : "Just+Me+Again+Down+Here","Kameron" : "Kameron","Karla" : "Karla","Kaushan Script" : "Kaushan+Script","Kavoon" : "Kavoon","Keania One" : "Keania+One","Kelly Slab" : "Kelly+Slab","Kenia" : "Kenia","Khmer" : "Khmer","Kite One" : "Kite+One","Knewave" : "Knewave","Kotta One" : "Kotta+One","Koulen" : "Koulen","Kranky" : "Kranky","Kreon" : "Kreon","Kristi" : "Kristi","Krona One" : "Krona+One","La Belle Aurore" : "La+Belle+Aurore","Lancelot" : "Lancelot","Lato" : "Lato","League Script" : "League+Script","Leckerli One" : "Leckerli+One","Ledger" : "Ledger","Lekton" : "Lekton","Lemon" : "Lemon","Libre Baskerville" : "Libre+Baskerville","Life Savers" : "Life+Savers","Lilita One" : "Lilita+One","Limelight" : "Limelight","Linden Hill" : "Linden+Hill","Lobster" : "Lobster","Lobster Two" : "Lobster+Two","Londrina Outline" : "Londrina+Outline","Londrina Shadow" : "Londrina+Shadow","Londrina Sketch" : "Londrina+Sketch","Londrina Solid" : "Londrina+Solid","Lora" : "Lora","Love Ya Like A Sister" : "Love+Ya+Like+A+Sister","Loved by the King" : "Loved+by+the+King","Lovers Quarrel" : "Lovers+Quarrel","Luckiest Guy" : "Luckiest+Guy","Lusitana" : "Lusitana","Lustria" : "Lustria","Macondo" : "Macondo","Macondo Swash Caps" : "Macondo+Swash+Caps","Magra" : "Magra","Maiden Orange" : "Maiden+Orange","Mako" : "Mako","Marcellus" : "Marcellus","Marcellus SC" : "Marcellus+SC","Marck Script" : "Marck+Script","Margarine" : "Margarine","Marko One" : "Marko+One","Marmelad" : "Marmelad","Marvel" : "Marvel","Mate" : "Mate","Mate SC" : "Mate+SC","Maven Pro" : "Maven+Pro","McLaren" : "McLaren","Meddon" : "Meddon","MedievalSharp" : "MedievalSharp","Medula One" : "Medula+One","Megrim" : "Megrim","Meie Script" : "Meie+Script","Merienda" : "Merienda","Merienda One" : "Merienda+One","Merriweather" : "Merriweather","Merriweather Sans" : "Merriweather+Sans","Metal" : "Metal","Metal Mania" : "Metal+Mania","Metamorphous" : "Metamorphous","Metrophobic" : "Metrophobic","Michroma" : "Michroma","Milonga" : "Milonga","Miltonian" : "Miltonian","Miltonian Tattoo" : "Miltonian+Tattoo","Miniver" : "Miniver","Miss Fajardose" : "Miss+Fajardose","Modern Antiqua" : "Modern+Antiqua","Molengo" : "Molengo","Molle" : "Molle","Monda" : "Monda","Monofett" : "Monofett","Monoton" : "Monoton","Monsieur La Doulaise" : "Monsieur+La+Doulaise","Montaga" : "Montaga","Montez" : "Montez","Montserrat" : "Montserrat","Montserrat Alternates" : "Montserrat+Alternates","Montserrat Subrayada" : "Montserrat+Subrayada","Moul" : "Moul","Moulpali" : "Moulpali","Mountains of Christmas" : "Mountains+of+Christmas","Mouse Memoirs" : "Mouse+Memoirs","Mr Bedfort" : "Mr+Bedfort","Mr Dafoe" : "Mr+Dafoe","Mr De Haviland" : "Mr+De+Haviland","Mrs Saint Delafield" : "Mrs+Saint+Delafield","Mrs Sheppards" : "Mrs+Sheppards","Muli" : "Muli","Mystery Quest" : "Mystery+Quest","Neucha" : "Neucha","Neuton" : "Neuton","New Rocker" : "New+Rocker","News Cycle" : "News+Cycle","Niconne" : "Niconne","Nixie One" : "Nixie+One","Nobile" : "Nobile","Nokora" : "Nokora","Norican" : "Norican","Nosifer" : "Nosifer","Nothing You Could Do" : "Nothing+You+Could+Do","Noticia Text" : "Noticia+Text","Nova Cut" : "Nova+Cut","Nova Flat" : "Nova+Flat","Nova Mono" : "Nova+Mono","Nova Oval" : "Nova+Oval","Nova Round" : "Nova+Round","Nova Script" : "Nova+Script","Nova Slim" : "Nova+Slim","Nova Square" : "Nova+Square","Numans" : "Numans","Nunito" : "Nunito","Odor Mean Chey" : "Odor+Mean+Chey","Offside" : "Offside","Old Standard TT" : "Old+Standard+TT","Oldenburg" : "Oldenburg","Oleo Script" : "Oleo+Script","Oleo Script Swash Caps" : "Oleo+Script+Swash+Caps","Open Sans" : "Open+Sans","Open Sans Condensed" : "Open+Sans+Condensed","Oranienbaum" : "Oranienbaum","Orbitron" : "Orbitron","Oregano" : "Oregano","Orienta" : "Orienta","Original Surfer" : "Original+Surfer","Oswald" : "Oswald","Over the Rainbow" : "Over+the+Rainbow","Overlock" : "Overlock","Overlock SC" : "Overlock+SC","Ovo" : "Ovo","Oxygen" : "Oxygen","Oxygen Mono" : "Oxygen+Mono","PT Mono" : "PT+Mono","PT Sans" : "PT+Sans","PT Sans Caption" : "PT+Sans+Caption","PT Sans Narrow" : "PT+Sans+Narrow","PT Serif" : "PT+Serif","PT Serif Caption" : "PT+Serif+Caption","Pacifico" : "Pacifico","Paprika" : "Paprika","Parisienne" : "Parisienne","Passero One" : "Passero+One","Passion One" : "Passion+One","Patrick Hand" : "Patrick+Hand","Patrick Hand SC" : "Patrick+Hand+SC","Patua One" : "Patua+One","Paytone One" : "Paytone+One","Peralta" : "Peralta","Permanent Marker" : "Permanent+Marker","Petit Formal Script" : "Petit+Formal+Script","Petrona" : "Petrona","Philosopher" : "Philosopher","Piedra" : "Piedra","Pinyon Script" : "Pinyon+Script","Pirata One" : "Pirata+One","Plaster" : "Plaster","Play" : "Play","Playball" : "Playball","Playfair Display" : "Playfair+Display","Playfair Display SC" : "Playfair+Display+SC","Podkova" : "Podkova","Poiret One" : "Poiret+One","Poller One" : "Poller+One","Poly" : "Poly","Pompiere" : "Pompiere","Pontano Sans" : "Pontano+Sans","Port Lligat Sans" : "Port+Lligat+Sans","Port Lligat Slab" : "Port+Lligat+Slab","Prata" : "Prata","Preahvihear" : "Preahvihear","Press Start 2P" : "Press+Start+2P","Princess Sofia" : "Princess+Sofia","Prociono" : "Prociono","Prosto One" : "Prosto+One","Puritan" : "Puritan","Purple Purse" : "Purple+Purse","Quando" : "Quando","Quantico" : "Quantico","Quattrocento" : "Quattrocento","Quattrocento Sans" : "Quattrocento+Sans","Questrial" : "Questrial","Quicksand" : "Quicksand","Quintessential" : "Quintessential","Qwigley" : "Qwigley","Racing Sans One" : "Racing+Sans+One","Radley" : "Radley","Raleway" : "Raleway","Raleway Dots" : "Raleway+Dots","Rambla" : "Rambla","Rammetto One" : "Rammetto+One","Ranchers" : "Ranchers","Rancho" : "Rancho","Rationale" : "Rationale","Redressed" : "Redressed","Reenie Beanie" : "Reenie+Beanie","Revalia" : "Revalia","Ribeye" : "Ribeye","Ribeye Marrow" : "Ribeye+Marrow","Righteous" : "Righteous","Risque" : "Risque","Roboto" : "Roboto","Roboto Condensed" : "Roboto+Condensed","Rochester" : "Rochester","Rock Salt" : "Rock+Salt","Rokkitt" : "Rokkitt","Romanesco" : "Romanesco","Ropa Sans" : "Ropa+Sans","Rosario" : "Rosario","Rosarivo" : "Rosarivo","Rouge Script" : "Rouge+Script","Ruda" : "Ruda","Rufina" : "Rufina","Ruge Boogie" : "Ruge+Boogie","Ruluko" : "Ruluko","Rum Raisin" : "Rum+Raisin","Ruslan Display" : "Ruslan+Display","Russo One" : "Russo+One","Ruthie" : "Ruthie","Rye" : "Rye","Sacramento" : "Sacramento","Sail" : "Sail","Salsa" : "Salsa","Sanchez" : "Sanchez","Sancreek" : "Sancreek","Sansita One" : "Sansita+One","Sarina" : "Sarina","Satisfy" : "Satisfy","Scada" : "Scada","Schoolbell" : "Schoolbell","Seaweed Script" : "Seaweed+Script","Sevillana" : "Sevillana","Seymour One" : "Seymour+One","Shadows Into Light" : "Shadows+Into+Light","Shadows Into Light Two" : "Shadows+Into+Light+Two","Shanti" : "Shanti","Share" : "Share","Share Tech" : "Share+Tech","Share Tech Mono" : "Share+Tech+Mono","Shojumaru" : "Shojumaru","Short Stack" : "Short+Stack","Siemreap" : "Siemreap","Sigmar One" : "Sigmar+One","Signika" : "Signika","Signika Negative" : "Signika+Negative","Simonetta" : "Simonetta","Sintony" : "Sintony","Sirin Stencil" : "Sirin+Stencil","Six Caps" : "Six+Caps","Skranji" : "Skranji","Slackey" : "Slackey","Smokum" : "Smokum","Smythe" : "Smythe","Sniglet" : "Sniglet","Snippet" : "Snippet","Snowburst One" : "Snowburst+One","Sofadi One" : "Sofadi+One","Sofia" : "Sofia","Sonsie One" : "Sonsie+One","Sorts Mill Goudy" : "Sorts+Mill+Goudy","Source Code Pro" : "Source+Code+Pro","Source Sans Pro" : "Source+Sans+Pro","Special Elite" : "Special+Elite","Spicy Rice" : "Spicy+Rice","Spinnaker" : "Spinnaker","Spirax" : "Spirax","Squada One" : "Squada+One","Stalemate" : "Stalemate","Stalinist One" : "Stalinist+One","Stardos Stencil" : "Stardos+Stencil","Stint Ultra Condensed" : "Stint+Ultra+Condensed","Stint Ultra Expanded" : "Stint+Ultra+Expanded","Stoke" : "Stoke","Strait" : "Strait","Sue Ellen Francisco" : "Sue+Ellen+Francisco","Sunshiney" : "Sunshiney","Supermercado One" : "Supermercado+One","Suwannaphum" : "Suwannaphum","Swanky and Moo Moo" : "Swanky+and+Moo+Moo","Syncopate" : "Syncopate","Tangerine" : "Tangerine","Taprom" : "Taprom","Tauri" : "Tauri","Telex" : "Telex","Tenor Sans" : "Tenor+Sans","Text Me One" : "Text+Me+One","The Girl Next Door" : "The+Girl+Next+Door","Tienne" : "Tienne","Tinos" : "Tinos","Titan One" : "Titan+One","Titillium Web" : "Titillium+Web","Trade Winds" : "Trade+Winds","Trocchi" : "Trocchi","Trochut" : "Trochut","Trykker" : "Trykker","Tulpen One" : "Tulpen+One","Ubuntu" : "Ubuntu","Ubuntu Condensed" : "Ubuntu+Condensed","Ubuntu Mono" : "Ubuntu+Mono","Ultra" : "Ultra","Uncial Antiqua" : "Uncial+Antiqua","Underdog" : "Underdog","Unica One" : "Unica+One","UnifrakturCook" : "UnifrakturCook","UnifrakturMaguntia" : "UnifrakturMaguntia","Unkempt" : "Unkempt","Unlock" : "Unlock","Unna" : "Unna","VT323" : "VT323","Vampiro One" : "Vampiro+One","Varela" : "Varela","Varela Round" : "Varela+Round","Vast Shadow" : "Vast+Shadow","Vibur" : "Vibur","Vidaloka" : "Vidaloka","Viga" : "Viga","Voces" : "Voces","Volkhov" : "Volkhov","Vollkorn" : "Vollkorn","Voltaire" : "Voltaire","Waiting for the Sunrise" : "Waiting+for+the+Sunrise","Wallpoet" : "Wallpoet","Walter Turncoat" : "Walter+Turncoat","Warnes" : "Warnes","Wellfleet" : "Wellfleet","Wendy One" : "Wendy+One","Wire One" : "Wire+One","Yanone Kaffeesatz" : "Yanone+Kaffeesatz","Yellowtail" : "Yellowtail","Yeseva One" : "Yeseva+One","Yesteryear" : "Yesteryear","Zeyada" : "Zeyada",};

			//google fonty v objektu - 645 ( zapotřebí menší počet a jiné )

			for (var key in googlefonts){

		         var value = font.font_compare(googlefonts[ key ]); //jednotlivé kontrly pro každou hodnotu v objektu
		         font_check.push( value );

			}

		}

		window.user_info = h_w +'/#/'+ os_v +'/#/'+ touch +'/#/'+ cpu_c +'/#/'+ platform +'/#/'+ lang +'/#/'+ c_d +'/#/'+ timezone +'/#/'+ mobile_check() +'/#/'+ font_check;
		//string s informacemi o zařízení,obrazoce,časové zóně a jazyku uživatele (globální proměná)
		

		$.ajax({ //kontrola jsetli uživatel hlasoval - nakonci aby bylo jisté že se user info vytvořilo
				url : myAjax.ajaxurl,
				type : 'post',
				data : {
					'action' : 'survey_it_ajax_voting_check',
					'user_info' : window.user_info,
					'id' : $( '.survey' ).attr("id") //získá id ankety 
				},
				success : function( response ) {
					$(".survey").html(response);

					$('.survey_bar').each(function( index ) { //pro každý prvek s class "survey_bar"

					    var width = $( this ).html(); //získá šířku podle v PHP spočítaného procenta odpovědí

					    if(width=="0%"){ //pokud je nula neukáže se žádná bar
					    	$( this ).css("background-color", "transparent");
					    	$( this ).width("10%"); //aby bylo správně zobrazeno
					    }else{ //jinak se ukáže bar s takovou šířkou jakou má v danou
					    	$( this ).width(width);
					    }
						
					});


				}
		});

		


		$('.survey').on('click', '.confirm_survey', function(event) {  //ajax pro potvrzování ankety - hlasování

	    	event.preventDefault();

	    	if ($(".survey input:checkbox:checked").length > 0) {

				var vote = $('.survey .survey_form').serialize(); 
				var option_other = $('.survey .option_other').val();
			      
				$.ajax({
						url : myAjax.ajaxurl,
						type : 'post',
						data : {
							'action' : 'survey_it_ajax_voting',
							'user_info' : user_info,
							'vote' : vote,
							'option_other' : option_other,
							'id' : $( '.survey' ).attr("id") //získá id ankety 
						},
						success : function( response ) {
							$(".survey").html(response);

							$('.survey_bar').each(function( index ) { //pro každý prvek s class "survey_bar"

							    var width = $( this ).html(); //získá šířku podle v PHP spočítaného procenta odpovědí

							    if(width=="0%"){ //pokud je nula neukáže se žádná bar
							    	$( this ).css("background-color", "transparent");
							    	$( this ).width("10%"); //aby bylo správně zobrazeno
							    }else{ //jinak se ukáže bar s takovou šířkou jakou má v danou
							    	$( this ).width(width);
							    }
								
							});

						}
				});
			}
	    });
	}

	
	
	
});
