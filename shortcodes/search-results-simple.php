<?php if(isset($_GET['simple_search'])){
    $search_string = strtolower($_GET['simple_search']);
    $rem_search_in = (isset($_GET['rem_search_in'])) ? $_GET['rem_search_in'] : '' ;
    $rem_cat = (isset($_GET['rem_cat'])) ? $_GET['rem_cat'] : '' ;
    $ppp = rem_get_option('properties_per_page', -1);   ?>
	<div class="ich-settings-main-wrap">
    <div class="row">
		<?php
        $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;

		$args = array(
            'post_type'   => 'rem_property',
            'paged'       => $paged,
            'post_status' => 'publish',
            'order'       => $order,
            'orderby'       => $orderby,
            'posts_per_page' => $ppp,
        );

        if (rem_get_option('simple_search_query', '') != '') {
            $simple_search_query = rem_get_option('simple_search_query');
            $all_queries = explode("\n", $simple_search_query);
            $args['meta_query'] = array();
            foreach ($all_queries as $s_query) {
                if ($s_query != '') {
                    $meta_q = explode(",", $s_query);
                    $text = (isset($meta_q[0])) ? strtolower($meta_q[0]) : '' ;
                    $meta_key = (isset($meta_q[1])) ? 'rem_'.$meta_q[1] : '' ;
                    $is_number = (isset($meta_q[2]) && trim($meta_q[2]) == 'true') ? true : false ;
                    $op_value = (isset($meta_q[3])) ? trim($meta_q[3]) : '' ;
                    if (strpos($search_string, $text) !== false && $is_number == true) {
                        $value = str_replace($text, "", $search_string);
                        $value_int = str_replace(' ', '', $value);
                        $args['meta_query'][] = array(
                            'key'     => $meta_key,
                            'value'   => intval($value_int),
                            'compare' => 'LIKE',
                        );
                    } elseif (strpos($search_string, $text) !== false && $is_number == false) {
                        $args['meta_query'][] = array(
                            'key'     => $meta_key,
                            'value'   => ($op_value != '') ? $op_value : $text,
                            'compare' => 'LIKE',
                        );
                    }
                }
            }
        }

        if ($rem_search_in != '') {
            $search_fields = explode(",", $rem_search_in);
            foreach ($search_fields as $field_name) {
                $args['meta_query'][] = array(
                    'key' => 'rem_'.trim($field_name),
                    'value' => $search_string,
                    'compare' => 'LIKE'
                );
            }
            if(count($args['meta_query']) > 1) {
                $args['meta_query']['relation'] = 'OR';
            }
        }

        if (empty($args['meta_query'])) {
            $args['s'] = $search_string;
        }
        $args = apply_filters( 'rem_simple_search_args', $args, $rem_search_in, $rem_cat );
		$property_query = new WP_Query( $args );
        $layout_style = rem_get_option('search_results_style', '1');
        $layout_style = apply_filters( 'rem_search_results_box_style', $layout_style, get_the_id() );
        $layout_cols = rem_get_option('search_results_cols', 'col-sm-12');  
        $target = rem_get_option('searched_properties_target', '');    
			if( $property_query->have_posts() ){
                if (!isset($args['offset'])) { ?>
                    <div class="filter-title col-sm-12">
                        <h2>
                            <?php
                                $heading = rem_get_option('s_results_h', 'Search Results (%count%)');
                                $heading = str_replace('%count%', '<span class="rem-results-count">'.$property_query->found_posts.'</span>', $heading);
                                echo wp_kses_post($heading);
                            ?>
                        </h2>
                    </div>
                <?php }                
        while( $property_query->have_posts() ){ $property_query->the_post(); 
		 		?>
            <div id="property-<?php echo get_the_id(); ?>" class="<?php echo esc_attr($layout_cols); ?>">
                <?php do_action('rem_property_box', get_the_id(), $layout_style, $target); ?>
            </div>
        <?php

		 	}
			wp_reset_postdata();
      echo '<div class="clearfix"></div>';
      do_action( 'rem_pagination', $paged, $property_query->max_num_pages );
	} else { ?>
  <div class="clearfix"></div>
    <div class="col-md-12">
        <div class="alert with-icon alert-info" role="alert">
            <p style="margin-top: 12px;margin-left: 10px;">
            <i class="icon fa fa-info"></i> <?php esc_attr_e( 'Sorry! No Properties Found. Try Searching Again.', 'real-estate-manager' ); ?></p>
        </div>
    </div>
	<?php } ?>
  </div>
	</div>
<?php } ?>