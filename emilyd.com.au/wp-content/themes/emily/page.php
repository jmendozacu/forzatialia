<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>
<div class="body_container">
        <div class="page_heading">
          <h1 class="nwes_heading"><?php the_title(); ?></h1>
        </div>
        <div class="inner_content_area">
          <div class="inner_contents">
          	<?php the_content(); ?>
          </div>
 
        </div>
      </div>
	   
      <!--Body End--> 
      
    </div>
  </div>


<?php endwhile; // end of the loop. ?>
<?php get_footer(); ?>