<div class="search-container rem-search-5 <?php echo ($auto_complete == 'enable') ? 'auto-complete' : '' ; ?>">

	<div class="rem-search-fields">
		<div class="rem-visible-fields">

			<?php do_action( 'rem_search_property_before_fields', $fields_arr, $columns ); ?>
			
			<?php if (in_array('search', $fields_arr)) { ?>
				<div class="rem-search-field-wrap">
					<label for="keywords"><?php esc_attr_e( 'Keywords', 'real-estate-manager' ); ?></label>
					<input type="text" name="search_property" id="keywords" placeholder="Any" />
				</div>
			<?php } else {
				echo '<input value="" type="hidden" name="search_property" />';
			} ?>
			
			<?php
			if (($selected_key = array_search($tabs_field, $fields_arr)) !== false) {
			    unset($fields_arr[$selected_key]);
			}

			foreach ($default_fields as $field) {

				$show_condition = isset($field['show_condition']) ? $field['show_condition'] : 'true' ; 
				$conditions = isset($field['condition']) ? $field['condition'] : array() ;
				if (in_array($field['key'], $fields_arr) && 'property_price' != $field['key']){ ?>
					<div class="rem-search-field-wrap search-field" data-condition_status="<?php echo esc_attr($show_condition); ?>" data-condition_bound="<?php echo isset($field['condition_bound']) ? esc_attr($field['condition_bound']) : 'all' ?>" data-condition='<?php echo json_encode($conditions); ?>'>
						<?php rem_render_property_search_fields($field, 'widget', true); ?>
					</div>
				<?php }
			} ?>
			
			<?php foreach ($fields_arr as $key) { if( $key == 'order' || $key == 'tags' || $key == 'categories' || $key == 'orderby' || $key == 'agent'|| $key == 'property_id'){ ?>
					<div class="rem-search-field-wrap">
						<?php rem_render_special_search_fields($key, true); ?>
					</div>
			<?php }} ?>

			
			<?php if (in_array('property_price', $fields_arr)) { ?>
				<div class="p-slide-wrap rem-search-field-wrap">
					<?php rem_render_price_range_field('form', true); ?>
				</div>
			<?php } ?>

			<?php do_action( 'rem_search_property_after_fields', $fields_arr, $columns ); ?>

		</div>
		<div class="rem-hidden-fields-wrap">
			<div class="rem-hidden-fields">
				<?php
					if ($fields_to_hide != '' && !empty($filter_fields_arr)) {
						foreach ($default_fields as $field) {

							$show_condition = isset($field['show_condition']) ? $field['show_condition'] : 'true' ; 
							$conditions = isset($field['condition']) ? $field['condition'] : array() ;
							if (in_array($field['key'], $filter_fields_arr) && 'property_price' != $field['key']){ ?>
								<div class="rem-search-field-wrap search-field" data-condition_status="<?php echo esc_attr($show_condition); ?>" data-condition_bound="<?php echo isset($field['condition_bound']) ? esc_attr($field['condition_bound']) : 'all' ?>" data-condition='<?php echo json_encode($conditions); ?>'>
									<span id="span-<?php echo esc_attr($field['key']); ?>" data-text="<?php echo rem_wpml_translate($field['title'], 'real-estate-manager-fields'); ?>"></span>
									<?php rem_render_property_search_fields($field, 'widget', true); ?>
								</div>
							<?php }
						}

						foreach ($filter_fields_arr as $key) { if( $key == 'order' || $key == 'tags' || $key == 'categories' || $key == 'orderby' || $key == 'agent'|| $key == 'property_id'){ ?>
								<div class="rem-search-field-wrap margin-bottom">
									<?php rem_render_special_search_fields($key, true); ?>
								</div>
						<?php }}
					}
				?>
				<?php if ($filters_btn_text != '') { ?>
					<div class="features-cbs-search">
						<?php foreach ($property_individual_cbs as $cb) { ?>
							<div class="<?php echo esc_attr($more_filters_column_class); ?>">
								<?php
									$cb = stripcslashes($cb);
									$translated_text = rem_wpml_translate($cb, 'real-estate-manager-features');
								?>
								<input class="labelauty" type="checkbox" name="detail_cbs[<?php echo esc_attr($cb); ?>]" data-labelauty="<?php echo esc_attr($translated_text); ?>">
							</div>
						<?php } ?>
					</div>				
					<a class="more-filters-button" href="#">
						<span class="glyphicon glyphicon-plus"></span>
						<?php echo esc_attr($filters_btn_text); ?>
					</a>
				<?php } ?>			
			</div>
		</div>
	</div>

	<div class="rem-search-buttons">
		<div class="rem-buttons-wrap">
			<div class="rem-search-more">
				<a href="#" class="rem-toggle-adv">
					<i class="fa fa-search-plus"></i>
				</a>
			</div>	
			<div class="rem-search-btn">
				<button type="submit" class="search-button">
					<i class="fa fa-search"></i>
					<?php echo esc_attr($search_btn_text); ?>
				</button>
			</div>	
		</div>
	</div>
	
