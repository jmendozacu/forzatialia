<?php
/**
 * @package forzaitalia
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php
			/* translators: used between list items, there is a space after the comma */
			$category_list = get_the_category_list( __( ', ', 'forzaitalia' ) );

			/* translators: used between list items, there is a space after the comma */
			$tag_list = get_the_tag_list( '', __( ', ', 'forzaitalia' ) );

			if ( ! forzaitalia_categorized_blog() ) {
				// This blog only has 1 category so we just need to worry about tags in the meta text
				if ( '' != $tag_list ) {
					//$meta_text = __( 'This entry was tagged %2$s. Bookmark the <a href="%3$s" rel="bookmark">permalink</a>.', 'forzaitalia' );
					$meta_text = __( 'Tagges: %2$s.', 'forzaitalia' );
				} else {
					//$meta_text = __( 'Bookmark the <a href="%3$s" rel="bookmark">permalink</a>.', 'forzaitalia' );
				}

			} else {
				// But this blog has loads of categories so we should probably display them here
				if ( '' != $tag_list ) {
					$meta_text = __( '%1$s and tagged %2$s. ', 'forzaitalia' );
				} else {
					$meta_text = __( '%1$s. ', 'forzaitalia' );
				}

			} // end check for categories on this blog

			/*printf(
				$meta_text,
				$category_list,
				$tag_list,
				get_permalink()
			);*/
		?>
		<?php //edit_post_link( __( 'Edit', 'forzaitalia' ), '<span class="edit-link">', '</span>' ); ?>
     <?php   if(is_page('affiliated-club-member-form')){ 
	 //entery header display none 
	 }
    else {  ?>
	<h1 class="entry-header">
   
		<h1 class="entry-title" style="margin-bottom:5px;"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>  <?php edit_post_link( __( 'Edit', 'forzaitalia' ), '<span class="edit-link">', '</span>' ); ?></h1>
           <div class="entry-utility" style="padding-top: 3px;">
                              <span class="post_date"  style="margin:3px;"><?php printf(
				$meta_text,
				$category_list,
				$tag_list,
				get_permalink()
			);?></span> <?php if ( '' != $tag_list || '' !=$category_list )  {?> | <?php }?>  <a class="post_by" href="#"><?php forzaitalia_posted_on(); ?></a> |
                               <span class="comm" style="margin:3px;"><a href="#"><?php echo number_format_i18n( get_comments_number()); ?> comments</a></span> 
							   <span class="poding_coops" style="margin:3px;"></span>
         </div> 
		
	</h1>	
<?php     } 
	?>
    <!-- .entry-header -->

	<?php if ( is_search() ) : // Only display Excerpts for Search ?>
	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->
	<?php else : ?>
	<div class="entry-content">
		<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'forzaitalia' ) ); ?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'forzaitalia' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->
	<?php endif; ?>

	<!-- .entry-meta -->
</article><!-- #post-## -->
