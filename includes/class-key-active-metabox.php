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
                        
                        $(".generate-key-button").on("click", function(e) {
                            e.preventDefault();
                            $("#title").val(generateRandomKey());
                        });
                        
                        // Style improvements for the metabox
                        $("#key_active_metabox .inside").css({
                            "padding": "15px",
                            "margin": "0"
                        });
                        
                        $("#key_active_metabox label").css({
                            "font-weight": "600",
                            "margin-bottom": "5px",
                            "display": "block"
                        });
                        
                        $("#key_active_metabox input[type=\'text\'], #key_active_metabox input[type=\'email\']").css({
                            "width": "100%",
                            "padding": "8px 10px",
                            "margin-bottom": "15px",
                            "border-radius": "4px",
                            "border": "1px solid #ddd"
                        });
                    });
                ');
                
                // Add custom styles for post edit screen
                wp_add_inline_style('wp-admin', '
                    .generate-key-button {
                        background: #f0f0f1;
                        border: 1px solid #ddd;
                        border-radius: 4px;
                        padding: 4px 10px;
                        font-size: 13px;
                        margin-left: 10px;
                        cursor: pointer;
                    }
                    .generate-key-button:hover {
                        background: #ddd;
                    }
                    #titlediv #title-prompt-text {
                        padding: 3px 10px;
                    }
                    .key-active-edit-title {
                        display: flex;
                        align-items: center;
                    }
                ');
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