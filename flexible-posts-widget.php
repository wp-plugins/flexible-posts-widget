<?php
/*
Plugin Name: Flexible Posts Widget
Plugin URI: http://wordpress.org/extend/plugins/flexible-posts-widget/
Author: dpe415
Author URI: http://dpedesign.com
Version: 3.0
Description: An advanced posts display widget with many options: post by taxonomy & term or post type, thumbnails, order & order by, customizable templates
License: GPL2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/*  Copyright 2012  David Paul Ellenwood  (email : david@dpedesign.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Block direct requests
if( !defined('ABSPATH') )
	die('-1');
	
if( !defined('DPE_FP_Version') )
	define( 'DPE_FP_Version', '2.2' );


// Load the widget on widgets_init
function dpe_load_flexible_posts_widget() {
	register_widget('DPE_Flexible_Posts_Widget');
}
add_action('widgets_init', 'dpe_load_flexible_posts_widget');

		
// Setup our get terms/AJAX callback
add_action( 'wp_ajax_dpe_fp_get_terms', 'dpe_fp_term_me' );

/**
 * Flexible Posts Widget Class
 */

class DPE_Flexible_Posts_Widget extends WP_Widget {
	
	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		
		parent::__construct(
	 		'dpe_fp_widget', // Base ID
			'Flexible Posts Widget', // Name
			array( 'description' => __( 'Display posts as widget items', 'text_domain' ), ) // Args
		);
		
		$this->add_actions_filters();	// Register actions & filters
		$this->register_sns(); 			// Register styles & scripts
		
		global $pagenow;
		
		// Enqueue admin scripts
		if ( defined("WP_ADMIN") && WP_ADMIN ) {
			if ( 'widgets.php' == $pagenow ) {
				wp_enqueue_script( 'dpe-fp-widget' );
				wp_enqueue_style( 'dpe-fp-widget' );
			}
		}

		
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
        extract( $args );
		extract( $instance );
		
		$posttypes = get_post_types( array('public' => true ), 'names' );		
		$title = apply_filters( 'widget_title', empty( $title ) ? '' : $title );
		
		if ( empty($template) )
			$template = 'widget.php';
		
		// Setup the query
		$args = array(
			'post_status'		=> array('publish', 'inherit'),
			'posts_per_page'	=> $number,
			'offset'			=> $offset,
			'orderby'			=> $orderby,
			'order'				=> $order,
		);
		
		// Set the query post_type based on the user's selection
		if ( 'all' == $posttype ) {
			$args['post_type'] = $posttypes;
		} else {
			$args['post_type'] = $posttype;
		}
		
