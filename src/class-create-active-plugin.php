<?php


function custom_key_active_post_type()
    {
    $labels = array(
        'name'               => 'Key Active',
        'singular_name'      => 'Key Active',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Key Active',
        'edit_item'          => 'Edit Key Active',
        'new_item'           => 'New Key Active',
        'all_items'          => 'All Key Active',
        'view_item'          => 'View Key Active',
        'search_items'       => 'Search Key Active',
        'not_found'          => 'No Key Active found',
        'not_found_in_trash' => 'No Key Active found in Trash',
        'menu_name'          => 'Key Active',
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
    add_action( 'add_meta_boxes', 'add_key_active_metabox' );

    }

add_action( 'init', 'custom_key_active_post_type' );

function add_key_active_metabox()
    {
    add_meta_box( 'key_active_metabox', 'Key Active Details', 'key_active_metabox_callback', 'key_active', 'normal', 'high' );
    }

function key_active_metabox_callback($post)
    {
    $email       	= get_post_meta( $post->ID, 'email', true );
    $person      	= get_post_meta( $post->ID, 'person', true );
    $plugin_id   	= get_post_meta( $post->ID, 'plugin_id', true );
    $plugin_name 	= get_post_meta( $post->ID, 'plugin_name', true );
    $domain      	= get_post_meta( $post->ID, 'domain', true );
    $version      	= get_post_meta( $post->ID, 'version', true );
    $status      	= get_post_meta( $post->ID, 'status', true );
    $expire      	= get_post_meta( $post->ID, 'expire', true );
    ?>

    <label>Email:</label>
    <input type="text" name="email" value="<?php echo esc_attr( $email ); ?>"><br>

    <label>Person:</label>
    <input type="text" name="person" value="<?php echo esc_attr( $person ); ?>"><br>

    <label>Plugin ID:</label>
    <input type="text" name="plugin_id" value="<?php echo esc_attr( $plugin_id ); ?>"><br>

    <label>Plugin Name:</label>
    <input type="text" name="plugin_name" value="<?php echo esc_attr( $plugin_name ); ?>"><br>

    <label>Domain:</label>
    <input type="text" name="domain" value="<?php echo esc_attr( $domain ); ?>"><br>


	<label>Version:</label>
	<input type="text" name="version" value="<?php echo esc_attr( $version ); ?>"><br>

    <label>Status:</label>
	<input type="checkbox" name="status" <?php checked( $status, 'true', true ); ?>>

    <label>Expire:</label>
    <input type="date" name="expire" value="<?php echo esc_attr( $expire ); ?>"><br>
    <?php
    }

function save_key_active_metabox($post_id)
    {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( !current_user_can( 'edit_post', $post_id ) ) return;

    $fields = [ 'email', 'person', 'plugin_id', 'plugin_name', 'domain','version', 'status', 'expire' ];

    foreach ( $fields as $field ) {
        if ( isset($_POST[$field]) ) {
            update_post_meta( $post_id, $field, sanitize_text_field( $_POST[$field] ) );
            }
        if ( isset($_POST['status']) && $_POST['status'] === 'on' ) {
            update_post_meta( $post_id, 'status', 'true' );
            } else {
            update_post_meta( $post_id, 'status', 'false' );
            }

        }
    }
add_action( 'save_post_key_active', 'save_key_active_metabox' );