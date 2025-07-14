<?php
add_action( 'rest_api_init', function () {
    register_rest_route(
        'license/v2',
        '/check',
        array(
            'methods'  => 'GET',
            'callback' => 'check_key_active',
        )
    );
    } );


function check_key_active(WP_REST_Request $request)
    {
    global $wpdb;

    $License = $request->get_param( 'License' );
    $Domain  = $request->get_param( 'Domain' );
    $Soft_Id = $request->get_param( 'Soft_Id' );
    $data    = [
        "error"   => 100,
        "success" => true,
        "msg"     => "License: " . $License . " - Hợp lệ",
    ];

    $post = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type='key_active'", $License ) );

    if ( $post ) {
        $active_key = get_post( $post, OBJECT );
        $meta_data  = get_post_meta( $active_key->ID ); // Retrieve all post meta

        $domain      = isset($meta_data['domain'][0]) 		? $meta_data['domain'][0] : '';
        $plugin_id   = isset($meta_data['plugin_id'][0]) 	? $meta_data['plugin_id'][0] : '';
        $plugin_name = isset($meta_data['plugin_name'][0]) 	? $meta_data['plugin_name'][0] : '';
        $version	 = isset($meta_data['version'][0]) 		? $meta_data['version'][0] : '';
		
        $status      = $meta_data['status'][0];
        $expire      = $meta_data['expire'][0];

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

		if ( $status == 'true' ) {
			$status = "Active";
		} else {
			$status = "Inactive";
			 $data = [
                "error"   => 102,
                "success" => false,
                "msg"     => "Bị vô hiệu hoá rùi! Liên hệ Zalo: <strong style='color:green'> 0813.908.901 </strong>.",
            ];
            return rest_ensure_response( $data );
		}
        $date     = '';
        $isExpire = !isset($meta_data['expire']) || $meta_data['expire'][0] == '';

        if ( $isExpire ) {
            $date = date_create();
            date_add( $date, date_interval_create_from_date_string( "999 years" ) );
            } else {
            $date = date_create( $meta_data['expire'][0] );
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

        $license         = [
            "row"            => $active_key->ID,
            "License"        => $License,
            "Soft_Id"        => $plugin_id,
            "Soft_Name"      => $plugin_name,
            "Price"          => "",
            "Domain"         => $domain,
            "Expiry_Date"    => $expire ? date( 'd-m-Y', strtotime( $expire ) ) : '',
            "Customer_Name"  => "Tran Danh Trong",
            "Customer_Phone" => "0813908901",
            "Customer_Email" => "codevnes@gmail.com",
            "Status"         => $status,
            "Expiry_C_Use"   => "",
            "Timestamp"      => $isExpire ? '' : $date->getTimestamp(),
            "Note"           => "",
        ];
        $data['plugin']  = $plugin;
        $data['license'] = $license;
        } else {
        $data = [
            "error"   => 103,
            "success" => false,
            "msg"     => "License:" . $License . " -  không hợp lệ",
        ];
        }
    return rest_ensure_response( $data );
    }