</div>

<style type="text/css">
	.rem-search-5 {
		display: flex;
	}
	.rem-search-5 .rem-search-fields {
		width: 80%;
	}
	.rem-search-5 .rem-search-buttons {
		width: 20%;
	}
	.rem-visible-fields {
		display: flex;
		flex-wrap: wrap;
	}
	.features-cbs-search {
		width: 100%;
		padding: 10px 0;
		background: white;
		border: 1px solid rgba(128, 128, 128, .2);
		display: none;
	}
	.rem-hidden-fields {
		display: flex;
		flex-wrap: wrap;
	}
	.rem-hidden-fields-wrap {
		display: none;
		width: 100%;
	}
	.rem-search-5 .more-filters-button {
		width: 100%;
		border: 1px solid rgba(128, 128, 128, .2);
		cursor: pointer;
		font-size: 13px;
		padding: 0 10px;
	}
	.rem-search-5 .rem-search-field-wrap {
		flex: 1 1 auto;
		width: 25%;
		padding: 0;
		background: #fff;
		border: 1px solid rgba(128, 128, 128, .2);
	}
	.rem-search-5 .rem-search-field-wrap label {
		padding: 18px 20px 10px;
		margin-bottom: 0;
		color: #444;
		display: block;
		font-weight: normal;
	}
	.rem-search-5 .rem-search-field-wrap input,
	.rem-search-5 .rem-search-field-wrap input:focus {
		padding: 0 20px;
		color: #444;
		display: block;
		width: 100%;
		font-size: 13px;
		height: 36px;
		outline: 0;
		box-shadow: none;
		background-color: rgba(0, 0, 0, 0);
		border: none;
	}
	.rem-search-5 .rem-search-field-wrap .nice-select {
		border: none;
		color: #444;
	}
	.rem-search-5 .rem-buttons-wrap {
		display: flex;
		flex-wrap: nowrap;
		width: 100%;
		justify-content: space-between;
	}
	.rem-search-5 .rem-search-more {
		position: relative;
		display: inline-block;
		width: 35%;
		text-align: center;
	}
	.rem-search-5 .rem-search-btn {
		position: relative;
		display: inline-block;
		width: 65%;
		text-align: center;
	}
	.rem-toggle-adv {
		display: flex;
		justify-content: center;
		align-items: center;
		width: 100%;
		height: 100%;
		color: #FFF;
		transition: background .2s linear;
	}
	.rem-toggle-adv:hover { text-decoration: none !important; }
	.rem-toggle-adv i { color: #FFF;  }
	.rem-search-5 .search-button {
		transition: background .2s linear;
		height: 87px;
		outline: none;
		border: none;
		width: 100%;
		color: #FFF;
	}
</style>

<script type="text/javascript">
	jQuery(document).ready(function($) {
        $('.rem-search-5').on('click', '.more-filters-button', function(event) {
        	event.preventDefault();
        	$('.features-cbs-search').slideToggle();
        });
        $('.rem-search-5').on('click', '.rem-toggle-adv', function(event) {
        	event.preventDefault();
        	$('.rem-hidden-fields-wrap').slideToggle();
        });
    });
</script>