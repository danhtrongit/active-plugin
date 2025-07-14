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

        // Kiểm tra các tham số đầu vào
        if ( empty( $license_key ) || empty( $domain ) || empty( $soft_id ) ) {
            $data = [
                "error"   => 100,
                "success" => false,
                "msg"     => "Yêu cầu không hợp lệ. Vui lòng cung cấp đủ thông tin License, Domain, và Soft_Id.",
            ];
            return rest_ensure_response( $data );
        }

        // Tìm kiếm license trong cơ sở dữ liệu
        $post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type='key_active'", $license_key ) );

        if ( ! $post_id ) {
            $data = [
                "error"   => 103,
                "success" => false,
                "msg"     => "Mã kích hoạt không tồn tại hoặc không hợp lệ.",
            ];
            return rest_ensure_response( $data );
        }

        // Lấy thông tin meta của license
        $meta_data = get_post_meta( $post_id );

        $registered_domain    = isset( $meta_data['domain'][0] ) ? $meta_data['domain'][0] : '';
        $registered_plugin_id = isset( $meta_data['plugin_id'][0] ) ? $meta_data['plugin_id'][0] : '';
        $plugin_name          = isset( $meta_data['plugin_name'][0] ) ? $meta_data['plugin_name'][0] : '';
        $version              = isset( $meta_data['version'][0] ) ? $meta_data['version'][0] : '';
        $status               = isset( $meta_data['status'][0] ) ? $meta_data['status'][0] : 'false';
        $expire               = isset( $meta_data['expire'][0] ) ? $meta_data['expire'][0] : '';

        // Xác thực thông tin
        if ( $registered_plugin_id !== $soft_id ) {
            $data = [
                "error"   => 101,
                "success" => false,
                "msg"     => "Sản phẩm (plugin/theme) không hợp lệ.",
            ];
            return rest_ensure_response( $data );
        }

        if ( $registered_domain !== $domain ) {
            $data = [
                "error"   => 102,
                "success" => false,
                "msg"     => "Tên miền sử dụng không hợp lệ.",
            ];
            return rest_ensure_response( $data );
        }

        if ( $status !== 'true' ) {
            $data = [
                "error"   => 104,
                "success" => false,
                "msg"     => "Mã kích hoạt đã bị vô hiệu hoá. Vui lòng liên hệ Zalo: 0813.908.901 để được hỗ trợ.",
            ];
            return rest_ensure_response( $data );
        }

        // Kiểm tra ngày hết hạn
        if ( ! empty( $expire ) && time() > strtotime( $expire ) ) {
            $data = [
                "error"   => 105,
                "success" => false,
                "msg"     => "Mã kích hoạt đã hết hạn.",
            ];
            return rest_ensure_response( $data );
        }

        // Chuẩn bị dữ liệu trả về khi thành công
        $response_data = [
            "error"   => 0,
            "success" => true,
            "msg"     => "Mã kích hoạt hợp lệ.",
            "plugin"  => [
                "ID"             => $registered_plugin_id,
                "Ten"            => $plugin_name,
                "PhienBanMoi"    => $version,
                "URLTaiXuong"    => "", // Có thể bổ sung sau
                "NhatKyThayDoi"  => "", // Có thể bổ sung sau
            ],
            "license" => [
                "MaKichHoat"     => $license_key,
                "MaSanPham"      => $registered_plugin_id,
                "TenSanPham"     => $plugin_name,
                "TenMien"        => $registered_domain,
                "NgayHetHan"     => $expire ? date( 'd-m-Y', strtotime( $expire ) ) : 'Vĩnh viễn',
                "TenKhachHang"   => isset( $meta_data['person'][0] ) ? $meta_data['person'][0] : 'Trần Danh Trọng',
                "EmailKhachHang" => isset( $meta_data['email'][0] ) ? $meta_data['email'][0] : 'codevnes@gmail.com',
                "TrangThai"      => "Đã kích hoạt",
            ],
        ];

        return rest_ensure_response( $response_data );
    }
}
