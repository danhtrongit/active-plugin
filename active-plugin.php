<?php
/**
 * Plugin Name:       AM
 * Plugin URI:        https://example.com/
 * Description:       A simple, restructured plugin.
 * Version:           1.0.0
 * Author:            Your Name
 * Author URI:        https://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       active-plugin
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Register Custom Post Type for Key Active
 */
function custom_key_active_post_type() {
    $labels = array(
        'name'               => 'Key Active',
        'singular_name'      => 'Key Active',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Key Active',
        'edit_item'          => 'Edit Key Active',
        'new_item'           => 'New Key Active',
        'all_items'          => 'All Key Active',
        'view_item'          => 'View Key Active',
        'search_items'       => 'Search Key Active',
        'not_found'          => 'No Key Active found',
        'not_found_in_trash' => 'No Key Active found in Trash',
        'menu_name'          => 'Key Active',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'key_active' ),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title' ),
    );

    register_post_type( 'key_active', $args );
}
add_action( 'init', 'custom_key_active_post_type' );

/**
 * Add Meta Box for Key Active Details
 */
function add_key_active_metabox() {
    add_meta_box( 
        'key_active_metabox', 
        'Key Active Details', 
        'key_active_metabox_callback', 
        'key_active', 
        'normal', 
        'high' 
    );
}
add_action( 'add_meta_boxes', 'add_key_active_metabox' );

/**
 * Meta Box Callback Function
 */
function key_active_metabox_callback( $post ) {
    wp_nonce_field( 'save_key_active_metabox', 'key_active_nonce' );

    $email       	= get_post_meta( $post->ID, 'email', true );
    $person      	= get_post_meta( $post->ID, 'person', true );
    $plugin_id   	= get_post_meta( $post->ID, 'plugin_id', true );
    $plugin_name 	= get_post_meta( $post->ID, 'plugin_name', true );
    $domain      	= get_post_meta( $post->ID, 'domain', true );
    $version      	= get_post_meta( $post->ID, 'version', true );
    $status      	= get_post_meta( $post->ID, 'status', true );
    $expire      	= get_post_meta( $post->ID, 'expire', true );
    ?>
    <p>
        <label for="email">Email:</label>
        <input type="text" id="email" name="email" value="<?php echo esc_attr( $email ); ?>" class="widefat">
    </p>
    <p>
        <label for="person">Person:</label>
        <input type="text" id="person" name="person" value="<?php echo esc_attr( $person ); ?>" class="widefat">
    </p>
    <p>
        <label for="plugin_id">Plugin ID:</label>
        <input type="text" id="plugin_id" name="plugin_id" value="<?php echo esc_attr( $plugin_id ); ?>" class="widefat">
    </p>
    <p>
        <label for="plugin_name">Plugin Name:</label>
        <input type="text" id="plugin_name" name="plugin_name" value="<?php echo esc_attr( $plugin_name ); ?>" class="widefat">
    </p>
    <p>
        <label for="domain">Domain:</label>
        <input type="text" id="domain" name="domain" value="<?php echo esc_attr( $domain ); ?>" class="widefat">
    </p>
    <p>
        <label for="version">Version:</label>
        <input type="text" id="version" name="version" value="<?php echo esc_attr( $version ); ?>" class="widefat">
    </p>
    <p>
        <label for="status">Status:</label>
        <input type="checkbox" id="status" name="status" <?php checked( $status, 'true', true ); ?>>
    </p>
    <p>
        <label for="expire">Expire:</label>
        <input type="date" id="expire" name="expire" value="<?php echo esc_attr( $expire ); ?>">
    </p>
    <?php
}

/**
 * Save Meta Box Data
 */
