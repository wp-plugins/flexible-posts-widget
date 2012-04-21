<?php
/**
 * Flexible Posts Widget: Default widget template
 */

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

echo $before_widget;

if ( !empty($title) )
	echo $before_title . $title . $after_title;
	
	if( $flexible_posts->have_posts() ):
?>
		<ul class="dpe-flexible-posts">
		<?php while ( $flexible_posts->have_posts() ) : $flexible_posts->the_post(); ?>
			<li id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<a href="<?php echo the_permalink(); ?>">
					<?php
						if( $thumbnail == true )
							the_post_thumbnail( $thumbsize );
					?>
					<h4 class="title"><?php the_title(); ?></h4>
				</a>
			</li>
		<?php endwhile; ?>
		</ul><!-- .dpe-flexible-posts -->
<?php
		echo $after_widget;

else: ?>
	<div class="no-posts dpe-flexible-posts">
		<p>No post found</p>
	</div>
<?php
	endif; // End have_posts()
?>