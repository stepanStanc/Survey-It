<?php

/**
 * Adds Survey_Widget widget.
 */
class Survey_Widget extends WP_Widget {

   /**
    * Register widget with WordPress.
    */
   function __construct() {
      parent::__construct(
         'survey_widget', // Base ID
         __( 'Survey', 'text_domain' ), // Name
         array( 'description' => __( 'Add survey sidebar', 'text_domain' ), ) // Args
      );
   }

   /**
    * Front-end display of widget.
    *
    * @see WP_Widget::widget()
    *
    * @param array $args     Widget arguments.
    * @param array $instance Saved values from database.
    */
   function widget($args, $instance) {
    // PART 1: Extracting the arguments + getting the values
    extract($args, EXTR_SKIP);
    $id = empty($instance['survey']) ? '' : $instance['survey'];

    // Before widget code, if any
    echo (isset($before_widget)?$before_widget:'');


    echo "<div class='survey' id='{$id}'> </div>"; //připraví pro ajax - načte to samé jako shortcode

    // After widget code, if any  
    echo (isset($after_widget)?$after_widget:'');
  }

   /**
    * Back-end widget form.
    *
    * @see WP_Widget::form()
    *
    * @param array $instance Previously saved values from database.
    */
   public function form( $instance ) { //formulář pro výběr ankety
     // PART 1: Extract the data from the instance variable
     $survey = $instance['survey'];   

     // PART 2-3: Display the fields
     ?>

     <!-- PART 3: Widget field START -->
     <p>
      <label for="<?php echo $this->get_field_id('text'); ?>">Choose survey to add: 
        <select class='widefat' id="<?php echo $this->get_field_id('survey'); ?>" name="<?php echo $this->get_field_name('survey'); ?>" type="text">

          <?php
          global $wpdb;

          $table_name = $wpdb->prefix . "surveys";
          $ids = $wpdb->get_col( "SELECT id FROM $table_name ORDER BY id DESC ");//získá všechna id z wpdb anket - od nejnovějšího po nejstarší
          $names = $wpdb->get_col( "SELECT name FROM $table_name ORDER BY id DESC ");

          foreach($ids as $key => $id){
              $name = $names[$key];

              $chosen = ($survey==$id) ? 'selected' : '' ; //pokud je anketa již zvolená vypší se v checkboxu
              
              echo "
             <option value='{$id}' {$chosen} >
               {$name}
             </option>";
          }

          ?>

        </select>                
      </label>
     </p>
     <!-- Widget field END -->
     <?php 
  }

   /**
    * Sanitize widget form values as they are saved.
    *
    * @see WP_Widget::update()
    *
    * @param array $new_instance Values just sent to be saved.
    * @param array $old_instance Previously saved values from database.
    *
    * @return array Updated safe values to be saved.
    */
   function update($new_instance, $old_instance) { //ukládání hodont z
       $instance = $old_instance;
       $instance['survey'] = $new_instance['survey'];

       return $instance;
     }

} // class Survey_Widget


class Survey_Results_Widget extends WP_Widget {

   /**
    * Register widget with WordPress.
    */
   function __construct() {
      parent::__construct(
         'survey_r_widget', // Base ID
         __( 'Survey results', 'text_domain' ), // Name
         array( 'description' => __( 'Add survey results sidebar', 'text_domain' ), ) // Args
      );
   }

   /**
    * Front-end display of widget.
    *
    * @see WP_Widget::widget()
    *
    * @param array $args     Widget arguments.
    * @param array $instance Saved values from database.
    */
   function widget($args, $instance) {
    // PART 1: Extracting the arguments + getting the values
    extract($args, EXTR_SKIP);
    $id = empty($instance['survey_r']) ? '' : $instance['survey_r'];

    // Before widget code, if any
    echo (isset($before_widget)?$before_widget:'');

    $table_name = $wpdb->prefix . "surveys"; //používá stejný kód jako shortcode
    $data = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = $id", ARRAY_A ); //načte tabulku vybrané ankety jako pole

    echo survey_results_fc($data);

    // After widget code, if any  
    echo (isset($after_widget)?$after_widget:'');
  }

   /**
    * Back-end widget form.
    *
    * @see WP_Widget::form()
    *
    * @param array $instance Previously saved values from database.
    */
   public function form( $instance ) { //formulář pro výběr ankety
     // PART 1: Extract the data from the instance variable
     $survey_r = $instance['survey_r'];   

     // PART 2-3: Display the fields
     ?>

     <!-- PART 3: Widget field START -->
     <p>
      <label for="<?php echo $this->get_field_id('text'); ?>">Choose survey to add it's results: 
        <select class='widefat' id="<?php echo $this->get_field_id('survey_r'); ?>" name="<?php echo $this->get_field_name('survey_r'); ?>" type="text">

          <?php
          global $wpdb;

          $table_name = $wpdb->prefix . "surveys";
          $ids = $wpdb->get_col( "SELECT id FROM $table_name ORDER BY id DESC ");//získá všechna id z wpdb anket - od nejnovějšího po nejstarší
          $names = $wpdb->get_col( "SELECT name FROM $table_name ORDER BY id DESC ");

          foreach($ids as $key => $id){
              $name = $names[$key];

              $chosen = ($survey_r==$id) ? 'selected' : '' ;
              
              echo "
             <option value='{$id}' {$chosen} >
               {$name}
             </option>";
          }

          ?>

        </select>                
      </label>
     </p>
     <!-- Widget field END -->
     <?php 
  }

   /**
    * Sanitize widget form values as they are saved.
    *
    * @see WP_Widget::update()
    *
    * @param array $new_instance Values just sent to be saved.
    * @param array $old_instance Previously saved values from database.
    *
    * @return array Updated safe values to be saved.
    */
   function update($new_instance, $old_instance) { //ukládání hodont z
       $instance = $old_instance;
       $instance['survey_r'] = $new_instance['survey_r'];

       return $instance;
     }

} // class Survey_Results_Widget

?>