function save_key_active_metabox( $post_id ) {
    if ( ! isset( $_POST['key_active_nonce'] ) || ! wp_verify_nonce( $_POST['key_active_nonce'], 'save_key_active_metabox' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $fields = [ 'email', 'person', 'plugin_id', 'plugin_name', 'domain', 'version', 'expire' ];

    foreach ( $fields as $field ) {
        if ( isset( $_POST[$field] ) ) {
            update_post_meta( $post_id, $field, sanitize_text_field( $_POST[$field] ) );
        }
    }

    $status = isset( $_POST['status'] ) && $_POST['status'] === 'on' ? 'true' : 'false';
    update_post_meta( $post_id, 'status', $status );
}
add_action( 'save_post_key_active', 'save_key_active_metabox' );


/**
 * Register REST API Endpoint for Key Check
 */
function register_license_check_endpoint() {
    register_rest_route(
        'license/v2',
        '/check',
        array(
            'methods'  => 'GET',
            'callback' => 'check_key_active',
            'permission_callback' => '__return_true' // Public endpoint
        )
    );
}
add_action( 'rest_api_init', 'register_license_check_endpoint' );

/**
 * Callback function for the license check endpoint.
 */
function check_key_active( WP_REST_Request $request ) {
    global $wpdb;

    $license_key = $request->get_param( 'License' );
    $domain      = $request->get_param( 'Domain' );
    $soft_id     = $request->get_param( 'Soft_Id' );
    
    if ( empty( $license_key ) || empty( $domain ) || empty( $soft_id ) ) {
        return new WP_Error( 'missing_params', 'Required parameters are missing.', array( 'status' => 400 ) );
    }

    $post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type='key_active'", $license_key ) );

    if ( ! $post_id ) {
        return new WP_Error( 'invalid_license', "License: {$license_key} - is not valid.", array( 'status' => 404, 'error' => 103 ) );
    }

    $meta_data = get_post_meta( $post_id );

    $registered_domain = isset( $meta_data['domain'][0] ) ? $meta_data['domain'][0] : '';
    $registered_plugin_id = isset( $meta_data['plugin_id'][0] ) ? $meta_data['plugin_id'][0] : '';
    $status = isset( $meta_data['status'][0] ) ? $meta_data['status'][0] : 'false';
    
    if ( $registered_plugin_id !== $soft_id ) {
        return new WP_Error( 'invalid_plugin', 'Invalid plugin.', array( 'status' => 403, 'error' => 101 ) );
    }

    if ( $registered_domain !== $domain ) {
        return new WP_Error( 'invalid_domain', 'Invalid domain.', array( 'status' => 403, 'error' => 102 ) );
    }
    
    if ( $status !== 'true' ) {
        return new WP_Error( 'license_disabled', 'License is disabled. Contact Zalo: 0813.908.901.', array( 'status' => 403, 'error' => 104 ) );
    }

    $expire = isset( $meta_data['expire'][0] ) ? $meta_data['expire'][0] : '';
    $is_expired = ! empty( $expire ) && time() > strtotime( $expire );

    if ( $is_expired ) {
        return new WP_Error( 'license_expired', 'License has expired.', array( 'status' => 403, 'error' => 105 ) );
    }

    // Success response
    $response_data = [
        "error"   => 0,
        "success" => true,
        "msg"     => "License: " . $license_key . " - Valid",
        "license" => [
            "License"        => $license_key,
            "Soft_Id"        => $registered_plugin_id,
            "Soft_Name"      => isset( $meta_data['plugin_name'][0] ) ? $meta_data['plugin_name'][0] : '',
            "Domain"         => $registered_domain,
            "Expiry_Date"    => $expire ? date( 'd-m-Y', strtotime( $expire ) ) : 'Never',
            "Customer_Name"  => isset( $meta_data['person'][0] ) ? $meta_data['person'][0] : 'Tran Danh Trong',
            "Customer_Email" => isset( $meta_data['email'][0] ) ? $meta_data['email'][0] : 'codevnes@gmail.com',
            "Status"         => "Active",
        ],
        "plugin" => [
            "ID"             => $registered_plugin_id,
            "Name"           => isset( $meta_data['plugin_name'][0] ) ? $meta_data['plugin_name'][0] : '',
            "Newest_Version" => isset( $meta_data['version'][0] ) ? $meta_data['version'][0] : '',
        ]
    ];

    return rest_ensure_response( $response_data );
} 