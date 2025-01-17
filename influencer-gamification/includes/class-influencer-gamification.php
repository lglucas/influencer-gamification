<?php

class Influencer_Gamification {

    protected $loader;
    protected $plugin_name;
    protected $version;

    public function __construct() {
        if ( defined( 'INFLUENCER_GAMIFICATION_VERSION' ) ) {
            $this->version = INFLUENCER_GAMIFICATION_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'influencer-gamification';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    private function load_dependencies() {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-influencer-gamification-loader.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-influencer-gamification-i18n.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-influencer-gamification-admin.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-influencer-gamification-public.php';

        $this->loader = new Influencer_Gamification_Loader();
    }

    private function set_locale() {
        $plugin_i18n = new Influencer_Gamification_i18n();
        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }

    private function define_admin_hooks() {
        // Cria uma nova instância do admin
        $plugin_admin = new Influencer_Gamification_Admin(
            $this->get_plugin_name(),
            $this->get_version(),
            $this->loader
        );
        
        // Inicializa os hooks do admin
        $plugin_admin->init();
    }

//    private function define_public_hooks() {
//        $plugin_public = new Influencer_Gamification_Public( $this->get_plugin_name(), $this->get_version() );
//        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
//        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
//    }

private function define_public_hooks() {
    $plugin_public = new Influencer_Gamification_Public( $this->get_plugin_name(), $this->get_version() );

    $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
    $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
    $this->loader->add_action( 'widgets_init', $plugin_public, 'register_widgets' );

    // Novo: registrar o tipo de post personalizado
    $this->loader->add_action( 'init', $plugin_public, 'register_task_post_type' );
}
    
    public function run() {
        $this->loader->run();
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_loader() {
        return $this->loader;
    }

    public function get_version() {
        return $this->version;
    }

}