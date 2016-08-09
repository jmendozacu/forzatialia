<?php
/**
 * The template for displaying the footer.
 *
 * Contains footer content and the closing of the
 * #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
?>
  <div class="footer_container">
    <div class="footer_body">
      <div class="footer_left">
        <div class="copy_right">EMILY DIMITRIADIS | All Rights Reserved <?php echo date('Y'); ?></div>
        <div class="footer_logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><img src="<?php echo get_template_directory_uri(); ?>/images/footer_logo.png" alt="" /></a></div>
      </div>
      <div class="footer_right">
        <?php	if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('social') ) : endif; ?>
      </div>
    </div>
  </div>
  
  <!--Footer End--> 
  
</div>
<?php wp_footer(); ?>
</body>
</html>