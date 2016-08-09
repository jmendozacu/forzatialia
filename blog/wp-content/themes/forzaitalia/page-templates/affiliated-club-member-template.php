<?php
/**
 * Template Name: affiliated-club-member-template
 *
*/


get_header(); ?>
<style>
input.input-text, textarea, input.qty{
height:auto;
}
.hidden {
display:none;
}
.btnsubmit
{
float:right;
font-family:sans-serif;
color:#fff;
background:none repeat scroll 0 0 #333;
}
button, input[type="button"], input[type="reset"], input[type="submit"]{

    background: none repeat scroll 0 0 #262626;
    border: 1px solid #ccc;
    border-radius: 3px;
    cursor: pointer;
    font-size: 16px;;
    line-height: 1;
    padding: 0.3em 1em 0.4em;
}



</style>
	<div class="col-main">

		<!--<main id="main" class="site-main" role="main">-->



		<?php if ( have_posts() ) : ?>



			<?php /* Start the Loop */ ?>

			<?php while ( have_posts() ) : the_post(); ?>



				<?php

					/* Include the Post-Format-specific template for the content.

					 * If you want to override this in a child theme, then include a file

					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.

					 */

					get_template_part( 'content', get_post_format() );

				?>



			<?php endwhile; ?>



			<?php forzaitalia_paging_nav(); ?>



		<?php else : ?>



			<?php get_template_part( 'content', 'none' ); ?>



		<?php endif; ?>



		<!--</main>--><!-- #main -->

	</div><!-- #primary -->





<?php get_footer(); ?>

