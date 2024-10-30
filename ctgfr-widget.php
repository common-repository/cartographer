<?php 

  class cartographer_widget extends WP_Widget {

    function __construct(){
      $widget_ops = array('classname' => 'ctgfr-widget', 'description' => 'Displays a custom google map of your Cartographer locations.' );
      $this->WP_Widget('cartographer_widget', 'Cartographer Map', $widget_ops);
    }
   
    function form($instance){
      $instance = wp_parse_args((array) $instance, array('ctgfr_shortcode' => '', 'ctgfr_title' => ''));
      $ctgfr_shortcode = $instance['ctgfr_shortcode'];
      $ctgfr_title = $instance['ctgfr_title'];
      ?>
      <p class="ctgfr-widget">
        <label for="<?php echo $this->get_field_id('ctgfr_title'); ?>">Title:</label>
        <input class="widefat title" id="<?php echo $this->get_field_id('ctgfr_title'); ?>" name="<?php echo $this->get_field_name('ctgfr_title'); ?>" type="text" value="<?php echo attribute_escape($ctgfr_title); ?>" />
        <label for="<?php echo $this->get_field_id('ctgfr_shortcode'); ?>">Shortcode:</label>
        <textarea class="widefat shortcode" rows="8" id="<?php echo $this->get_field_id('ctgfr_shortcode'); ?>" name="<?php echo $this->get_field_name('ctgfr_shortcode'); ?>" type="text"><?php echo attribute_escape($ctgfr_shortcode); ?></textarea>
        <button class="add-ctgfr-map button">Build Shortcode</button>
        <?php add_thickbox(); ?>
      </p>
      <?php
      }
   
    function update($new_instance, $old_instance){
      $instance = $old_instance;
      $instance['ctgfr_shortcode'] = $new_instance['ctgfr_shortcode'];
      $instance['ctgfr_title'] = $new_instance['ctgfr_title'];
      return $instance;
    }
   
    function widget($args, $instance){
      extract($args, EXTR_SKIP);
      $title = apply_filters('widget_title', $instance['ctgfr_title']);

      echo $before_widget;

      // render widget
      echo '<div class="ctgfr-widget-box">';
      if($title){echo '<h3 class="widget-title">' . $title . '</h3>';}
      echo do_shortcode($instance['ctgfr_shortcode']);
      echo '</div>';
   
      echo $after_widget;
    }
  }

  // register widget
  add_action('widgets_init', create_function('', 'return register_widget("cartographer_widget");'));

?>