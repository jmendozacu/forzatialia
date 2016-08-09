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
    <h1 class="entry-header">
		<h1 class="entry-title" style="margin-bottom:5px;"><?php the_title(); ?>  <?php edit_post_link( __( 'Edit', 'forzaitalia' ), '<span class="edit-link">', '</span>' ); ?></h1>
        <div class="entry-utility"  style="margin:3px;">
                              <span class="post_date"><?php printf(
				$meta_text,
				$category_list,
				$tag_list,
				get_permalink()
			);?></span><?php if ( '' != $tag_list || '' !=$category_list )  {?>| <?php }?> <a class="post_by" href="#"><?php forzaitalia_posted_on(); ?></a> |
                               <span class="comm"><a href="#"><?php echo number_format_i18n( get_comments_number()); ?> comments</a></span> 
							   <span class="poding_coops"></span>
         </div> 
		<!--<div class="entry-meta">
			<?php //forzaitalia_posted_on(); ?> 
            <?php //echo number_format_i18n( get_comments_number()); ?>
		</div>--><!-- .entry-meta -->
	</h1><!-- .entry-header -->
   
	<div class="entry-content">
		<?php the_content(); ?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'forzaitalia' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

	<!-- .entry-meta -->
</article><!-- #post-## -->
