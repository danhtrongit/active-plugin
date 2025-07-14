<?php

class Key_Active_Admin {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_management_page' ) );
        add_action( 'admin_init', array( $this, 'handle_form_actions' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
    }

    public function enqueue_admin_styles($hook) {
        if (strpos($hook, 'key_active_management') !== false || strpos($hook, 'key_active_add_new') !== false) {
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('wp-color-picker');
            
            // Add inline styles for the admin page
            wp_add_inline_style('admin-styles', '
                .key-active-wrap {
                    margin: 20px 20px 0 0;
                    background: #fff;
                    border-radius: 8px;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
                    overflow: hidden;
                }
                .key-active-header {
                    padding: 20px 25px;
                    background: linear-gradient(135deg, #f8f9fa 0%, #f1f3f4 100%);
                    border-bottom: 1px solid #e8e8e8;
                    position: relative;
                }
                .key-active-header h1 {
                    margin: 0;
                    color: #23282d;
                    font-size: 24px;
                    display: flex;
                    align-items: center;
                }
                .key-active-content {
                    padding: 25px;
                }
                .key-active-dashboard {
                    display: flex;
                    flex-wrap: wrap;
                    margin-bottom: 30px;
                    gap: 20px;
                }
                .key-active-card {
                    background: #f8f9fa;
                    border-radius: 8px;
                    padding: 20px;
                    min-width: 220px;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
                    border-left: 4px solid #2271b1;
                    transition: all 0.2s ease;
                    flex: 1;
                }
                .key-active-card:hover {
                    transform: translateY(-3px);
                    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                }
                .key-active-card h3 {
                    margin-top: 0;
                    color: #50575e;
                    font-size: 16px;
                    margin-bottom: 10px;
                }
                .key-active-card .number {
                    font-size: 36px;
                    font-weight: 600;
                    color: #2271b1;
                }
                #col-container {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 25px;
                }
                #col-left {
                    width: 35%;
                    padding-right: 25px;
                }
                #col-right {
                    width: 65%;
                }
                .key-active-table {
                    width: 100%;
                    border-collapse: separate;
                    border-spacing: 0;
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
                }
                .key-active-table th {
                    text-align: left;
                    padding: 14px 18px;
                    background: #f8f9fa;
                    border-bottom: 2px solid #e8e8e8;
                    font-weight: 600;
                    color: #23282d;
                }
                .key-active-table td {
                    padding: 14px 18px;
                    border-bottom: 1px solid #e8e8e8;
                    vertical-align: middle;
                }
                .key-active-table tr:last-child td {
                    border-bottom: none;
                }
                .key-active-table tr:hover {
                    background-color: #f9f9f9;
                }
                .key-active-form {
                    background: #fff;
                    border-radius: 8px;
                    padding: 25px;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
                }
                .key-active-form .form-field {
                    margin-bottom: 20px;
                }
                .key-active-form label {
                    display: block;
                    margin-bottom: 8px;
                    font-weight: 600;
                    color: #23282d;
                    font-size: 14px;
                }
                .key-active-form input[type="text"],
                .key-active-form input[type="email"],
                .key-active-form input[type="date"] {
                    width: 100%;
                    padding: 10px 14px;
                    border-radius: 4px;
                    border: 1px solid #ddd;
                    box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
                    transition: all 0.2s ease;
                }
                .key-active-form input[type="text"]:focus,
                .key-active-form input[type="email"]:focus,
                .key-active-form input[type="date"]:focus {
                    border-color: #2271b1;
                    box-shadow: 0 0 0 1px #2271b1;
                    outline: none;
                }
                .key-active-form .submit-btn {
                    background: linear-gradient(135deg, #2271b1 0%, #135e96 100%);
                    color: #fff;
                    border: none;
                    padding: 12px 20px;
                    border-radius: 4px;
                    cursor: pointer;
                    font-weight: 600;
                    font-size: 14px;
                    transition: all 0.2s ease;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                }
                .key-active-form .submit-btn:hover {
                    background: linear-gradient(135deg, #135e96 0%, #0d3c61 100%);
                    transform: translateY(-2px);
                    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
                }
                .key-badge {
                    display: inline-block;
                    padding: 5px 10px;
                    border-radius: 4px;
                    font-size: 12px;
                    font-weight: 600;
                    letter-spacing: 0.5px;
                    text-transform: uppercase;
                }
                .key-badge-active {
                    background: #d1e7dd;
                    color: #0f5132;
                }
                .key-badge-inactive {
                    background: #f8d7da;
                    color: #842029;
                }
                .key-actions {
                    display: flex;
                    gap: 10px;
                }
                .key-actions a {
                    text-decoration: none;
                    display: inline-flex;
                    align-items: center;
                    gap: 5px;
                    padding: 5px 10px;
                    border-radius: 4px;
                    font-size: 13px;
                    transition: all 0.2s ease;
                }
                .key-actions .edit {
                    color: #2271b1;
                    border: 1px solid #2271b1;
                }
                .key-actions .edit:hover {
                    background: #2271b1;
                    color: #fff;
                }
                .key-actions .delete {
                    color: #b32d2e;
                    border: 1px solid #b32d2e;
                }
                .key-actions .delete:hover {
                    background: #b32d2e;
                    color: #fff;
                }
                .notice-custom {
                    background: #f0f6fc;
                    border-left: 4px solid #2271b1;
                    box-shadow: 0 1px 1px rgba(0,0,0,.04);
                    margin: 0 0 20px;
                    padding: 12px 15px;
                    border-radius: 4px;
                }
                .key-generate-wrap {
                    display: flex;
                    gap: 10px;
                }
                .key-generate-wrap input {
                    flex: 1;
                }
                .key-generate-btn {
                    background: #f0f0f1;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    color: #50575e;
                    cursor: pointer;
                    padding: 0 12px;
                    font-size: 13px;
                    transition: all 0.2s ease;
                }
                .key-generate-btn:hover {
                    background: #ddd;
                    color: #23282d;
                }
                @media screen and (max-width: 782px) {
                    #col-left, #col-right {
                        width: 100%;
                        padding-right: 0;
                    }
                    .key-active-dashboard {
                        flex-direction: column;
                    }
                    .key-active-card {
                        width: 100%;
                    }
                    .key-actions {
                        flex-direction: column;
                    }
                }
            ');
            
            // Add JavaScript for random key generation
            wp_add_inline_script('jquery', '
                jQuery(document).ready(function($) {
                    function generateRandomKey() {
                        var chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                        var segments = 4;
                        var segmentLength = 5;
                        var key = "";
                        
                        for(var i = 0; i < segments; i++) {
                            for(var j = 0; j < segmentLength; j++) {
                                var randomIndex = Math.floor(Math.random() * chars.length);
                                key += chars.charAt(randomIndex);
                            }
                            if(i < segments - 1) {
                                key += "-";
                            }
                        }
                        
                        return key;
                    }
                    
                    $(".key-generate-btn").on("click", function(e) {
                        e.preventDefault();
                        var randomKey = generateRandomKey();
                        $("#license_key").val(randomKey);
                    });
                    
                    // Auto-generate a key if the field is empty on page load
                    if($("#license_key").length && $("#license_key").val() === "") {
                        $("#license_key").val(generateRandomKey());
                    }
                });
            ');
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