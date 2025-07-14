<?php

class Key_Active_CPT {

    public function __construct() {
        add_action( 'init', array( $this, 'register' ) );
    }

    public function register() {
        $labels = array(
            'name'               => 'Quản lý Key',
            'singular_name'      => 'Key',
            'add_new'            => 'Thêm mới',
            'add_new_item'       => 'Thêm Key mới',
            'edit_item'          => 'Sửa Key',
            'new_item'           => 'Key mới',
            'all_items'          => 'Tất cả Key',
            'view_item'          => 'Xem Key',
            'search_items'       => 'Tìm kiếm Key',
            'not_found'          => 'Không tìm thấy Key',
            'not_found_in_trash' => 'Không tìm thấy Key trong thùng rác',
            'menu_name'          => 'Quản lý Key',
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
} 