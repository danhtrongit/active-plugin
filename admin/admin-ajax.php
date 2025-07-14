<?php

// Get current URL
if ( !function_exists( 'CDVN_Response' ) ) {

    function CDVN_Response($flag = true, $msg = '', $data = '', $die = true, $type = 0)
        {
        if ( $type )
            echo json_encode( array_merge( [ 'success' => $flag, 'msg' => $msg ], $data ) );
        else
            echo json_encode( [ 'success' => $flag, 'msg' => $msg, 'data' => $data ] );
        if ( $die )
            die();
        }
    }

function checkPermissionAdmin()
    {
    if ( !current_user_can( 'administrator' ) )
        CDVN_Response( false, 'Không có quyền thực hiện thao tác này.', [] );
    }


function create_key_active_post()
    {
    checkPermissionAdmin();

    $title = sanitize_text_field( $_POST['title'] );

    // Meta box data
    $email       = sanitize_text_field( $_POST['email'] );
    $person      = sanitize_text_field( $_POST['person'] );
    $plugin_id   = sanitize_text_field( $_POST['plugin_id'] );
    $plugin_name = sanitize_text_field( $_POST['plugin_name'] );
    $domain      = sanitize_text_field( $_POST['domain'] );
    $status      = isset($_POST['status']) ? 'on' : 'off'; // Change 'true' to 'on', 'false' to 'off'
    $expire      = sanitize_text_field( $_POST['expire'] );

    // Parse the date using DateTime::createFromFormat
    $expire_date = DateTime::createFromFormat( 'D, d M Y H:i:s e', $expire );

    // Check if parsing was successful and then format the date as 'Y-m-d'
    if ( $expire_date ) {
        $expire_formatted = $expire_date->format( 'Y-m-d' );
        } else {
        $expire_formatted = '';
        }
    $post_data = array(
        'post_title'  => $title,
        'post_status' => 'publish',
        'post_type'   => 'key_active',
    );

    $post_id = wp_insert_post( $post_data );

    if ( $post_id ) {
        // Save meta box data
        update_post_meta( $post_id, 'email', $email );
        update_post_meta( $post_id, 'person', $person );
        update_post_meta( $post_id, 'plugin_id', $plugin_id );
        update_post_meta( $post_id, 'plugin_name', $plugin_name );
        update_post_meta( $post_id, 'domain', $domain );
        update_post_meta( $post_id, 'status', $status );
        update_post_meta( $post_id, 'expire', $expire_formatted );

        wp_send_json_success();
        } else {
        wp_send_json_error();
        }
    }

add_action( 'wp_ajax_create_key_active_post', 'create_key_active_post' );
add_action( 'wp_ajax_nopriv_create_key_active_post', 'create_key_active_post' );



function delete_post_callback()
    {
    $response = array( 'success' => false );

    if ( isset($_POST['post_id']) && current_user_can( 'delete_post', $_POST['post_id'] ) ) {
        $post_id = intval( $_POST['post_id'] );
        wp_delete_post( $post_id, true );
        $response['success'] = true;
        }

    wp_send_json( $response );
    }

add_action( 'wp_ajax_delete_post', 'delete_post_callback' );
add_action( 'wp_ajax_nopriv_delete_post', 'delete_post_callback' );