<?php

class Key_Active_REST_API {

    public function __construct() {
        add_action( 'rest_api_init', array( $this, 'register_endpoint' ) );
    }

    public function register_endpoint() {
        register_rest_route(
            'license/v2',
            '/check',
            array(
                'methods'             => 'GET',
                'callback'            => array( $this, 'check_key' ),
                'permission_callback' => '__return_true' // Public endpoint
            )
        );
    }

    public function check_key( WP_REST_Request $request ) {
        global $wpdb;

        $license_key = $request->get_param( 'License' );
        $domain      = $request->get_param( 'Domain' );
        $soft_id     = $request->get_param( 'Soft_Id' );

        if ( empty( $license_key ) || empty( $domain ) || empty( $soft_id ) ) {
            return new WP_Error( 'missing_params', 'Thiếu các tham số bắt buộc.', array( 'status' => 400 ) );
        }

        $post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type='key_active'", $license_key ) );

        if ( ! $post_id ) {
            return new WP_Error( 'invalid_license', "Giấy phép: {$license_key} - không hợp lệ.", array( 'status' => 404, 'error' => 103 ) );
        }

        $meta_data = get_post_meta( $post_id );

        $registered_domain    = isset( $meta_data['domain'][0] ) ? $meta_data['domain'][0] : '';
        $registered_plugin_id = isset( $meta_data['plugin_id'][0] ) ? $meta_data['plugin_id'][0] : '';
        $status               = isset( $meta_data['status'][0] ) ? $meta_data['status'][0] : 'false';

        if ( $registered_plugin_id !== $soft_id ) {
            return new WP_Error( 'invalid_plugin', 'Plugin không hợp lệ.', array( 'status' => 403, 'error' => 101 ) );
        }

        if ( $registered_domain !== $domain ) {
            return new WP_Error( 'invalid_domain', 'Tên miền không hợp lệ.', array( 'status' => 403, 'error' => 102 ) );
        }

        if ( $status !== 'true' ) {
            return new WP_Error( 'license_disabled', 'Giấy phép đã bị vô hiệu hóa. Liên hệ Zalo: 0813.908.901.', array( 'status' => 403, 'error' => 104 ) );
        }

        $expire     = isset( $meta_data['expire'][0] ) ? $meta_data['expire'][0] : '';
        $is_expired = ! empty( $expire ) && time() > strtotime( $expire );

        if ( $is_expired ) {
            return new WP_Error( 'license_expired', 'Giấy phép đã hết hạn.', array( 'status' => 403, 'error' => 105 ) );
        }

        // Success response
        $response_data = [
            "error"   => 0,
            "success" => true,
            "msg"     => "Giấy phép: " . $license_key . " - Hợp lệ",
            "license" => [
                "License"        => $license_key,
                "Soft_Id"        => $registered_plugin_id,
                "Soft_Name"      => isset( $meta_data['plugin_name'][0] ) ? $meta_data['plugin_name'][0] : '',
                "Domain"         => $registered_domain,
                "Expiry_Date"    => $expire ? date( 'd-m-Y', strtotime( $expire ) ) : 'Vĩnh viễn',
                "Customer_Name"  => isset( $meta_data['person'][0] ) ? $meta_data['person'][0] : 'Trần Danh Trọng',
                "Customer_Email" => isset( $meta_data['email'][0] ) ? $meta_data['email'][0] : 'codevnes@gmail.com',
                "Status"         => "Đã kích hoạt",
            ],
            "plugin"  => [
                "ID"             => $registered_plugin_id,
                "Name"           => isset( $meta_data['plugin_name'][0] ) ? $meta_data['plugin_name'][0] : '',
                "Newest_Version" => isset( $meta_data['version'][0] ) ? $meta_data['version'][0] : '',
            ]
        ];

        return rest_ensure_response( $response_data );
    }
}
