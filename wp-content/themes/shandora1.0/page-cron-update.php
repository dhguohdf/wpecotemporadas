<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 26/08/14
 * Time: 15:20
 */
$date = new DateTime();
$now = (string) $date->format( 'd-m-Y' );
echo 'now:'.$now;
// WP_Query arguments
$args = array(
	'post_type'      => 'rwp_transaction',
	'posts_per_page' => 9999999999999999999,
);

// The Query
$expire = new WP_Query( $args );

// The Loop
$contador = 0;
if ( $expire->have_posts() ) {
	while ( $expire->have_posts() ) {
		$expire->the_post();
		$tr_id = get_the_ID();
		$meta_day = get_post_meta( $tr_id, 'rwp_transaction_expire_date', true );
		echo '<br>Meta:'.$meta.'<br>';
		if($meta_day == '26-08-2014'){
			$newstatus = 'retirado';
			$object_id = get_post_meta( $tr_id, 'rwp_transaction_object', true );
			$post_id = get_post_meta( $tr_id, 'rwp_transaction_listing_id', true );

			update_post_meta( $tr_id, 'rwp_transaction_status', $newstatus );
			$_post = array(
				'ID'           => (int) $post_id,
				'post_status' => 'private'
			);
			wp_update_post($_post);

			do_action( 'rwp_status_changed', $newstatus, $tr_id, $object_id );
			do_action( 'rwp_status_changed_to_' . $newstatus, $tr_id, $object_id );
			$contador++;
		}
	}
}
else{
	echo '<br> else1 <br><br>';
}
echo '<br> Removidos:' . $contador;
$contador = 0;
// Restore original Post Data
wp_reset_postdata();

$date->modify( '-10 days' );
$now = (string) $date->format( 'd-m-Y' );
echo 'now:'.$now;
// WP_Query arguments
$args = array(
	'post_type'      => 'rwp_transaction',
	'posts_per_page' => 9999999999999999999,
	'meta_query'     => array(
		array(
			'key'     => 'rwp_transaction_pre_expire_date',
			'value'   => $now,
			'compare' => '=',
		),
	),
);

// The Query
$pre = new WP_Query( $args );

// The Loop
$contador = 0;
if ( $pre->have_posts() ) {
	while ( $pre->have_posts() ) {
		$expire->the_post();
		$tr_id = get_the_ID();
		$newstatus = 'expirando';

		$object_id = get_post_meta( $tr_id, 'rwp_transaction_object', true );

		update_post_meta( $tr_id, 'rwp_transaction_status', $newstatus );

		do_action( 'rwp_status_changed', $newstatus, $tr_id, $object_id );
		do_action( 'rwp_status_changed_to_' . $newstatus, $tr_id, $object_id );
		$contador++;
	}
}
else{
	echo '<br> else2 <br>';
}
echo 'Expirados:' . $contador;
// Restore original Post Data
wp_reset_postdata();
die();