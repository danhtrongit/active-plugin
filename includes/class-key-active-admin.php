<?php

class Key_Active_Admin {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_management_page' ) );
        add_action( 'admin_init', array( $this, 'handle_form_actions' ) );
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
            wp_redirect( admin_url( 'edit.php?post_type=key_active&page=key_active_management&message=1' ) );
            exit;
        }
    }

    private function delete_key( $post_id ) {
        wp_delete_post( $post_id, true ); // true to bypass trash and force delete
        wp_redirect( admin_url( 'edit.php?post_type=key_active&page=key_active_management&message=2' ) );
        exit;
    }

    public function render_management_page() {
        ?>
        <div class="wrap">
            <h1>Quản lý Key</h1>

            <?php if ( isset( $_GET['message'] ) && $_GET['message'] == '1' ) : ?>
                <div id="message" class="updated notice is-dismissible"><p>Key đã được tạo thành công.</p></div>
            <?php elseif ( isset( $_GET['message'] ) && $_GET['message'] == '2' ) : ?>
                <div id="message" class="updated notice is-dismissible"><p>Key đã được xóa thành công.</p></div>
            <?php endif; ?>

            <div id="col-container">
                <div id="col-right">
                    <div class="col-wrap">
                        <h2>Danh sách Key</h2>
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th>License Key</th>
                                    <th>Domain</th>
                                    <th>Tên Plugin</th>
                                    <th>Ngày hết hạn</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $args = array( 'post_type' => 'key_active', 'posts_per_page' => -1 );
                                $key_posts = get_posts( $args );
                                if ( $key_posts ) :
                                    foreach ( $key_posts as $post ) : setup_postdata( $post );
                                        $delete_url = wp_nonce_url( admin_url('edit.php?post_type=key_active&page=key_active_management&action=delete&key_id=' . $post->ID), 'delete_key_' . $post->ID );
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><a href="<?php echo get_edit_post_link( $post->ID ); ?>"><?php echo esc_html( $post->post_title ); ?></a></strong>
                                            <div class="row-actions">
                                                <span class="edit"><a href="<?php echo get_edit_post_link( $post->ID ); ?>">Sửa</a> | </span>
                                                <span class="delete"><a href="<?php echo esc_url( $delete_url ); ?>" class="text-danger" onclick="return confirm('Bạn có chắc muốn xóa key này?')">Xóa</a></span>
                                            </div>
                                        </td>
                                        <td><?php echo esc_html( get_post_meta( $post->ID, 'domain', true ) ); ?></td>
                                        <td><?php echo esc_html( get_post_meta( $post->ID, 'plugin_name', true ) ); ?></td>
                                        <td><?php echo esc_html( get_post_meta( $post->ID, 'expire', true ) ); ?></td>
                                        <td><?php echo get_post_meta( $post->ID, 'status', true ) === 'true' ? 'Kích hoạt' : 'Vô hiệu hóa'; ?></td>
                                    </tr>
                                <?php endforeach; wp_reset_postdata(); else : ?>
                                    <tr><td colspan="5">Chưa có key nào.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="col-left">
                    <div class="col-wrap">
                        <h2>Thêm Key mới</h2>
                        <form method="post" action="">
                            <?php wp_nonce_field( 'add_new_key_nonce' ); ?>
                            <div class="form-field">
                                <label for="license_key">License Key</label>
                                <input type="text" name="license_key" id="license_key" required class="widefat">
                            </div>
                            <div class="form-field">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" class="widefat">
                            </div>
                            <div class="form-field">
                                <label for="person">Người dùng</label>
                                <input type="text" name="person" id="person" class="widefat">
                            </div>
                            <div class="form-field">
                                <label for="plugin_id">ID Plugin</label>
                                <input type="text" name="plugin_id" id="plugin_id" class="widefat">
                            </div>
                            <div class="form-field">
                                <label for="plugin_name">Tên Plugin</label>
                                <input type="text" name="plugin_name" id="plugin_name" class="widefat">
                            </div>
                            <div class="form-field">
                                <label for="domain">Tên miền</label>
                                <input type="text" name="domain" id="domain" class="widefat">
                            </div>
                            <div class="form-field">
                                <label for="version">Phiên bản</label>
                                <input type="text" name="version" id="version" class="widefat">
                            </div>
                            <div class="form-field">
                                <label for="expire">Ngày hết hạn</label>
                                <input type="date" name="expire" id="expire">
                            </div>
                            <div class="form-field">
                                <label for="status">Trạng thái</label>
                                <input type="checkbox" name="status" id="status" checked> Kích hoạt
                            </div>
                            <?php submit_button( 'Thêm Key mới', 'primary', 'submit_new_key' ); ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <style>#col-left { width: 30%; } #col-right { width: 68%; float: right; }</style>
        <?php
    }
} 