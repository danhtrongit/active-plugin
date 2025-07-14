<?php
add_action( 'rest_api_init', function () {
    register_rest_route(
        'license/v2',
        '/check',
        array(
            'methods'  => 'GET',
            'callback' => 'check_key_active',
            'permission_callback' => '__return_true'
        )
    );
} );


function check_key_active(WP_REST_Request $request)
{
    global $wpdb;

    $License = $request->get_param( 'License' );
    $Domain  = $request->get_param( 'Domain' );
    $Soft_Id = $request->get_param( 'Soft_Id' );
    
    // Dữ liệu trả về mặc định khi thành công
    $data    = [
        "error"   => 0,
        "success" => true,
        "msg"     => "Giấy phép: " . $License . " - Hợp lệ",
    ];

    $post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type='key_active'", $License ) );

    if ( $post_id ) {
        $active_key = get_post( $post_id, OBJECT );
        $meta_data  = get_post_meta( $active_key->ID );

        $domain      = isset($meta_data['domain'][0]) ? $meta_data['domain'][0] : '';
        $plugin_id   = isset($meta_data['plugin_id'][0]) ? $meta_data['plugin_id'][0] : '';
        $plugin_name = isset($meta_data['plugin_name'][0]) ? $meta_data['plugin_name'][0] : '';
        $version	 = isset($meta_data['version'][0]) ? $meta_data['version'][0] : '';
        $status      = isset($meta_data['status'][0]) ? $meta_data['status'][0] : 'false';
        $expire      = isset($meta_data['expire'][0]) ? $meta_data['expire'][0] : '';

        if ( $plugin_id !== $Soft_Id ) {
            $data = [
                "error"   => 101,
                "success" => false,
                "msg"     => "Plugin không hợp lệ.",
            ];
            return rest_ensure_response( $data );
        }

        if ( $domain !== $Domain ) {
            $data = [
                "error"   => 102,
                "success" => false,
                "msg"     => "Tên miền không hợp lệ.",
            ];
            return rest_ensure_response( $data );
        }

        if ( $status !== 'true' ) {
            $data = [
                "error"   => 104, // Changed from 102 to avoid conflict
                "success" => false,
                "msg"     => "Giấy phép đã bị vô hiệu hoá! Vui lòng liên hệ Zalo: 0813.908.901.",
            ];
            return rest_ensure_response( $data );
        }
        
        $is_expired = ! empty( $expire ) && time() > strtotime( $expire );
        if ( $is_expired ) {
            $data = [
                "error"   => 105,
                "success" => false,
                "msg"     => "Giấy phép đã hết hạn.",
            ];
            return rest_ensure_response( $data );
        }

        $plugin = [
            "row"             => $active_key->ID,
            "ID"              => $plugin_id,
            "Name"            => $plugin_name,
            "Price"           => 0,
            "Download_Url"    => "",
            "Change_Log"      => "",
            "Newest_Version"  => $version,
            "Update New Core" => "",
        ];

        $license_status = ($status === 'true') ? 'Active' : 'Inactive';
        $expiry_date_formatted = $expire ? date( 'd-m-Y', strtotime( $expire ) ) : 'Vĩnh viễn';
        
        $license = [
            "row"            => $active_key->ID,
            "License"        => $License,
            "Soft_Id"        => $plugin_id,
            "Soft_Name"      => $plugin_name,
            "Price"          => "",
            "Domain"         => $domain,
            "Expiry_Date"    => $expiry_date_formatted,
            "Customer_Name"  => isset($meta_data['person'][0]) ? $meta_data['person'][0] : "Tran Danh Trong",
            "Customer_Phone" => "0813908901",
            "Customer_Email" => isset($meta_data['email'][0]) ? $meta_data['email'][0] : "codevnes@gmail.com",
            "Status"         => $license_status,
            "Expiry_C_Use"   => "",
            "Timestamp"      => $expire ? strtotime($expire) : '',
            "Note"           => "",
        ];
        
        $data['plugin']  = $plugin;
        $data['license'] = $license;
        
    } else {
        $data = [
            "error"   => 103,
            "success" => false,
            "msg"     => "Giấy phép: " . $License . " - không hợp lệ",
        ];
    }
    
    return rest_ensure_response( $data );
}
