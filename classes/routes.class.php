<?php
/**
* REST API end points for mobile app
*/
class REM_REST_ROUTES
{
    
    function __construct(){
        add_action( 'rest_api_init', array($this, 'register_api_hooks') );
    }

    function register_api_hooks(){
        register_rest_route( 'rem', '/login', array( 'methods'  => 'POST','permission_callback' => '__return_true', 'callback' => array($this, 'api_login'), ) );
        register_rest_route( 'rem', '/stats', array( 'methods'  => 'POST','permission_callback' => array($this, 'check_permissions'), 'callback' => array($this, 'get_stats'), ) );
        register_rest_route( 'rem', '/properties', array( 'methods'  => 'POST','permission_callback' => array($this, 'check_permissions'), 'callback' => array($this, 'get_my_properties'), ) );
        register_rest_route( 'rem', '/all-properties', array( 'methods'  => 'POST','permission_callback' => array($this, 'check_permissions'), 'callback' => array($this, 'get_all_properties'), ) );
        register_rest_route( 'rem', '/property', array( 'methods'  => 'POST','permission_callback' => array($this, 'check_permissions'), 'callback' => array($this, 'get_single_property'), ) );
        register_rest_route( 'rem', '/get-fields', array( 'methods'  => 'POST','permission_callback' => array($this, 'check_permissions'), 'callback' => array($this, 'get_property_fields'), ) );
        register_rest_route( 'rem', '/create-property', array( 'methods'  => 'POST','permission_callback' => array($this, 'check_permissions'), 'callback' => array($this, 'create_property'), ) );
        register_rest_route( 'rem', '/upload-image', array( 'methods'  => 'POST','permission_callback' => array($this, 'check_permissions'), 'callback' => array($this, 'upload_image'), ) );
        register_rest_route( 'rem', '/delete-property', array( 'methods'  => 'POST','permission_callback' => array($this, 'check_permissions'), 'callback' => array($this, 'delete_property'), ) );
        register_rest_route( 'rem', '/contact-agent', array( 'methods'  => 'POST','permission_callback' => array($this, 'check_permissions'), 'callback' => array($this, 'send_email_to_agent'), ) );
    }

    function check_permissions(WP_REST_Request $request){
        $token = $request->get_param( 'token' );
        $user_id = $request->get_param( 'user' );
        $is_valid = $this->is_valid_request($token, $user_id);
        if($is_valid){
            return true;
        } else {
            return false;
        }
    }

    function get_single_property(WP_REST_Request $request){
        $property_id = $request->get_param( 'property_id' );
        global $rem_ob;
        $fields = $rem_ob->single_property_fields();
        $data = array();
        $resp = array();

        foreach ($fields as $field) {
            if (isset($field['key']) && get_post_meta( $property_id, 'rem_'.$field['key'], true ) != '') {
                $value = get_post_meta( $property_id, 'rem_'.$field['key'], true );
                if(is_array($value)){
                    $value = implode(" ", $value);
                }
                $data[] = array(
                    'title' => $field['title'],
                    'value' => $value,
                    'key'   => $field['key'],
                );   
            }
        }
        
        $property_details_cbs = get_post_meta( $property_id, 'rem_property_detail_cbs', true );
        $features = array();
        if(is_array($property_details_cbs)){
            foreach($property_details_cbs as $option_name => $value) { if($option_name != '') {
                $features[] = $option_name;
            } }             
        }
       

        $property_images = get_post_meta( $property_id, 'rem_property_images', true );

        if (is_array($property_images)) {
            foreach ($property_images as $id) {
                $image_url = wp_get_attachment_image_url($id, 'medium_large');

                if($image_url){
                    $resp['imgs'][] = array(
                        'image_url'   => $image_url,
                        'image_id' => $id,
                    );      
                }
                 
            }
        }        

        $resp['data'] = $data;
        $resp['features'] = $features;
        $content_post = get_post($property_id);
        $content = $content_post->post_content;
        $content = apply_filters('the_content', $content);
        $content = str_replace(']]>', ']]&gt;', $content);        
        $resp['content'] = wp_strip_all_tags($content);
        $resp['status'] = 'success';
        $resp['message'] = 'Operation Successful!';

        return $resp;
    }