		// Setup the tax & term query based on the user's selection
		if ( $taxonomy != 'none' ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => $taxonomy,
					'field' => 'slug',
					'terms' => $term,
				)
			);
		}
		
		// Get the posts for this instance
		$flexible_posts = new WP_Query( $args );
		
		// Get and include the template we're going to use
		include( $this->getTemplateHierarchy( $template ) );
		
		// Be sure to reset any post_data before proceeding
		wp_reset_postdata();
        
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
    function update( $new_instance, $old_instance ) {		
		
		// Get the default values to test against
		$posttypes		= get_post_types( array('public' => true ), 'names' );
		$taxonomies		= get_taxonomies( array('public' => true ), 'names' );
		$orderbys		= array( 'ID', 'title', 'date', 'rand', 'menu_order', );
		$orders			= array( 'ASC', 'DESC', );
		$thumbsizes	= get_intermediate_image_sizes();
		
		// Add our defaults
		$posttypes[]	= 'all';
		$taxonomies[]	= 'none';
		
		$instance 				= $old_instance;
		$instance['title']		= strip_tags( $new_instance['title'] );
		$instance['posttype']	= ( in_array( $new_instance['posttype'], $posttypes ) ? $new_instance['posttype'] : 'post' );
		$instance['taxonomy']	= ( in_array( $new_instance['taxonomy'], $taxonomies ) ? $new_instance['taxonomy'] : 'none' );
		$instance['term']		= strip_tags( $new_instance['term'] );
		$instance['number']		= (int)$new_instance['number'];
		$instance['offset']		= (int)$new_instance['offset'];
		$instance['orderby']	= ( in_array( $new_instance['orderby'], $orderbys ) ? $new_instance['orderby'] : 'date' );
		$instance['order']		= ( in_array( $new_instance['order'], $orders ) ? $new_instance['order'] : 'DESC' );
		$instance['thumbnail']	= (bool)$new_instance['thumbnail'];
		$instance['thumbsize']	= (in_array ( $new_instance['thumbsize'], $thumbsizes ) ? $new_instance['thumbsize'] : '' );
		$instance['template']	= strip_tags( $new_instance['template'] );
        
        return $instance;
      
    }

    /**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
    function form( $instance ) {
		
		$posttypes		= get_post_types( array('public' => true ), 'objects' );
		$taxonomies		= get_taxonomies( array('public' => true ), 'objects' );
		$orderbys		= array( 'ID', 'title', 'date', 'rand', 'menu_order', );
		$orders			= array( 'ASC', 'DESC', );
		$thumbsizes	= get_intermediate_image_sizes();

		$instance = wp_parse_args( (array) $instance, array(
			'title'		=> '',
			'posttype'	=> 'post',
			'taxonomy'	=> 'none',
			'term'		=> '',
			'number'	=> '3',
			'offset'	=> '0',
			'orderby'	=> 'date',
			'order'		=> 'DESC',
			'thumbnail' => false,
			'thumbsize' => '',
			'template'	=> 'widget.php',
		) );
		
		extract( $instance );
		
        ?>
        <div class="dpe-fp-widget">
        
        	<div class="section title">
		        <p>
					<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget title:'); ?></label> 
					<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		        </p>
        	</div>
	        
	        <div class="section getemby">
				<h4><label for="<?php echo $this->get_field_id('getemby'); ?>"><?php _e('Get posts by'); ?></label></strong></h4>
				<div id="<?php echo $this->get_field_id('pt-box'); ?>" class="pt">
					<p>	
						<label for="<?php echo $this->get_field_id('posttype'); ?>"><?php _e('Select a post type:'); ?></label> 
						<select class="widefat dpe-fp-pt" name="<?php echo $this->get_field_name('posttype'); ?>" id="<?php echo $this->get_field_id('posttype'); ?>">
							<option value="all">All Post Types</option>
							<?php
							foreach ($posttypes as $option) {
								echo '<option value="' . $option->name . '"', $posttype == $option->name ? ' selected="selected"' : '', '>', $option->labels->name, '</option>';
							}
							?>
						</select>
					</p>
				</div><!-- .pt.getemby -->
				<div id="<?php echo $this->get_field_id('tnt-box'); ?>" class="tnt">
					<p>	
						<label for="<?php echo $this->get_field_id('taxonomy'); ?>"><?php _e('Select a taxonomy:'); ?></label> 
						<select class="widefat dpe-fp-taxonomy" name="<?php echo $this->get_field_name('taxonomy'); ?>" id="<?php echo $this->get_field_id('taxonomy'); ?>">
							<option value="none" <?php echo 'none' == $taxonomy ? ' selected="selected"' : ''; ?>>No Taxonomy</option>
							<?php
							foreach ($taxonomies as $option) {
								echo '<option value="' . $option->name . '"', $taxonomy == $option->name ? ' selected="selected"' : '', '>', $option->label, '</option>';
							}
							?>
						</select>		
					</p>
					<p<?php echo 'none' == $taxonomy ? ' style="display:none;"' : ''; ?>>
						<label for="<?php echo $this->get_field_id('term'); ?>"><?php _e('Select a term:'); ?></label> 
						<select class="widefat dpe-fp-term" name="<?php echo $this->get_field_name('term'); ?>" id="<?php echo $this->get_field_id('term'); ?>">
							<option value="-1">Please select...</option>
							<?php
								if ( $taxonomy && $taxonomy != 'none' ) {
									$args = array (
										'hide_empty' => 0,
									);
									
									$terms = get_terms( $taxonomy, $args );
									
									if ( !empty($terms) ) {
										$output = '';
										foreach ( $terms as $option )
											$output .= '<option value="' . $option->slug . '"' . ( $term == $option->slug ? ' selected="selected"' : '' ) . '>' . $option->name . '</option>';
										echo $output;
									}
								}
							?>
						</select>
					</p>
				</div><!-- .tnt.getemby -->
				<p class="description warning" style="display:none;"><strong>Woah!</strong> Your current settings will return absolutely everything. You might want to choose a post type or taxonomy &amp; term.</p> 
			</div>
			
			<div class="section display">
				<h4>Display options</h4>
				<p class="cf">
		          <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts to show:'); ?></label> 
		          <input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" />
		        </p>
				<p class="cf">
		          <label for="<?php echo $this->get_field_id('offset'); ?>"><?php _e('Number of posts to skip:'); ?></label> 
		          <input id="<?php echo $this->get_field_id('offset'); ?>" name="<?php echo $this->get_field_name('offset'); ?>" type="text" value="<?php echo $offset; ?>" />
		        </p>
		   		<p class="cf">
					<label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Order posts by:'); ?></label> 
					<select name="<?php echo $this->get_field_name('orderby'); ?>" id="<?php echo $this->get_field_id('orderby'); ?>">
						<?php
						foreach ($orderbys as $option) {
							echo '<option value="' . $option . '" id="' . $option . '"', $orderby == $option ? ' selected="selected"' : '', '>', $option, '</option>';
						}
						?>
					</select>		
				</p>
				<p class="cf">
					<label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order:'); ?></label> 
					<select name="<?php echo $this->get_field_name('order'); ?>" id="<?php echo $this->get_field_id('order'); ?>">
						<?php
						foreach ($orders as $option) {
							echo '<option value="' . $option . '"', $order == $option ? ' selected="selected"' : '', '>', $option, '</option>';
						}
						?>
					</select>		
				</p>
			</div>
			
			<div class="section thumbnails">
				<p style="margin-top:1.33em;">
		          <input class="dpe-fp-thumbnail" id="<?php echo $this->get_field_id('thumbnail'); ?>" name="<?php echo $this->get_field_name('thumbnail'); ?>" type="checkbox" value="1" <?php checked( '1', $thumbnail ); ?>/>
		          <label style="font-weight:bold;" for="<?php echo $this->get_field_id('thumbnail'); ?>"><?php _e('Display thumbnails?'); ?></label> 
		        </p>
				<p <?php echo $thumbnail ? '' : 'style="display:none;"'?>  class="thumb-size">	
					<label for="<?php echo $this->get_field_id('thumbsize'); ?>"><?php _e('Select a thumbnail size to show:'); ?></label> 
					<select class="widefat" name="<?php echo $this->get_field_name('thumbsize'); ?>" id="<?php echo $this->get_field_id('thumbsize'); ?>">
						<?php
						foreach ($thumbsizes as $option) {
							echo '<option value="' . $option . '" id="' . $option . '"', $thumbsize == $option ? ' selected="selected"' : '', '>', $option, '</option>';
						}
						?>
					</select>		
				</p>
			</div>
			
			<div class="section template">
				<p style="margin:1.33em 0;">
					<label for="<?php echo $this->get_field_id('template'); ?>"><?php _e('Template filename:'); ?></label>
					<input id="<?php echo $this->get_field_id('template'); ?>" name="<?php echo $this->get_field_name('template'); ?>" type="text" value="<?php echo $template; ?>" />
					<br />
					<span style="padding-top:3px;" class="description"><a target="_blank" href="http://wordpress.org/extend/plugins/flexible-posts-widget/other_notes/">See documentation</a> for details.</span>
				</p>
			</div>
			
		</div>
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
	public function getTemplateHierarchy( $template ) {
		
		// whether or not .php was added
		$template_slug = rtrim( $template, '.php' );
		$template = $template_slug . '.php';

		if ( $theme_file = locate_template( array( 'flexible-posts-widget/' . $template ) ) ) {
			$file = $theme_file;
		} else {
			$file = 'views/' . $template;
		}		
		
		//return apply_filters( 'dpe_template_flexible-posts_'.$template, $file); // - Maybe we'll add this in the future
		
		return $file;
		
	}
	
	/**
	 * Register styles & scripts
	 */
	public function register_sns() {
		$dir = plugins_url('/', __FILE__);
		wp_register_script( 'dpe-fp-widget', $dir . 'js/admin.js', array('jquery'), DPE_FP_Version, true );
		wp_register_style( 'dpe-fp-widget', $dir . 'css/admin.css', array(), DPE_FP_Version );
	}
	
	/**
	 * Setup our get terms/AJAX callback
	 */
	public function add_actions_filters() {
		add_action( 'wp_ajax_dpe_fp_get_terms', array( &$this, 'dpe_fp_term_me' ) );
	}
	
	/**
	 * return a list of terms for the chosen taxonomy used via AJAX
	 */
	public function dpe_fp_term_me() {
		
		$taxonomy = esc_attr( $_POST['taxonomy'] );
		$term = esc_attr( $_POST['term'] );
		
		if ( empty($taxonomy) || 'none' == $taxonomy ) {
			echo false;
			die();
		}
		
		$args = array (
			'hide_empty' => 0,
		);
		
		$terms = get_terms( $taxonomy, $args );
		
		if( empty($terms) ) { 
			$output = '<option value="-1">No terms found...</option>';
		} else {
			$output = '<option value="-1">Please select...</option>';
			foreach ( $terms as $option ) {
				$output .= '<option value="' . $option->slug . '"' . ( $term == $option->slug ? ' selected="selected"' : '' ) . '>' . $option->name . '</option>';
			}
		}
		
		echo( $output );
		
		die();
		
	}
	

} // class DPE_Flexible_Posts_Widget

?>