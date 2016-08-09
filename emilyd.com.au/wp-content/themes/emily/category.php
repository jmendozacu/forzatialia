<?php
/**
 * The template for displaying Category pages.
 *
 * Used to display archive-type pages for posts in a category.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

get_header(); ?>
</div>
<?php if ( have_posts() ) : ?>
<div class="body_container">
        <div class="page_heading">
          <h1 class="nwes_heading"><?php printf( __( '%s', 'twentytwelve' ), '<span>' . single_cat_title( '', false ) . '</span>' ); ?></h1>
        </div>
        <div class="inner_content_area">
          <div class="inner_contents">
          	<?php
			/* Start the Loop */
			while ( have_posts() ) : the_post();
			?>
				<div class="content_rows">
              <div class="news_img">
              	<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
              </div>
              <div class="news_text"> <a href="<?php the_permalink(); ?>" class="news_title"><?php the_title(); ?></a><span class="news_date"><?php echo get_the_date(); ?></span>
                <?php the_excerpt(); ?>
              </div>
              <div class="clear"></div>
            </div>

			<?php
            endwhile;
			?>
            <div class="clear"></div>
          </div>
            <?php if(function_exists('wp_paginate')) {
					wp_paginate();
				} 
			?>
          
          <?php else : ?>
			<?php get_template_part( 'content', 'none' ); ?>
		<?php endif; ?>     
        </div>
      </div>
	   
      <!--Body End--> 
      
    </div>
  </div>



<?php get_footer(); ?>