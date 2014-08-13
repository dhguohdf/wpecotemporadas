<?php if ( have_posts() ) : $compare_page = bon_get_option('compare_page'); ?>

    <?php while ( have_posts() ) : the_post(); ?>

        <?php 
        bon_get_template_part( 'content', get_post_type() ); ?>

    <?php endwhile; 
        $count = $wp_query->found_posts;
        $content_count = sprintf(_n('%s Car Listed', '%s Cars Listed', $count, 'bon'), $count);
        $show_listing_count = bon_get_option('show_listing_count', 'no');
        if($content_count && $show_listing_count == 'yes') { ?>
             <script type="text/javascript">
                /* <![CDATA[ */
                var shandora_data_count = "<?php echo $content_count; ?>";
                /* ]]> */
                </script>
        <?php }


    ?>

<?php else : ?>

    <?php bon_get_template_part( 'loop', 'error' ); // Loads the loop-error.php template. ?>

<?php endif; ?>