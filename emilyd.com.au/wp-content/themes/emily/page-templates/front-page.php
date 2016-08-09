<?php
/**
 * Template Name: Front Page
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
<div class="banner_area">
          <div class="banner_title_bar">
            <div class="title"><?php bloginfo( 'description' ); ?></div>

          </div>
          <div class="banner_content_area">
            <div class="banner_content"> 
            	<?php while ( have_posts() ) : the_post(); ?>
					<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentytwelve' ) ); ?>
				<?php endwhile; // end of the loop. ?>
            </div>
            <div class="banner_img">
            	<?php	if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('home-image') ) : endif; ?>
            </div>
            <div class="clear"></div>
          </div>
        </div>
      </div>
      
      
      
      <div class="body_container">
        <div class="row1">
          <div class="boxs">
            <div class="box_content_area">
              <div class="small_title_bar">
                <h2 class="p_icon"><a href="http://emilyd.com.au/gallery/" style="font-size:21px;">GALLERY</a></h2>
              </div>
              <div class="box_content">
              	<?php	if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('gallery') ) : endif; ?>
              </div>
            </div>
          </div>
          <div class="boxs boxs_right">
            <div class="box_content_area">
              <div class="small_title_bar">
                <h2 class="v_icon"><a href="http://emilyd.com.au/gallery/" style="font-size:21px;">VIDEOS</a></h2>
              </div>
              <div class="box_content">
                <?php	if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('video') ) : endif; ?>
              </div>
            </div>
          </div>
          <div class="clear"></div>
        </div>
        <div class="row2">
          <div class="boxs">
            <div class="box_content_area">
              <div class="small_title_bar">
                <h2 class="m_icon">MY LATEST TWEETS</h2>
              </div>
              <div class="box_content">
                <?php	if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('twitter') ) : endif; ?>
              </div>
            </div>
          </div>
          <div class="boxs boxs_right">
            <div class="box_content_area">
              <div class="small_title_bar">
                <h2>TESTIMONIALS</h2>
              </div>
              <div class="box_content">
                <?php	if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('testimonial') ) : endif; ?>
                <div style="clear:both"></div>
              </div>
              <div style="clear:both"></div>
            </div>
          </div>
          <div class="clear"></div>
        </div>
        <div class="clear"></div>
      </div>
      
      <!--Body End--> 

<?php get_footer(); ?>