    function get_property_fields(WP_REST_Request $request){
        $token = $request->get_param( 'token' );
        $user_id = $request->get_param( 'user' );
        $property_id = $request->get_param( 'property_id' );

        global $rem_ob;
        $inputFields = $rem_ob->single_property_fields();
        $tabsData = rem_get_single_property_settings_tabs();
        $valid_tabs = array();
        foreach ($tabsData as $tabData) {
            $tab_key = $tabData['key'];
            $tab_title = $tabData['title'];
            foreach ($inputFields as $field) {
                $field_tab = (isset($field['tab'])) ? $field['tab'] : '' ;
                if ($tab_key == $field_tab && !in_array($field_tab, $valid_tabs)) {
                   $valid_tabs[] = $field_tab; 
                }
            }
        }
        $fields_array = array();
        foreach ($tabsData as $tabData) {
            $tab_key = $tabData['key'];
            $tab_title = $tabData['title'];
            if ($tab_key != 'property_video' && $tab_key != 'property_attachments') {
                if (in_array($tab_key, $valid_tabs) && rem_is_tab_accessible($tabData)) {
                        
                    $fields_array[] = array( "type"=> "section", "title"=> $title,"key"=> $tab_key);
                    foreach ($inputFields as $field) {
                        if($field['tab'] == $tab_key && $field['accessibility'] != 'disable' ){
                            $arr_to_push = array( "type"=> $field['type'], "title"=> $field['title'],"key"=> $field['key']);
                            if (isset($field['options'])) {
                                $arr_to_push['options'] = $field['options'];
                            }
                            if( $property_id && get_post_meta($property_id, 'rem_'.$field['key'], true) != "" ){
                                $arr_to_push['value'] = get_post_meta($property_id, 'rem_'.$field['key'], true);
                            }
                            $fields_array[] = $arr_to_push;
                        }
                    }
                } 
            } 
        }
        
        $resp = array(
            'status' => 'success',
            'message' => 'Operation Successfull!',
            'data' => $fields_array,
        );
        
        if($property_id){
            $features = get_post_meta($property_id, 'rem_property_detail_cbs', true);
            if(is_array($features)){
                $features = array_keys($features);    
                $resp['features'] = $features; 
            }
            
            $resp['title'] = get_the_title($property_id);
            $content_post = get_post($property_id);
            $content = $content_post->post_content;
            $content = apply_filters('the_content', $content);
            $content = str_replace(']]>', ']]&gt;', $content);        
            $resp['content'] = wp_strip_all_tags($content);
            $resp['property_status'] = get_post_status($property_id);
            
            $property_images = get_post_meta( $property_id, 'rem_property_images', true );
        
            if (is_array($property_images)) {
                $resp['imgs'] = array();
                foreach ($property_images as $id) {
                    $image_url = wp_get_attachment_image_url($id, 'medium_large');
                    if($image_url){
                        $resp['imgs'][] = array(
                            'image_url'   => $image_url,
                            'image_id' => $id,
                        );      
                    }
                     
                }
            }            
        }
        


        return $resp;
    }

    function upload_image(WP_REST_Request $request){
        $tokne = $request->get_param( 'token' );
        $user_id = $request->get_param( 'user' );

        $imageData = $request->get_file_params();

        if (isset($imageData['photo'])) {
            $image_id = $this->upload_property_images($imageData['photo'], $user_id);
            if ($image_id) {
                $resp = array(
                    'status' => 'success',
                    'message' => 'Image is uploaded',
                    'data' => array(
                        'image_id' => $image_id,
                        'image_url' => wp_get_attachment_image_url($image_id, 'medium_large'),
                    ),
                );
                return $resp;
            }
        }

        return array( 'status' => 'failed', 'message' => 'There is some error uploading', 'data' => '' );
    }

