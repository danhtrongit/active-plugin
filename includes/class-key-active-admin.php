<?php

class Key_Active_Admin {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_management_page' ) );
        add_action( 'admin_init', array( $this, 'handle_form_actions' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
    }

    public function enqueue_admin_styles($hook) {
        if (strpos($hook, 'key_active_management') !== false || strpos($hook, 'key_active_add_new') !== false) {
            // Register and enqueue the CSS file
            wp_register_style(
                'key-active-admin-css',
                plugin_dir_url( dirname( __FILE__ ) ) . 'assets/css/key-active-admin.css',
                array(),
                filemtime( plugin_dir_path( dirname( __FILE__ ) ) . 'assets/css/key-active-admin.css' )
            );
            wp_enqueue_style('key-active-admin-css');
            
            // Register and enqueue the JS file
            wp_register_script(
                'key-active-admin-js',
                plugin_dir_url( dirname( __FILE__ ) ) . 'assets/js/key-active-admin.js',
                array('jquery'),
                filemtime( plugin_dir_path( dirname( __FILE__ ) ) . 'assets/js/key-active-admin.js' ),
                true
            );
            wp_enqueue_script('key-active-admin-js');
        }
    }

    public function add_management_page() {
        // Add a top-level menu item
        add_menu_page(
            'Quản lý Key',         // Page title
            'Quản lý Key',         // Menu title
            'manage_options',      // Capability
            'key_active_management', // Menu slug
            array( $this, 'render_management_page' ), // Callback function
            'dashicons-lock',      // Icon
            30                     // Position
        );
        
        // Add submenu items
        add_submenu_page(
            'key_active_management',    // Parent slug
            'Tất cả Key',               // Page title
            'Tất cả Key',               // Menu title
            'manage_options',           // Capability
            'key_active_management',    // Menu slug (same as parent to overwrite)
            array( $this, 'render_management_page' ) // Callback function
        );
        
        add_submenu_page(
            'key_active_management',    // Parent slug
            'Thêm Key mới',             // Page title
            'Thêm Key mới',             // Menu title
            'manage_options',           // Capability
            'key_active_add_new',       // Menu slug
            array( $this, 'render_add_new_page' ) // Callback function
        );
    }

    public function handle_form_actions() {
        // Handle creating a new key
        if ( isset( $_POST['submit_new_key'] ) && check_admin_referer( 'add_new_key_nonce' ) ) {
            $this->create_key();
        }

        // Handle deleting a key
        if ( isset( $_GET['action'], $_GET['key_id'] ) && $_GET['action'] === 'delete' && check_admin_referer( 'delete_key_' . $_GET['key_id'] ) ) {
            $this->delete_key( intval( $_GET['key_id'] ) );
        }
    }

    private function create_key() {
        $post_data = array(
            'post_title'  => sanitize_text_field( $_POST['license_key'] ),
            'post_type'   => 'key_active',
            'post_status' => 'publish',
        );

        $post_id = wp_insert_post( $post_data );

        if ( ! is_wp_error( $post_id ) ) {
            $fields = [ 'email', 'person', 'plugin_id', 'plugin_name', 'domain', 'version', 'expire' ];
            foreach ( $fields as $field ) {
                if ( isset( $_POST[$field] ) ) {
                    update_post_meta( $post_id, $field, sanitize_text_field( $_POST[$field] ) );
                }
            }
            $status = isset( $_POST['status'] ) && $_POST['status'] === 'on' ? 'true' : 'false';
            update_post_meta( $post_id, 'status', $status );

            // Redirect to avoid form resubmission
            wp_redirect( admin_url( 'admin.php?page=key_active_management&message=1' ) );
            exit;
        }
    }

    private function delete_key( $post_id ) {
        wp_delete_post( $post_id, true ); // true to bypass trash and force delete
        wp_redirect( admin_url( 'admin.php?page=key_active_management&message=2' ) );
        exit;
    }

    public function render_add_new_page() {
        ?>
        <div class="wrap key-active-wrap">
            <div class="key-active-header">
                <h1><span class="dashicons dashicons-plus-alt" style="font-size: 30px; height: 30px; width: 30px; padding-right: 10px;"></span> Thêm Key mới</h1>
            </div>
            <div class="key-active-content">
                <form method="post" action="" class="key-active-form">
                    <?php wp_nonce_field( 'add_new_key_nonce' ); ?>
                    <div class="form-field">
                        <label for="license_key">License Key</label>
                        <div class="key-generate-wrap">
                            <input type="text" name="license_key" id="license_key" required>
                            <button type="button" class="key-generate-btn">Tạo Key mới</button>
                        </div>
                    </div>
                    <div class="form-field">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email">
                    </div>
                    <div class="form-field">
                        <label for="person">Người dùng</label>
                        <input type="text" name="person" id="person">
                    </div>
                    <div class="form-field">
                        <label for="plugin_id">ID Plugin</label>
                        <input type="text" name="plugin_id" id="plugin_id">
                    </div>
                    <div class="form-field">
                        <label for="plugin_name">Tên Plugin</label>
                        <input type="text" name="plugin_name" id="plugin_name">
                    </div>
                    <div class="form-field">
                        <label for="domain">Tên miền</label>
                        <input type="text" name="domain" id="domain">
                    </div>
                    <div class="form-field">
                        <label for="version">Phiên bản</label>
                        <input type="text" name="version" id="version">
                    </div>
                    <div class="form-field">
                        <label for="expire">Ngày hết hạn</label>
                        <input type="date" name="expire" id="expire">
                    </div>
                    <div class="form-field">
                        <label for="status">Trạng thái</label>
                        <input type="checkbox" name="status" id="status" checked> Kích hoạt
                    </div>
                    <input type="submit" name="submit_new_key" value="Thêm Key mới" class="submit-btn">
                </form>
            </div>
        </div>
        <?php
    }

    public function render_management_page() {
        // Count active and inactive keys
        $active_count = 0;
        $inactive_count = 0;
        $total_count = 0;
        
        $args = array('post_type' => 'key_active', 'posts_per_page' => -1);
        $keys = get_posts($args);
        
        foreach ($keys as $key) {
            $total_count++;
            $status = get_post_meta($key->ID, 'status', true);
            if ($status === 'true') {
                $active_count++;
            } else {
                $inactive_count++;
            }
        }
        ?>
        <div class="wrap key-active-wrap">
            <div class="key-active-header">
                <h1><span class="dashicons dashicons-lock" style="font-size: 30px; height: 30px; width: 30px; padding-right: 10px;"></span> Quản lý Key</h1>
            </div>

            <div class="key-active-content">
                <?php if ( isset( $_GET['message'] ) && $_GET['message'] == '1' ) : ?>
                    <div class="notice-custom">
                        <p>Key đã được tạo thành công.</p>
                    </div>
                <?php elseif ( isset( $_GET['message'] ) && $_GET['message'] == '2' ) : ?>
                    <div class="notice-custom">
                        <p>Key đã được xóa thành công.</p>
                    </div>
                <?php endif; ?>

                <div class="key-active-dashboard">
                    <div class="key-active-card">
                        <h3>Tổng số Key</h3>
                        <div class="number"><?php echo $total_count; ?></div>
                    </div>
                    <div class="key-active-card">
                        <h3>Key đang kích hoạt</h3>
                        <div class="number"><?php echo $active_count; ?></div>
                    </div>
                    <div class="key-active-card">
                        <h3>Key vô hiệu hóa</h3>
                        <div class="number"><?php echo $inactive_count; ?></div>
                    </div>
                </div>

                <div>
                    <div style="margin-bottom: 20px">
                        <a href="<?php echo admin_url('admin.php?page=key_active_add_new'); ?>" class="button button-primary">
                            <span class="dashicons dashicons-plus" style="vertical-align: middle;"></span> Thêm Key mới
                        </a>
                    </div>
                    
                    <table class="key-active-table">
                        <thead>
                            <tr>
                                <th>License Key</th>
                                <th>Domain</th>
                                <th>Tên Plugin</th>
                                <th>Ngày hết hạn</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $args = array( 'post_type' => 'key_active', 'posts_per_page' => -1 );
                            $key_posts = get_posts( $args );
                            if ( $key_posts ) :
                                foreach ( $key_posts as $post ) : setup_postdata( $post );
                                    $delete_url = wp_nonce_url( admin_url('admin.php?page=key_active_management&action=delete&key_id=' . $post->ID), 'delete_key_' . $post->ID );
                                    $edit_url = admin_url('post.php?post=' . $post->ID . '&action=edit');
                                    $status = get_post_meta($post->ID, 'status', true);
                                ?>
                                <tr>
                                    <td>
                                        <strong><?php echo esc_html( $post->post_title ); ?></strong>
                                    </td>
                                    <td><?php echo esc_html( get_post_meta( $post->ID, 'domain', true ) ); ?></td>
                                    <td><?php echo esc_html( get_post_meta( $post->ID, 'plugin_name', true ) ); ?></td>
                                    <td><?php echo esc_html( get_post_meta( $post->ID, 'expire', true ) ); ?></td>
                                    <td>
                                        <span class="key-badge <?php echo $status === 'true' ? 'key-badge-active' : 'key-badge-inactive'; ?>">
                                            <?php echo $status === 'true' ? 'Kích hoạt' : 'Vô hiệu hóa'; ?>
                                        </span>
                                    </td>
                                    <td class="key-actions">
                                        <a href="<?php echo esc_url( $edit_url ); ?>" class="edit">
                                            <span class="dashicons dashicons-edit"></span> Sửa
                                        </a>
                                        <a href="<?php echo esc_url( $delete_url ); ?>" class="delete" onclick="return confirm('Bạn có chắc muốn xóa key này?')">
                                            <span class="dashicons dashicons-trash"></span> Xóa
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; wp_reset_postdata(); else : ?>
                                <tr><td colspan="6">Chưa có key nào.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
    }
} 