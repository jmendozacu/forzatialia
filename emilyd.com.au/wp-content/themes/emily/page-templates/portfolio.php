<?php
/**
 * Template Name: Gallery Page Template
 *
 * Description: A page template that provides a key component of WordPress as a CMS
 * by meeting the need for a carefully crafted introductory page. The front page template
 * in Twenty Twelve consists of a page content area for adding text, images, video --
 * anything you'd like -- followed by front-page-only widgets in one or two columns.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>
<div class="body_container">
        <div class="page_heading">
          <h1 class="port_heading"><?php the_title(); ?></h1>
        </div>
        <div class="inner_content_area">
          <div class="inner_contents">
          	<?php the_content(); ?>
          </div>
          <div class="video_heading">
          	<h1 class="video_gallery_title">Video Gallery</h1>
          </div>
          <div class="inner_contents">
          	<?php	if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('video_gallery') ) : endif; ?>
          </div>
 		<div class="portfolio">
        	<?php	if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('portfolio') ) : endif; ?>
        </div>
        </div>
      </div>
	   
      <!--Body End--> 
      
    </div>
  </div>


<?php endwhile; // end of the loop. ?>
<?php get_footer(); ?>