    function create_property(WP_REST_Request $request){
        $token = $request->get_param( 'token' );
        $user_id = $request->get_param( 'user' );
        $property_id = $request->get_param( 'property_id' );
        
        $resp = array(
            'status' => 'failed',
            'message' => 'There is some error!',
            'data' => array(),
        );
        
        if (!$user_id) {
            return $resp;
        }

        if ($request->get_param( 'title' ) == '') {
            $resp['message'] = 'Please provide a title';
            return $resp;
        }
        
        $args = array(
            "post_title"    => $request->get_param( 'title' ),
            "post_type"     => "rem_property",
            "post_status"   => $request->get_param( 'status' ),
            'post_author'   => $user_id,
        );
        
        if($request->get_param( 'content' ) && $request->get_param( 'content' ) != ''){
            $args['post_content'] = $request->get_param( 'content' );
        }

        if($property_id){
            $args['ID'] = $property_id;
        }
        $post_id = wp_insert_post( $args );

        $fields = $request->get_param( 'fields_data' );
        if (!empty($fields)) {
            $fields = json_decode($fields);
            foreach ($fields as $key => $value) {
                
                update_post_meta( $post_id, 'rem_'.$key, $value );
            }
        }
        
        $features = $request->get_param( 'features' );
        $features = json_decode($features);
        if (!empty($features)) {
            $selected_features = array();
            foreach ($features as $feature) {
                $selected_features[$feature] = 'on' ;
            }
        }
        update_post_meta( $post_id, 'rem_property_detail_cbs', $selected_features );

        
        $images = $request->get_param( 'images' );

        if(is_array($images)){
            $image_ids = array();
            foreach ($images as $singleImage) {
                $image_ids[] = $singleImage['image_id'];
            }
            update_post_meta( $post_id, 'rem_property_images', $image_ids );
        }
        
        $resp = array(
            'status' => 'success',
            'message' => 'Property is created with ID '.$post_id.' and status '.$request->get_param( 'status' ),
            'data' => $post_id,
        );
        if($property_id){
            $resp['message'] = 'Property Data Updated!';
        }
        return $resp;

    }

    function delete_property(WP_REST_Request $request){
        $token = $request->get_param( 'token' );
        $user_id = $this->is_valid_request($token);
        $property_id = $request->get_param( 'property_id' );

        $resp = array(
            'status' => 'failed',
            'message' => 'There is some error!',
            'data' => array(),
        );
        if (!$user_id) {
            return $resp;
        }

        if (get_post_field( 'post_author', $property_id ) == $user_id) {
            if (rem_get_option('attachment_deletion', 'remain') == 'delete') {
                $gallery_images = get_post_meta( $property_id, 'rem_property_images', true );
                if (is_array($gallery_images)) {
                    foreach ($gallery_images as $key => $id) {
                        wp_delete_attachment( $id, false );
                    }
                }
            }
            if (rem_get_option('property_deletion', 'delete') == 'trash') {
                wp_trash_post( $property_id );
            } else {
                wp_delete_post( $property_id, true );
            }
            $resp = array(
                'status' => 'success',
                'message' => 'Deleted',
                'data' => array(),
            );
        } else {
            $resp = array(
                'status' => 'failed',
                'message' => 'Sorry! You can not delete this property.',
                'data' => array(),
            );
        }
        return $resp;

    }
    
