<?php
    $price = get_post_meta($property_id, 'rem_property_price', true);
?>
<div class="wrap-slider">
    <?php do_action( 'rem_property_ribbon', $property_id, 'single-page' ); ?>
    <?php if($price){ ?>
        <span class="large-price"><?php echo rem_display_property_price($property_id); ?></span>
    <?php } ?>

    <div class="fotorama-custom" <?php echo $this->fotorama_data_attrs(); ?>>
        <?php if (is_array($property_images)) {
            foreach ($property_images as $id) {
                $id = function_exists('icl_object_id') ? icl_object_id($id, 'attachment', true) : $id;
                $image_url = wp_get_attachment_image_url($id, $image_size);
                $image_title = wp_strip_all_tags(get_the_title($id));
                $image_alt = wp_strip_all_tags(get_post_meta($id, '_wp_attachment_image_alt', TRUE));
                
                if (wp_attachment_is( 'video', $id )) {
                    $video_url = wp_get_attachment_url($id);
                    echo '<a class="rem-slider-image" href="'.esc_url($video_url).'" data-video="true"></a>';
                } else {
                    echo '<img class="skip-lazy rem-slider-image" data-alt="'.esc_attr($image_alt).'" data-title="'.esc_attr($image_title).'" src="'.esc_url($image_url).'">';
                }
            }
        } ?>
    </div>
</div>