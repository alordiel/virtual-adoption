<?php
/**
 * @var int $post_id
 */
$sheltered_animal = get_post( $post_id );
dbga($post_id);
dbga($sheltered_animal);
if ( empty( $sheltered_animal ) ) {
	include_once 'no-animals-found.php';
} else {
	$age           = get_post_meta( $post_id, 'animals-age', true );
	$sheltered_for = get_post_meta( $post_id, 'sheltered-years', true );
	$animal_link   = get_the_permalink( $post_id );
	$image         = get_the_post_thumbnail_url( $post_id, 'medium' );
	$the_title     = $sheltered_animal->post_title;
	include 'animal-card.php';
}