    function send_email_to_agent(WP_REST_Request $request){
        $property_id    = $request->get_param( 'property_id' );
        $clientName     = $request->get_param( 'clientName' );
        $clientemail    = $request->get_param( 'clientEmail' );
        $clientMessage  = $request->get_param( 'clientMessage' );

        $author_id = get_post_field ('post_author', $property_id);
        $agent_info = get_userdata($author_id);
        $agent_email = $agent_info->user_email;

        $subject = get_the_title($property_id);;

        $headers = array();
        $headers[] = "From: {$clientName} <{$clientemail}>";
        $headers[] = "Content-Type: text/html";
        $headers[] = "MIME-Version: 1.0\r\n";
        if (wp_mail( $agent_email, $subject, $clientMessage, $headers )) {
            $resp = array('status' => 'sent', 'message' => __( 'Email Sent Successfully', 'real-estate-manager'  ) );
        } else {
            $resp = array('status' => 'fail', 'message' => __( 'There is some problem, please try later', 'real-estate-manager' ) );
        }
        return $resp;
    }

    function upload_property_images($photo, $user_id){

        if ( isset($photo['name']) ) {
            $file = array( 
                'name' => $photo['name'],
                'type' => $photo['type'], 
                'tmp_name' => $photo['tmp_name'], 
                'error' => $photo['error'],
                'size' => $photo['size']
            );
            $_FILES = array("rem_photo" => $file);
            require_once(ABSPATH . "wp-admin" . '/includes/image.php');
            require_once(ABSPATH . "wp-admin" . '/includes/file.php');
            require_once(ABSPATH . "wp-admin" . '/includes/media.php');
            foreach ($_FILES as $file => $array) {
                $attach_id = media_handle_upload( $file, 0 );
            }
            if($attach_id){
                $property_image = array(
                    'ID'           => $attach_id,
                    'post_author' => $user_id
                );
                wp_update_post( $property_image );
                return $attach_id;
            } else {
                return false;
            }
        }
        
        return false;
    }

    function get_my_properties(WP_REST_Request $request){

        $user_id = $request->get_param( 'user' );
        $page = $request->get_param( 'page' );

        $myproperties = new WP_Query( array(
            'author' => $user_id,
            'post_type' => 'rem_property',
            'posts_per_page' => 10,
            'paged' => $page,
            'post_status'    => array('draft', 'publish', 'pending'),
        ) );

        $properties = array();
        if( $myproperties->have_posts() ){
            $resp['maxpages'] = $myproperties->max_num_pages;
            while( $myproperties->have_posts() ){ 
                $myproperties->the_post();
                $posted = get_the_time('U');
                $properties[] = array(
                    'title' => html_entity_decode(get_the_title()),
                    'id' => get_the_id(),
                    'excerpt' => get_the_excerpt(),
                    'date' => human_time_diff($posted, current_time( 'U' )). " ago",
                    'img' => get_the_post_thumbnail_url( get_the_id(), 'medium_large' ),
                    'status' => get_post_status(get_the_id()),
                );
            }
            wp_reset_postdata();
        } else {
            $resp['message'] = 'No Properties Found!';
            return $resp;
        }
        $resp['message'] = 'Operation Successful!';
        $resp['status'] = 'success';
        $resp['data'] = $properties;

        return $resp;
    }

    function get_all_properties(WP_REST_Request $request){

        $page = $request->get_param( 'page' );

        $myproperties = new WP_Query( array(
            'post_type' => 'rem_property',
            'posts_per_page' => 10,
            'paged' => $page,
            'post_status'    => array('publish'),
        ) );

        $properties = array();
        if( $myproperties->have_posts() ){
            $resp['maxpages'] = $myproperties->max_num_pages;
            while( $myproperties->have_posts() ){ 
                $myproperties->the_post();
                $posted = get_the_time('U');
                $properties[] = array(
                    'title' => html_entity_decode(get_the_title()),
                    'id' => get_the_id(),
                    'excerpt' => get_the_excerpt(),
                    'date' => human_time_diff($posted, current_time( 'U' )). " ago",
                    'img' => get_the_post_thumbnail_url( get_the_id(), 'medium_large' ),
                    'status' => get_post_status(get_the_id()),
                    'price' => get_post_meta(get_the_id(), 'rem_property_price', true),
                    'address' => get_post_meta(get_the_id(), 'rem_property_address', true),
                );
            }
            wp_reset_postdata();
        } else {
            $resp['message'] = 'No Properties Found!';
            return $resp;
        }
        $resp['message'] = 'Operation Successful!';
        $resp['status'] = 'success';
        $resp['data'] = $properties;

        return $resp;
    }

