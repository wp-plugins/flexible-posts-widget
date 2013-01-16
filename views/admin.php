<?php
/**
 * Flexible Posts Widget: Widget Admin Form 
 */

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

?>
<div class="dpe-fp-widget">

	<div class="section title">
        <p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget title:'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
	</div>
    
    <div class="section getemby">
		<h4><?php _e('Get posts by'); ?></h4>
		<div class="inside">
		
			<div id="<?php echo $this->get_field_id('getemby'); ?>" class="categorydiv getembytabs">
				
				<ul id="<?php echo $this->get_field_id('getemby-tabs'); ?>" class="category-tabs">
					<li><a href="#<?php echo $this->get_field_id('getemby-pt'); ?>">Post Type</a></li>
					<li><a href="#<?php echo $this->get_field_id('getemby-tt'); ?>">Taxonomy &amp; Term</a></li>
				</ul>
				
				<div id="<?php echo $this->get_field_id('getemby-pt'); ?>" class="tabs-panel pt">
					<?php $this->posttype_checklist( $posttype ); ?>
				</div><!-- .pt.getemby -->
				
				<div id="<?php echo $this->get_field_id('getemby-tt'); ?>" class="tabs-panel tt" style="display:none;">
					<p>	
						<!-- label for="<?php echo $this->get_field_id('taxonomy'); ?>"><?php _e('Select a taxonomy:'); ?></label --> 
						<select class="widefat dpe-fp-taxonomy" name="<?php echo $this->get_field_name('taxonomy'); ?>" id="<?php echo $this->get_field_id('taxonomy'); ?>">
							<option value="none" <?php echo 'none' == $taxonomy ? ' selected="selected"' : ''; ?>>Ignore Taxonomy &amp; Term</option>
							<?php
							foreach ($this->taxonomies as $option) {
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
				</div><!-- .tt.getemby -->
			
			</div><!-- #<?php echo $this->get_field_id('getemby'); ?> -->
			
		</div><!-- .inside -->
	
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
				foreach ( $this->orderbys as $key => $value ) {
					echo '<option value="' . $key . '" id="' . $this->get_field_id( $key ) . '"', $orderby == $key ? ' selected="selected"' : '', '>', $value, '</option>';
				}
				?>
			</select>		
		</p>
		<p class="cf">
			<label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order:'); ?></label> 
			<select name="<?php echo $this->get_field_name('order'); ?>" id="<?php echo $this->get_field_id('order'); ?>">
				<?php
				foreach ( $this->orders as $key => $value ) {
					echo '<option value="' . $key . '" id="' . $this->get_field_id( $key ) . '"', $order == $key ? ' selected="selected"' : '', '>', $value, '</option>';
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
				foreach ($this->thumbsizes as $option) {
					echo '<option value="' . $option . '" id="' . $this->get_field_id( $option ) . '"', $thumbsize == $option ? ' selected="selected"' : '', '>', $option, '</option>';
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
	
</div><!-- .dpe-fp-widget -->