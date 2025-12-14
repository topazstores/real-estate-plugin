<?php get_header();
    global $post;
    $author_id = $post->post_author;
    $max_width = apply_filters( 'rem_max_container_width', '1170px' );
    $sticky_class = '';
    if (rem_get_option('agent_sidebar_sticky') == 'enable'){
        $sticky_class = 'rem_sticky_sidebar';
    }
    $property_id = get_the_id();
    $enable_ftd_img = (has_post_thumbnail( $property_id ) && rem_get_option('slider_featured_image', 'enable') == 'enable');
    $user_info = get_userdata($author_id);
    $email = $user_info->user_email;
    $phone = get_user_meta( $author_id, 'rem_mobile_url' , true );    
?>
<div id="rem-single-property-style-2">
	<div class="container ich-settings-main-wrap rem-gallery-main-wrap" style="max-width: <?php echo esc_attr($max_width); ?>;margin:0 auto;">
	    <div class="row">

	    	<div class="col-sm-12">
	    		<?php
	    			/**
	    			 * Hook: rem_single_property_page_slider.
	    			 */
	    			do_action( 'rem_single_property_page_slider', get_the_id() );
	    		?>
	    	</div>

		    <div class="rem-contact-details col-sm-12">
		    	<ul class="rem-inline-info">
		    		<?php if($phone){ ?>
		    		<li>
		    			<a href="tel:<?php echo esc_attr($phone); ?>">
			    			<i class="fas fa-phone"></i>
			    			<?php echo esc_attr($phone); ?>
		    			</a>
		    		</li>
		    		<?php } ?>
		    		<?php if($email){ ?>
		    		<li>
		    			<a href="mailto:<?php echo esc_attr($email); ?>">
			    			<i class="fas fa-envelope"></i>
			    			<?php echo esc_attr($email); ?>
		    			</a>
		    		</li>
		    		<?php } ?>
		    	</ul>
		    </div>
	    </div>
	</div>

	<div class="container-fluid rem-title-area">
		<div class="container ich-settings-main-wrap" style="max-width: <?php echo esc_attr($max_width); ?>;margin:0 auto;">
			<div class="rem-title-line-wrap">
				<h1><?php the_title(); ?></h1>
				<?php
					$type = get_post_meta( $property_id, 'rem_property_type', true );
					$purpose = get_post_meta( $property_id, 'rem_property_purpose', true );
				?>
				<?php if ($type || $purpose) { ?>
					<span class="rem-type">
						<?php echo $type ? $type : $purpose; ?>
					</span>
				<?php } ?>
			</div>

			<?php

				do_action('rem_property_details_icons', $property_id, 'inline');
			?>
			
		</div>
	</div>

	<div class="container ich-settings-main-wrap rem-description-area" style="max-width: <?php echo esc_attr($max_width); ?>;margin:0 auto;">
		<?php
			if ( ! post_password_required($post) ) {
				if( have_posts() ){
					while( have_posts() ){
						the_post();  ?>
						<div class="row">
							<div class="col-sm-9">
								<h2 class="desc-title"><?php _e( 'Description', 'real-estate-manager' ); ?></h2>
								<div class="desc-wrap">
									<?php
									    $content_property = get_post($property_id);
									    $content = $content_property->post_content;
									    if($content){
									        $content = apply_filters('the_content', $content);
									        $content = str_replace(']]>', ']]&gt;', $content);
									    }
									    echo $content;
									?>
								</div>

								<div id="rem-property-content">
								<?php
									/**
									 * Hook: rem_single_property_page_contents.
									 */
									do_action( 'rem_single_property_page_childs', get_the_id() );

									/**
									 * Hook: rem_single_property_page_sections.
									 */
									do_action( 'rem_single_property_page_sections', get_the_id() );

									/**
									 * Hook: rem_single_property_page_map.
									 */
									do_action( 'rem_single_property_page_map', get_the_id() );

									/**
									 * Hook: rem_single_property_page_tags.
									 */
									do_action( 'rem_single_property_page_tags', get_the_id() );

									/**
									 * Hook: rem_single_property_page_edit.
									 */
									do_action( 'rem_single_property_page_edit', get_the_id() );
								?>
								</div>

							</div>
							<div class="col-sm-3">
								<div class="rem-contact-details <?php echo esc_attr($sticky_class); ?>">
									<?php
										do_action( 'rem_single_property_agent', $author_id );
										$p_sidebar = rem_get_option('property_page_sidebar', '');
										if ( is_active_sidebar( $p_sidebar )  ) {
											dynamic_sidebar( $p_sidebar );
										}
									?>
								</div>
							</div>
						</div>
					<?php
				 	}
				}
			} else {
				echo get_the_password_form();
			}
		?>
	</div>
</div>

<?php get_footer(); ?>
