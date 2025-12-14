<div class="rem-flex-gallery-wrap">
    <?php if (!empty($property_images)){ ?>
    	<div class="rem-gallery-container">
            <div class="rem-gallery-left-image">
                <a href="<?php echo esc_url(wp_get_attachment_image_url($property_images[0], 'full')); ?>" class="rem-popup-gallery">
                    <img src="<?php echo esc_url(wp_get_attachment_image_url($property_images[0], 'large')); ?>" alt="Property Image">
                </a>
            </div>
            <div class="rem-gallery-right-images">
                <div class="rem-gallery-row">
                    <?php if (isset($property_images[1])): ?>
                        <a href="<?php echo esc_url(wp_get_attachment_image_url($property_images[1], 'full')); ?>" class="rem-popup-gallery">
                            <img src="<?php echo esc_url(wp_get_attachment_image_url($property_images[1], 'medium_large')); ?>" alt="Property Image 1">
                        </a>
                    <?php endif; ?>
                    <?php if (isset($property_images[3])): ?>
                        <a href="<?php echo esc_url(wp_get_attachment_image_url($property_images[3], 'full')); ?>" class="rem-popup-gallery">
                            <img src="<?php echo esc_url(wp_get_attachment_image_url($property_images[3], 'medium_large')); ?>" alt="Property Image 3">
                        </a>
                    <?php endif; ?>
                </div>
                <div class="rem-gallery-row">
                    <?php if (isset($property_images[2])): ?>
                        <a href="<?php echo esc_url(wp_get_attachment_image_url($property_images[2], 'full')); ?>" class="rem-popup-gallery">
                            <img src="<?php echo esc_url(wp_get_attachment_image_url($property_images[2], 'medium_large')); ?>" alt="Property Image 2">
                        </a>
                    <?php endif; ?>
                    <?php if (isset($property_images[4])): ?>
                        <a href="<?php echo esc_url(wp_get_attachment_image_url($property_images[4], 'full')); ?>" class="rem-popup-gallery">
                            <img src="<?php echo esc_url(wp_get_attachment_image_url($property_images[4], 'medium_large')); ?>" alt="Property Image 4">
                        </a>
                    <?php endif; ?>
                </div>
            </div>
    	</div>
    	<div class="rem-gallery-buttons">
    		<a href="#" class="rem-gallery-button open-all-images"><?php _e( 'View All Images', 'real-estate-manager' ); ?> (<?php echo count($property_images); ?>)</a>
    	</div>
    <?php } ?>

    <!-- Hidden Images for Popup -->
    <div id="rem-hidden-gallery" style="display: none;">
        <?php 
        // Include extra images in the lightbox
        if (!empty($property_images)) {
            foreach ($property_images as $index => $id) {
                echo '<a href="'.esc_url(wp_get_attachment_image_url($id, 'full')).'" class=""></a>';
            }
        }
        ?>
    </div>
</div>