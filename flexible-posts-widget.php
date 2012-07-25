<?php
/*
Plugin Name: Flexible Posts Widget
Plugin URI: http://wordpress.org/extend/plugins/flexible-posts-widget/
Author: dpe415
Author URI: http://dpedesign.com
Version: 2.1
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
	define( 'DPE_FP_Version', '2.0' );


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
		
		$this->register_sns(); // Register styles & scripts
		
		global $pagenow;
		
		// Enqueue admin scripts
		if (defined("WP_ADMIN") && WP_ADMIN) {
			if ( 'widgets.php' == $pagenow ) {
				wp_enqueue_script( 'dpe-fp-widget' );
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
		$title = apply_filters( 'widget_title', empty( $title ) ? '' : $title );
		
		if( empty($template) )
			$template = 'widget.php';
		
		// Setup our query 
		if( 'tnt' == $getemby ) {
			$args = array(
				'tax_query' => array(
					array(
						'taxonomy' => $taxonomy,
						'field' => 'slug',
						'terms' => $term,
					)
				),
				'post_status'		=> array('publish', 'inherit'),
				'posts_per_page'	=> $number,
				'offset'			=> $offset,
				'orderby'			=> $orderby,
				'order'				=> $order,
			);
		} else {
			$args = array(
				'post_status'		=> array('publish', 'inherit'),
				'post_type'			=> $posttype,
				'posts_per_page'	=> $number,
				'offset'			=> $offset,
				'orderby'			=> $orderby,
				'order'				=> $order,
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
    function update($new_instance, $old_instance) {		
		global $posttypes;
		$instance 				= $old_instance;
		$instance['title']		= strip_tags( $new_instance['title'] );
		$instance['getemby']	= $new_instance['getemby'];
		$instance['posttype']	= $new_instance['posttype'];
		$instance['taxonomy']	= $new_instance['taxonomy'];
		$instance['term']		= strip_tags( $new_instance['term'] );
		$instance['number']		= strip_tags( $new_instance['number'] );
		$instance['offset']		= strip_tags( $new_instance['offset'] );
		$instance['orderby']	= $new_instance['orderby'];
		$instance['order']		= $new_instance['order'];
		$instance['thumbsize']	= $new_instance['thumbsize'];
		$instance['thumbnail']	= $new_instance['thumbnail'];
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
    function form($instance) {
		
		$getembies = array( 'tnt', 'pt' );
		$posttypes = get_post_types('', 'objects');
		$taxonomies = get_taxonomies('', 'objects');
		$orderbys = array( 'ID', 'title', 'date', 'rand', 'menu_order', );
		$orders = array( 'ASC', 'DESC', );
		$thumb_sizes = get_intermediate_image_sizes();		

		$instance = wp_parse_args( (array) $instance, array(
			'title'		=> '',
			'getemby'	=> 'tnt',
			'posttype'	=> 'post',
			'taxonomy'	=> 'category',
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
        <p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title:'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
		<h4 style="margin-bottom:.2em;"><label for="<?php echo $this->get_field_id('getemby'); ?>"><?php _e('Get posts by:'); ?></label></strong></h4>
		<p>
			<select class="widefat dpe-fp-getemby" name="<?php echo $this->get_field_name('getemby'); ?>" id="<?php echo $this->get_field_id('getemby'); ?>">
				<option value="tnt"<?php echo $getemby == 'tnt' ? ' selected="selected"' : ''?>>Taxonomy &amp; Term</option>
				<option value="pt"<?php echo $getemby == 'pt' ? ' selected="selected"' : ''?>>Post Type</option>
			</select>
		</p>
		
		<div id="<?php echo $this->get_field_id('tnt-box'); ?>" class="getembies tnt" <?php echo $getemby == 'tnt' ? '' : 'style="display:none;"'?>>
			<p>	
				<label for="<?php echo $this->get_field_id('taxonomy'); ?>"><?php _e('Select a taxonomy:'); ?></label> 
				<select style="background-color:#fff;" class="widefat dpe-fp-taxonomy" name="<?php echo $this->get_field_name('taxonomy'); ?>" id="<?php echo $this->get_field_id('taxonomy'); ?>">
					<?php
					foreach ($taxonomies as $option) {
						echo '<option value="' . $option->name . '"', $taxonomy == $option->name ? ' selected="selected"' : '', '>', $option->label, '</option>';
					}
					?>
				</select>		
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('term'); ?>"><?php _e('Select a term:'); ?></label> 
				<select class="widefat dpe-fp-term" name="<?php echo $this->get_field_name('term'); ?>" id="<?php echo $this->get_field_id('term'); ?>">
					<option value="-1">Please select...</option>
					<?php
						if( $taxonomy ) {
							$args = array (
								'hide_empty' => 0,
							);
							
							$terms = get_terms( $taxonomy, $args );
							
							if( !empty($terms) ) {
								$output = '';
								foreach ( $terms as $option )
									$output .= '<option value="' . $option->slug . '"' . ( $term == $option->slug ? ' selected="selected"' : '' ) . '>' . $option->name . '</option>';
								echo( $output );
							}
						}
					?>
				</select>
			</p>
		</div><!-- .tnt.getemby -->
		
		<div id="<?php echo $this->get_field_id('pt-box'); ?>" class="getembies pt" <?php echo $getemby == 'pt' ? '' : 'style="display:none;"'?>>
			<p>	
				<label for="<?php echo $this->get_field_id('posttype'); ?>"><?php _e('Select a post type:'); ?></label> 
				<select class="widefat" name="<?php echo $this->get_field_name('posttype'); ?>" id="<?php echo $this->get_field_id('posttype'); ?>">
					<?php
					foreach ($posttypes as $option) {
						echo '<option value="' . $option->name . '"', $posttype == $option->name ? ' selected="selected"' : '', '>', $option->labels->name, '</option>';
					}
					?>
				</select>
			</p>
		</div><!-- .pt.getemby -->
		
		<hr style="margin-bottom:1.33em; border:none; border-bottom:1px solid #dfdfdf;" />
		<h4 style="">Display options</h4>
		<p>
          <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts to show:'); ?></label> 
          <input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" style="width:40px; margin-left:.2em; text-align:right;" />
        </p>
		<p>
          <label for="<?php echo $this->get_field_id('offset'); ?>"><?php _e('Number of posts to skip:'); ?></label> 
          <input id="<?php echo $this->get_field_id('offset'); ?>" name="<?php echo $this->get_field_name('offset'); ?>" type="text" value="<?php echo $offset; ?>" style="width:40px; margin-left:.2em; text-align:right;" />
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
					echo '<option value="' . $option . '"', $order == $option ? ' selected="selected"' : '', '>', $option, '</option>';
				}
				?>
			</select>
			<br />
			<span style="padding-top:3px;" class="description"><?php _e('ASC = 1,2,3 or A,B,C<br />DESC = 3,2,1 or C,B,A'); ?></span>		
		</p>
		<hr style="margin-bottom:1.33em; border:none; border-bottom:1px solid #dfdfdf;" />
		<p style="margin-top:1.33em;">
          <input class="dpe-fp-thumbnail" id="<?php echo $this->get_field_id('thumbnail'); ?>" name="<?php echo $this->get_field_name('thumbnail'); ?>" type="checkbox" value="1" <?php checked( '1', $thumbnail ); ?>/>
          <label style="font-weight:bold;" for="<?php echo $this->get_field_id('thumbnail'); ?>"><?php _e('Display thumbnails?'); ?></label> 
        </p>
		<p <?php echo $thumbnail == '1' ? '' : 'style="display:none;"'?>  class="thumb-size">	
			<label for="<?php echo $this->get_field_id('thumbsize'); ?>"><?php _e('Select a thumbnail size to show:'); ?></label> 
			<select class="widefat" name="<?php echo $this->get_field_name('thumbsize'); ?>" id="<?php echo $this->get_field_id('thumbsize'); ?>">
				<?php
				foreach ($thumb_sizes as $option) {
					echo '<option value="' . $option . '" id="' . $option . '"', $thumbsize == $option ? ' selected="selected"' : '', '>', $option, '</option>';
				}
				?>
			</select>		
		</p>
		<hr style="margin-bottom:1.33em; border:none; border-bottom:1px solid #dfdfdf;" />
		<p style="margin:1.33em 0;">
			<label for="<?php echo $this->get_field_id('template'); ?>"><?php _e('Template filename:'); ?></label>
			<input id="<?php echo $this->get_field_id('template'); ?>" name="<?php echo $this->get_field_name('template'); ?>" type="text" value="<?php echo $template; ?>" />
			<br />
			<span style="padding-top:3px;" class="description"><a target="_blank" href="http://wordpress.org/extend/plugins/flexible-posts-widget/other_notes/">See documentation</a> for details.</span>
		</p>
		<hr style="margin-bottom:1.33em; border:none; border-bottom:1px solid #dfdfdf;" />
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

	function getTemplateHierarchy( $template ) {
		
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
	
	// Register styles & scripts
	function register_sns() {
		$dir = plugins_url('/', __FILE__);
		wp_register_script( 'dpe-fp-widget', $dir . 'js/admin.js', array('jquery'), DPE_FP_Version, true );
	}
	

} // class DPE_Flexible_Posts_Widget


// return a list of terms for the chosen taxonomy used via AJAX
function dpe_fp_term_me() {
	
	$taxonomy = esc_attr( $_POST['taxonomy'] );
	$term = esc_attr( $_POST['term'] );
	
	if( empty($taxonomy) )
		echo false;
	
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

?>