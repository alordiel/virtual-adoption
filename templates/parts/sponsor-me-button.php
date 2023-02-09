<?php
/**
 * @var string $sponsor_link
 * @var int $post_id
 */
?>
<div class="blue-button-wrap">
	<a class="blue-button" href="<?php echo $sponsor_link . '?aid=' . va_encode_id( $post_id ); ?>"
	   title="<?php _e('Sponsor me','virtual-adoptions'); ?>"><?php _e('Sponsor me','virtual-adoptions'); ?></a>
</div>
