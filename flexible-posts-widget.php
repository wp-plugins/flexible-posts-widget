<?php
/*
Plugin Name: Flexible Posts Widget
Plugin URI: http://wordpress.org/extend/plugins/flexible-posts-widget/
Author: David Paul Ellenwood
Author URI: http://dpedesign.com
Requires at least: 3.2
Tested up to: 3.3.1
Stable tag: 1.0
Tags: widget, widgets, posts, recent posts, thumbnails, custom post types, custom taxonomies
Description: An advanced posts display widget with many options: post by taxonomy & term or post type, thumbnails, order & order by, customizable templates
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

// Load the widget on widgets_init
function dpe_load_flexible_posts_widget() {
	register_widget('DPE_Flexible_Posts_Widget');
}
add_action('widgets_init', 'dpe_load_flexible_posts_widget');

/**
 * Flexible Posts Widget Class
 */
class DPE_Flexible_Posts_Widget extends WP_Widget {
	
    /** constructor */
    function DPE_Flexible_Posts_Widget() {
        parent::WP_Widget(false, $name = 'Flexible Posts Widget');	
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {	
        extract( $args );
        $title 		= apply_filters('widget_title', $instance['title']);		
		$taxonomy	= esc_attr( $instance['taxonomy'] );
        $term 		= esc_attr( $instance['term'] );
        $number 	= esc_attr( (int)$instance['number'] );
        $thumb_size = esc_attr( $instance['thumb_size'] );
        $thumbnail 	= esc_attr( $instance['thumbnail'] );
        $posttype 	= esc_attr( $instance['posttype'] );
		$orderby	= esc_attr( $instance['orderby'] );
		$order		= esc_attr( $instance['order'] );
		$template	= esc_attr( $instance['template'] );
		
		if( empty($template) )
			$template = 'widget';
		
		// Setup our query 
		if( !empty($term) ) {
			$args = array(
				'tax_query' => array(
					array(
						'taxonomy' => $taxonomy,
						'field' => 'slug',
						'terms' => $term,
					)
				),
				'post_status'		=> array('publish', 'private', 'inherit'),
				'posts_per_page'	=> $number,
				'orderby'			=> $orderby,
				'order'				=> $order,
			);
		} else {
			$args = array(
				'post_status'		=> array('publish', 'private', 'inherit'),
				'post_type'			=> $posttype,
				'posts_per_page'	=> $number,
				'orderby'			=> $orderby,
				'order'				=> $order,
			);
		}
		
		// Get the posts for this instance
		$flexible_posts = new WP_Query( $args );
		
		include( $this->getTemplateHierarchy( $template ) );
		
		wp_reset_postdata();
        
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {		
		global $posttypes;
		$instance 				= $old_instance;
		$instance['title']		= strip_tags( $new_instance['title'] );
		$instance['taxonomy']	= $new_instance['taxonomy'];
		$instance['term']		= strip_tags( $new_instance['term'] );
		$instance['number']		= strip_tags( $new_instance['number'] );
		$instance['thumb_size']	= $new_instance['thumb_size'];
		$instance['thumbnail']	= $new_instance['thumbnail'];
		$instance['posttype']	= $new_instance['posttype'];
		$instance['orderby']	= $new_instance['orderby'];
		$instance['order']		= $new_instance['order'];
		$instance['template']	= strip_tags( $new_instance['template'] );
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
		
		$posttypes = get_post_types('', 'objects');
		$taxonomies = get_taxonomies('', 'objects'); 
		
		$orderbys = array(
			'ID',
			'title',
			'date',
			'rand',
			'menu_order',
		);
		
		$orders = array(
			'ASC',
			'DESC',
		);
		
		$thumb_sizes = get_intermediate_image_sizes();
		
		if( !empty($instance) ) {
			$title		= esc_attr ($instance['title'] );
			$taxonomy	= esc_attr( $instance['taxonomy'] );
			$term		= esc_attr( $instance['term'] );
			$number		= esc_attr( $instance['number'] );
			$thumb_size	= esc_attr( $instance['thumb_size'] );
			$thumbnail	= esc_attr( $instance['thumbnail'] );
			$posttype	= esc_attr( $instance['posttype'] );
			$orderby	= esc_attr( $instance['orderby'] );
			$order		= esc_attr( $instance['order'] );
			$template	= esc_attr( $instance['template'] );
		}
		
		if( empty($orderby) )
			$orderby = 'date';
		
		if( empty($order) )
			$order = 'DESC';
			
		if( empty($template) )
			$template = 'widget';
		
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
		<h4 style="border-bottom:1px solid #dfdfdf;">Get posts by:</h4>
		<p><strong>By taxonomy &amp; term</strong></p>
		<p>	
			<label for="<?php echo $this->get_field_id('taxonomy'); ?>"><?php _e('Select a taxonomy'); ?></label> 
			<select class="widefat" name="<?php echo $this->get_field_name('taxonomy'); ?>" id="<?php echo $this->get_field_id('taxonomy'); ?>">
				<?php
				foreach ($taxonomies as $option) {
					echo '<option value="' . $option->name . '" id="' . $option->name . '"', $taxonomy == $option->name ? ' selected="selected"' : '', '>', $option->name, '</option>';
				}
				?>
			</select>		
		</p>
		<p>
          <label for="<?php echo $this->get_field_id('term'); ?>"><?php _e('Term &quot;slug&quot; (not the name)'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('term'); ?>" name="<?php echo $this->get_field_name('term'); ?>" type="text" value="<?php echo $term; ?>" />
          <span style="padding-top:3px;" class="description"><?php _e('Leave blank to ignore taxonomies &amp; terms'); ?></span>
		</p>
		<hr style="margin-bottom:12px; border:none; border-bottom:1px solid #dfdfdf" />
		<p><strong>By post type:</strong></p>
		<p>	
			<label for="<?php echo $this->get_field_id('posttype'); ?>"><?php _e('Post type'); ?></label> 
			<select class="widefat" name="<?php echo $this->get_field_name('posttype'); ?>" id="<?php echo $this->get_field_id('posttype'); ?>">
				<?php
				foreach ($posttypes as $option) {
					echo '<option value="' . $option->name . '" id="' . $option->name . '"', $posttype == $option->name ? ' selected="selected"' : '', '>', $option->name, '</option>';
				}
				?>
			</select>	
			<span style="padding-top:3px;" class="description">Ignored if a term is provided.</span>
		</p>
		<h4 style="border-bottom:1px solid #dfdfdf;">Display options</h4>
		<p>
          <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number to Show:'); ?></label> 
          <input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" />
        </p>
		<p>
          <input id="<?php echo $this->get_field_id('thumbnail'); ?>" name="<?php echo $this->get_field_name('thumbnail'); ?>" type="checkbox" value="1" <?php checked( '1', $thumbnail ); ?>/>
          <label for="<?php echo $this->get_field_id('thumbnail'); ?>"><?php _e('Display thumbnails?'); ?></label> 
        </p>
		<p>	
			<label for="<?php echo $this->get_field_id('thumb_size'); ?>"><?php _e('Select a thumbnail size to show'); ?></label> 
			<select class="widefat" name="<?php echo $this->get_field_name('thumb_size'); ?>" id="<?php echo $this->get_field_id('thumb_size'); ?>">
				<?php
				foreach ($thumb_sizes as $option) {
					echo '<option value="' . $option . '" id="' . $option . '"', $thumb_size == $option ? ' selected="selected"' : '', '>', $option, '</option>';
				}
				?>
			</select>		
		</p>
		<p>	
			<label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Order post by:'); ?></label> 
			<select class="widefat" name="<?php echo $this->get_field_name('orderby'); ?>" id="<?php echo $this->get_field_id('orderby'); ?>">
				<?php
				foreach ($orderbys as $option) {
					echo '<option value="' . $option . '" id="' . $option . '"', $orderby == $option ? ' selected="selected"' : '', '>', $option, '</option>';
				}
				?>
			</select>		
		</p>
		<p>	
			<label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order:'); ?></label> 
			<select class="widefat" name="<?php echo $this->get_field_name('order'); ?>" id="<?php echo $this->get_field_id('order'); ?>">
				<?php
				foreach ($orders as $option) {
					echo '<option value="' . $option . '" id="' . $option . '"', $order == $option ? ' selected="selected"' : '', '>', $option, '</option>';
				}
				?>
			</select>
			<br /><span style="padding-top:3px;" class="description"><?php _e('ASC = 1,2,3 or A,B,C<br />DESC = 3,2,1 or C,B,A'); ?></span>		
		</p>
		<p>
          <label for="<?php echo $this->get_field_id('template'); ?>"><?php _e('Template filename:'); ?></label> 
          <input id="<?php echo $this->get_field_id('template'); ?>" name="<?php echo $this->get_field_name('template'); ?>" type="text" value="<?php echo $template; ?>" />
		  <br /><span style="padding-top:3px;" class="description"><?php _e('Template filename without extension.'); ?></span>
        </p>
        <?php 
    }

	/**
	 * Loads theme files in appropriate hierarchy: 1) child theme,
	 * 2) parent template, 3) plugin resources. will look in the flexible-posts-widget/
	 * directory in a theme and the views/ directory in the plugin
	 *
	 * Function generously borrowed from the amazing image-widget
	 * by Matt Wiebe at Modern Tribe, Inc.
	 * http://wordpress.org/extend/plugins/image-widget/
	 * 
	 * @param string $template template file to search for
	 * @return template path
	 **/

	function getTemplateHierarchy($template) {
		// whether or not .php was added
		$template_slug = rtrim($template, '.php');
		$template = $template_slug . '.php';

		if ( $theme_file = locate_template(array('flexible-posts-widget/'.$template)) ) {
			$file = $theme_file;
		} else {
			$file = 'views/' . $template;
		}		
		//return apply_filters( 'dpe_template_flexible-posts_'.$template, $file); // - Maybe we'll add this in the future
		return $file;
	}


} // class DPE_Flexible_Posts_Widget

?>