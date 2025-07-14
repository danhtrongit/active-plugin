<?php

class Key_Active_Metabox {

    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
        add_action( 'save_post_key_active', array( $this, 'save_metabox' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_metabox_scripts' ) );
    }

    public function enqueue_metabox_scripts($hook) {
        global $post;
        
        if ($hook == 'post.php' || $hook == 'post-new.php') {
            if (isset($post) && $post->post_type === 'key_active') {
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
    }

    public function add_metabox() {
        add_meta_box(
            'key_active_metabox',
            'Chi tiết Key',
            array( $this, 'render_metabox' ),
            'key_active',
            'normal',
            'high'
        );
        
        // Add a note/instructions in the title area
        add_action('edit_form_after_title', array($this, 'add_title_instructions'));
    }
    
    public function add_title_instructions($post) {
        if ($post->post_type !== 'key_active') {
            return;
        }
        ?>
        <div class="key-active-edit-title">
            <p><em>License Key hiển thị phía trên. Bấm nút bên dưới để tạo key ngẫu nhiên mới.</em></p>
            <button type="button" class="generate-key-button">Tạo Key mới</button>
        </div>
        <?php
    }

    public function render_metabox( $post ) {
        wp_nonce_field( 'save_key_active_metabox', 'key_active_nonce' );

        $email       = get_post_meta( $post->ID, 'email', true );
        $person      = get_post_meta( $post->ID, 'person', true );
        $plugin_id   = get_post_meta( $post->ID, 'plugin_id', true );
        $plugin_name = get_post_meta( $post->ID, 'plugin_name', true );
        $domain      = get_post_meta( $post->ID, 'domain', true );
        $version     = get_post_meta( $post->ID, 'version', true );
        $status      = get_post_meta( $post->ID, 'status', true );
        $expire      = get_post_meta( $post->ID, 'expire', true );
        ?>
        <p>
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" value="<?php echo esc_attr( $email ); ?>" class="widefat">
        </p>
        <p>
            <label for="person">Người dùng:</label>
            <input type="text" id="person" name="person" value="<?php echo esc_attr( $person ); ?>" class="widefat">
        </p>
        <p>
            <label for="plugin_id">ID Plugin:</label>
            <input type="text" id="plugin_id" name="plugin_id" value="<?php echo esc_attr( $plugin_id ); ?>" class="widefat">
        </p>
        <p>
            <label for="plugin_name">Tên Plugin:</label>
            <input type="text" id="plugin_name" name="plugin_name" value="<?php echo esc_attr( $plugin_name ); ?>" class="widefat">
        </p>
        <p>
            <label for="domain">Tên miền:</label>
            <input type="text" id="domain" name="domain" value="<?php echo esc_attr( $domain ); ?>" class="widefat">
        </p>
        <p>
            <label for="version">Phiên bản:</label>
            <input type="text" id="version" name="version" value="<?php echo esc_attr( $version ); ?>" class="widefat">
        </p>
        <p>
            <label for="status">Trạng thái:</label>
            <input type="checkbox" id="status" name="status" <?php checked( $status, 'true', true ); ?>>
        </p>
        <p>
            <label for="expire">Ngày hết hạn:</label>
            <input type="date" id="expire" name="expire" value="<?php echo esc_attr( $expire ); ?>">
        </p>
        <?php
    }

    public function save_metabox( $post_id ) {
        if ( ! isset( $_POST['key_active_nonce'] ) || ! wp_verify_nonce( $_POST['key_active_nonce'], 'save_key_active_metabox' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $fields = [ 'email', 'person', 'plugin_id', 'plugin_name', 'domain', 'version', 'expire' ];

        foreach ( $fields as $field ) {
            if ( isset( $_POST[$field] ) ) {
                update_post_meta( $post_id, $field, sanitize_text_field( $_POST[$field] ) );
            }
        }

        $status = isset( $_POST['status'] ) && $_POST['status'] === 'on' ? 'true' : 'false';
        update_post_meta( $post_id, 'status', $status );
    }
} 