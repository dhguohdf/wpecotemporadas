<?php if ( have_posts() ) : $data_map = array(); ?>

    <?php while ( have_posts() ) : the_post(); ?>

        <?php bon_get_template_part( 'content', ( post_type_supports( get_post_type(), 'post-formats' ) ? get_post_format() : get_post_type() ) ); ?>

        <?php if ( is_singular() && post_type_supports( get_post_type(), 'comments') ) { comments_template(); } // Loads the comments.php template. ?>

    <?php endwhile; ?>

<?php else : ?>

    <?php bon_get_template_part( 'loop', 'error' ); // Loads the loop-error.php template. ?>

<?php endif; ?>


<?php bon_get_template_part( 'loop','nav' ); // Loads the loop-nav.php template. ?>