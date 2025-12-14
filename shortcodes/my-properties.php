<div class="ich-settings-main-wrap">
    <div class="row">
        <div class="col-sm-12">
            <div class="row" style="margin-bottom:25px;">
                <div class="col-sm-12">
                    <form action="" method="GET">
                        <div class="row">
                            <!-- Listing Status Filter -->
                            <div class="col-sm-3">
                                <select name="listing_status" class="form-control">
                                    <option value="all"><?php esc_attr_e('All Status', 'real-estate-manager'); ?></option>
                                    <option value="publish" <?php echo (isset($_GET['listing_status']) && $_GET['listing_status'] == 'publish') ? 'selected' : ''; ?>>
                                        <?php esc_attr_e('Only Published', 'real-estate-manager'); ?>
                                    </option>
                                    <option value="pending" <?php echo (isset($_GET['listing_status']) && $_GET['listing_status'] == 'pending') ? 'selected' : ''; ?>>
                                        <?php esc_attr_e('Only Pending', 'real-estate-manager'); ?>
                                    </option>
                                    <option value="draft" <?php echo (isset($_GET['listing_status']) && $_GET['listing_status'] == 'draft') ? 'selected' : ''; ?>>
                                        <?php esc_attr_e('Only Draft', 'real-estate-manager'); ?>
                                    </option>
                                </select>
                            </div>
                            
                            <!-- Search Input -->
                            <div class="col-sm-4">
                                <input type="text" value="<?php echo isset($_GET['rem_search_query']) ? esc_attr($_GET['rem_search_query']) : ''; ?>" name="rem_search_query" class="form-control" placeholder="<?php esc_attr_e('Search for...', 'real-estate-manager'); ?>">
                            </div>
                            
                            <!-- Sorting Dropdown -->
                            <div class="col-sm-3">
                                <select name="sort_by" class="form-control">
                                    <option value="date-desc" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] == 'date-desc') ? 'selected' : ''; ?>>
                                        <?php esc_attr_e('Sort By Date Published', 'real-estate-manager'); ?>
                                    </option>
                                    <option value="modified-desc" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] == 'modified-desc') ? 'selected' : ''; ?>>
                                        <?php esc_attr_e('Sort By Date Modified', 'real-estate-manager'); ?>
                                    </option>
                                    <option value="title-asc" <?php echo (!isset($_GET['sort_by']) || $_GET['sort_by'] == 'title-asc') ? 'selected' : ''; ?>>
                                        <?php esc_attr_e('Sort By Title', 'real-estate-manager'); ?>
                                    </option>
                                    <option value="price-desc" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] == 'price-desc') ? 'selected' : ''; ?>>
                                        <?php esc_attr_e('Sort By Price: High to Low', 'real-estate-manager'); ?>
                                    </option>
                                    <option value="price-asc" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] == 'price-asc') ? 'selected' : ''; ?>>
                                        <?php esc_attr_e('Sort By Price: Low to High', 'real-estate-manager'); ?>
                                    </option>
                                </select>
                            </div>

                            <!-- Search Button -->
                            <div class="col-sm-2">
                                <button class="btn btn-default btn-block" type="submit"><?php esc_attr_e('Search', 'real-estate-manager'); ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="user-profile">
        <div class="table-responsive property-list">
            <table class="table-striped table-hover">
                <thead>
                    <tr>
                        <th><?php esc_attr_e('Thumbnail', 'real-estate-manager'); ?></th>
                        <th><?php esc_attr_e('Title', 'real-estate-manager'); ?></th>
                        <th class="hidden-xs"><?php esc_attr_e('Type', 'real-estate-manager'); ?></th>
                        <th class="hidden-xs hidden-sm"><?php esc_attr_e('Added', 'real-estate-manager'); ?></th>
                        <th class="hidden-xs"><?php esc_attr_e('Purpose', 'real-estate-manager'); ?></th>
                        <th><?php esc_attr_e('Status', 'real-estate-manager'); ?></th>
                        <th><?php esc_attr_e('Actions', 'real-estate-manager'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $current_user_data = wp_get_current_user();
                    $statuses = isset($_GET['listing_status']) && $_GET['listing_status'] != 'all' ? array(sanitize_text_field($_GET['listing_status'])) : array('pending', 'draft', 'future', 'publish');                    

                    $author_posts = get_posts(array(
                        'post_type'      => 'rem_property',
                        'posts_per_page' => -1,
                        'fields'         => 'ids',
                        'author'         => $current_user_data->ID,
                        'post_status'    => $statuses,
                    ));

                    $meta_query_posts = get_posts(array(
                        'post_type'      => 'rem_property',
                        'posts_per_page' => -1,
                        'fields'         => 'ids',
                        'post_status'    => $statuses,
                        'meta_query'     => array(
                            'relation' => 'AND',
                            array(
                                'key'     => 'rem_property_multiple_agents',
                                'value'   => '"' . $current_user_data->ID . '"',
                                'compare' => 'LIKE',
                            ),
                            array(
                                'key'     => 'rem_property_multiple_agents',
                                'compare' => 'EXISTS',
                            ),
                            array(
                                'key'     => 'rem_property_multiple_agents',
                                'value'   => 'a:0:{}',
                                'compare' => '!=',
                            ),
                        ),
                    ));

                    $merged_post_ids = array_unique(array_merge($author_posts, $meta_query_posts));

                    // Sorting Logic
                    $orderby = 'date';
                    $order = 'DESC';

                    if (isset($_GET['sort_by'])) {
                        switch ($_GET['sort_by']) {
                            case 'modified-desc':
                                $orderby = 'modified';
                                $order = 'DESC';
                                break;
                            case 'title-asc':
                                $orderby = 'title';
                                $order = 'ASC';
                                break;
                            case 'price-desc':
                                $orderby = 'meta_value_num';
                                $order = 'DESC';
                                $meta_query = array(
                                    array(
                                        'key' => 'rem_property_price',
                                        'compare' => 'EXISTS',
                                        'type' => 'NUMERIC'
                                    )
                                );
                                break;
                            case 'price-asc':
                                $orderby = 'meta_value_num';
                                $order = 'ASC';
                                $meta_query = array(
                                    array(
                                        'key' => 'rem_property_price',
                                        'compare' => 'EXISTS',
                                        'type' => 'NUMERIC'
                                    )
                                );
                                break;
                        }
                    }

                    $args = array(
                        'post__in'    => $merged_post_ids,
                        'post_type' => 'rem_property',
                        'posts_per_page' => 10,
                        'order' => $order,
                        'orderby' => $orderby,
                        'post_status' => $statuses,
                    );

                    // Apply meta_query if sorting by price
                    if (isset($meta_query)) {
                        $args['meta_query'] = $meta_query;
                    }

                    if (!empty($_GET['rem_search_query'])) {
                        $search_query = sanitize_text_field($_GET['rem_search_query']);
                        if (is_numeric($search_query)) {
                            $args['p'] = (int)$search_query; // Search by ID
                        } else {
                            $args['s'] = $search_query; // Default WP search
                        }
                    }

                    $paged = is_front_page() ? (get_query_var('page') ? get_query_var('page') : 1) : (get_query_var('paged') ? get_query_var('paged') : 1);
                    $args['paged'] = $paged;

                    $edit_page_id = rem_get_option('property_edit_page', 1);
                    $link_page = get_permalink( $edit_page_id );                    

                    $myproperties = new WP_Query($args);
                    if ($myproperties->have_posts()) {
                        while ($myproperties->have_posts()) {
                            $myproperties->the_post();
                            ?>
                            <tr>
                                <td class="img-wrap"><?php do_action('rem_property_picture', get_the_id(), 'thumbnail'); ?></td>
                                <td><a class="property-title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></td>
                                <td class="hidden-xs"><?php echo ucfirst(get_post_meta(get_the_id(), 'rem_property_type', true)); ?></td>
                                <td class="hidden-xs hidden-sm"><?php the_time('Y/m/d'); ?></td>
                                <td class="hidden-xs"><?php echo ucfirst(get_post_meta(get_the_id(), 'rem_property_purpose', true)); ?></td>
                                <td>
                                    <?php
                                    $p_status = get_post_status(get_the_id());
                                    $status_class = ($p_status == 'publish') ? 'label-success' : 'label-info';
                                    ?>
                                    <span class="label property-status <?php echo esc_attr($status_class); ?>"><?php esc_attr_e($p_status, 'real-estate-manager'); ?></span>
                                </td>
                                <td style="white-space: nowrap;">
                                    <a href="<?php echo esc_url( add_query_arg( 'property_id', get_the_id(), $link_page ) ); ?>" class="btn btn-info btn-sm">
                                        <i class="fas fa-pencil-alt"></i>
                                        <?php esc_attr_e('Edit', 'real-estate-manager'); ?>
                                    </a>
                                    <a href="#" class="btn btn-danger btn-sm delete-property" data-pid="<?php echo get_the_id(); ?>">
                                        <i class="fa fa-trash"></i>
                                        <?php esc_attr_e('Delete', 'real-estate-manager'); ?>
                                    </a>
                                </td>
                            </tr>
                            <?php
                        }
                        wp_reset_postdata();
                    } else {
                        echo '<tr><td colspan="7" class="text-center"><strong>' . esc_html__('No Properties Found.', 'real-estate-manager') . '</strong></td></tr>';
                    }
                    ?>
                </tbody>
            </table>
            <p class="text-center">
                <?php _e( 'Total Properties', 'real-estate-manager' ); ?>
                <?php echo count($merged_post_ids); ?>
            </p>
            <?php do_action('rem_pagination', $paged, $myproperties->max_num_pages); ?>
        </div>
    </div>
</div>
