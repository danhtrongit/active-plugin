<?php

class Key_Active_Admin {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_management_page' ) );
    }

    public function add_management_page() {
        add_submenu_page(
            'edit.php?post_type=key_active',
            'Quản lý Key',
            'Quản lý Key',
            'manage_options',
            'key_active_management',
            array( $this, 'render_management_page' )
        );
    }

    public function render_management_page() {
        ?>
        <div class="wrap">
            <h1>Quản lý Key</h1>
            <p>Trang này sẽ dùng để thêm và quản lý các key.</p>
        </div>
        <?php
    }
} 