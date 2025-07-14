<?php

class Active_Plugin {

    protected static $instance = null;

    protected $version = '1.0.0';

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
            self::$instance->includes();
            self::$instance->init();
        }
        return self::$instance;
    }

    private function includes() {
        require_once plugin_dir_path( __FILE__ ) . 'class-key-active-cpt.php';
        require_once plugin_dir_path( __FILE__ ) . 'class-key-active-metabox.php';
        require_once plugin_dir_path( __FILE__ ) . 'class-key-active-rest-api.php';
    }

    private function init() {
        new Key_Active_CPT();
        new Key_Active_Metabox();
        new Key_Active_REST_API();
    }

    public function get_version() {
        return $this->version;
    }
} 