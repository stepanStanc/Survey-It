(function() {
    tinymce.create("tinymce.plugins.survey_it", {

        //url argument holds the absolute url of our plugin directory
        init : function(ed, url) {

            var survey_it_values = new Array(); //je za potřebí před založit pole aby s do něj daly přidávat hodnoty

            jQuery.each(window.survey_it_ids ,function( index ) { //js verze foreach - index představuje klíč pro hodnotu v poli
              var id =  window.survey_it_ids[index]; 
              var name = window.survey_it_names[index];
              var value = { text: name  , value:  id  }; //vygenerování hodnoty pro Tiny selectbox podle hodnot z DB - objekt

              survey_it_values.push( value ); //přidání nové hodnoty

            });
           
            //add new button     
            ed.addButton("add_survey", { //přidá Tiny tlačítko
              type: 'listbox', //typ tlačítka
              text: 'Add survey', //text v tlačítku 
              icon: true, //povolení icony
              image: "http://icons.iconarchive.com/icons/icons8/ios7/256/Business-Survey-icon.png", //obrázek icony
              onselect: function (e) { //pokud byla vybrána hodnnota
                var v =  this.value();

                var t = window.survey_it_slugs[ v ];

                tinyMCE.activeEditor.insertContent( '[survey slug-name="' + t + '" id="' + v + '" ]' ); //vytvoření pořebného shortcodu podle zvolených hodnot - přidání do textu
              },
              values: survey_it_values
            });

            ed.addButton("add_survey_results", { //přidá Tiny tlačítko
              type: 'listbox', //typ tlačítka
              text: 'Add survey result', //text v tlačítku 
              icon: true, //povolení icony
              image: "https://pixabay.com/static/uploads/photo/2012/04/10/23/49/graph-27130_960_720.png", //obrázek icony
              onselect: function (e) { //pokud byla vybrána hodnnota
                var v =  this.value();

                var t = window.survey_it_slugs[ v ];

                tinyMCE.activeEditor.insertContent( '[survey-result slug-name="' + t + '" id="' + v + '" ]' ); //vytvoření pořebného shortcodu podle zvolených hodnot - přidání do textu
              },
              values: survey_it_values
            });

            //konec tlačítka

        },

        createControl : function(n, cm) {

        },

        getInfo : function() {
            return {
                longname : "Survey it! buttons",
                author : "Štěpán Štanc",
                version : "1"
            };
        }
    });

    tinymce.PluginManager.add("survey_it", tinymce.plugins.survey_it);
})();