    function api_login(WP_REST_Request $request){
        
        $username = $request->get_param( 'username' );
        $password = $request->get_param( 'password' );
        $fromQR = $request->get_param( 'fromqr' );
        
        $response = array(
            'data'      => array(),
            'message'   => 'Invalid email or password',
            'status'    => 'failed'
        );
        
        
        if( $username != '' && $password != '' ){
            
            if ( $this->checkValidEmail($username) ) {
                $user = get_user_by( 'email', $username );   
            } else {
                $user = get_user_by( 'login', $username);
            }

            if ( $user ){
                
                if ($fromQR == 'YES' && get_user_meta( $user->ID, 'rem_barcode_access_token', true ) == $password) {
                    $password_check = true;    
                } else {
                    $password_check = wp_check_password( $password, $user->user_pass, $user->ID );
                }


                if ( $password_check ){

                    /* Generate a unique auth token */
                    $token = bin2hex(openssl_random_pseudo_bytes(64));
                    
                    if( update_user_meta( $user->ID, 'rem_auth_token', $token ) ){
                        $post_status = $this->check_post_stats($user->ID);
                        if(get_the_author_meta( 'rem_agent_meta_image', $user->ID ) != '') {
                            $agent_pic = esc_url_raw( get_the_author_meta( 'rem_agent_meta_image', $user->ID ) );
                        } else {
                            $agent_pic = get_avatar_url( $user->ID , 256 );
                        }
                        $response['status'] = 'success';
                        $response['data'] = array(
                            'rem_auth_token'    =>  $token,
                            'user_id'           =>  $user->ID,
                            'user_login'        =>  $user->user_login,
                            'user_display'        =>  $user->display_name,
                            'user_avatar'        =>  $agent_pic,
                        );
                        $response['message'] = 'Successfully Authenticated';
                    }
                }
            }
        }

        return $response;
    }
     
    function get_stats(WP_REST_Request $request){
        
        $token = $request->get_param( 'token' );
        $user_id = $request->get_param( 'user' );
        $resp = array(
            'status' => 'failed',
            'message' => 'There is something wrong',
        );

        if ($user_id) {
            $post_stats = $this->check_post_stats($user_id);
            $resp = array(
                'status' => 'success',
                'message' => 'Success',
                'data' => $post_stats,
            );
        }
        return $resp;
    }

    function is_valid_request($token, $user_id){
        $saved_token = get_user_meta( $user_id, 'rem_auth_token', true );
        if($saved_token === $token){
            return true;
        } else {
            return false;
        }
    }

    function checkValidEmail($email) {
       $find1 = strpos($email, '@');
       $find2 = strpos($email, '.');
       return ($find1 !== false && $find2 !== false && $find2 > $find1);
    }
    
    function check_post_stats($user_id){
        $post_args = array(
            'post_type' => 'rem_property',
            'posts_per_page' => -1,
            'author'    => $user_id,
            'post_status'    => array('draft', 'publish', 'pending'),
        );
        
        $ps = get_posts($post_args);
    
        $pending_pps = 0;
        $published_pps = 0;
        $draft_pps = 0;
        
        if (is_array($ps)) {
                
            foreach ($ps as $key => $post) {
                
                switch ($post->post_status) {
                    case 'publish':
                        $published_pps += 1;            
                        break;
                    case 'draft':
                        $draft_pps += 1;            
                        break;
                    case 'pending':
                        $pending_pps += 1;            
                        break;
                }
            }
        }
        
        $prop_stats = array(
            'publish' => $published_pps,
            'pending' => $pending_pps,
            'draft' => $draft_pps,
        );
        return $prop_stats;
    }
}

?>