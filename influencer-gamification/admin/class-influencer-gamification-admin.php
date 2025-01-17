<?php

class Influencer_Gamification_Admin {
    private $plugin_name;
    private $version;
    private $loader;

    /**
     * Construtor da classe.
     *
     * @param string $plugin_name O nome do plugin.
     * @param string $version A versão do plugin.
     * @param object $loader O objeto loader.
     */
    public function __construct($plugin_name, $version, $loader) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->loader = $loader;
    }

    /**
     * Inicializa as ações e filtros do plugin.
     */
    public function init() {
        // Registra as configurações e menus do admin
        $this->loader->add_action('admin_init', $this, 'register_settings');
        $this->loader->add_action('admin_menu', $this, 'add_plugin_admin_menu');
        $this->loader->add_action('admin_enqueue_scripts', $this, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $this, 'enqueue_scripts');
        
        // Registra handlers para ações do plugin
        $this->loader->add_action('admin_post_ig_add_influencer', $this, 'handle_add_influencer');
        $this->loader->add_action('admin_post_ig_delete_influencer', $this, 'handle_delete_influencer');
        $this->loader->add_action('admin_post_ig_add_task', $this, 'handle_add_task');
        $this->loader->add_action('admin_post_ig_delete_task', $this, 'handle_delete_task');
        $this->loader->add_action('save_post_ig_task_submission', $this, 'save_submission_meta_boxes');
        $this->loader->add_action('add_meta_boxes_ig_task_submission', $this, 'display_submission_meta_boxes');
        $this->loader->add_action('admin_notices', $this, 'display_admin_notices');
    }

/**
 * Registra todas as configurações do plugin.
 */
public function register_settings() {
    // Configurações principais
    register_setting( 
        'influencer_gamification_options', 
        'influencer_gamification_settings'
    );
    
    // Configurações de níveis
    register_setting( 
        'influencer_gamification', 
        'influencer_gamification_levels',
        array(
            'type' => 'array',
            'sanitize_callback' => array($this, 'sanitize_levels'),
            'default' => array(),
        )
    );

    // Configurações gerais
    register_setting(
        'influencer_gamification_settings', 
        'influencer_gamification_settings'
    );

    // Seção principal
    add_settings_section(
        'influencer_gamification_main',
        'Main Settings',
        array($this, 'settings_section_callback'),
        'influencer-gamification'
    );

    // Seção geral
    add_settings_section(
        'influencer_gamification_settings_general',
        __('General Settings', 'influencer-gamification'),
        array($this, 'settings_general_callback'),
        'influencer_gamification_settings'
    );
    
    // Campos de configuração
    add_settings_field(
        'xp_per_task',
        'XP Per Task',
        array($this, 'xp_per_task_callback'),
        'influencer-gamification',
        'influencer_gamification_main'
    );

    add_settings_field(
        'points_per_task',
        __('Points Per Task', 'influencer-gamification'),
        array($this, 'points_per_task_callback'),
        'influencer_gamification_settings',
        'influencer_gamification_settings_general'
    );
}
    
    /**
     * Callback de seção de configurações.
     */
    public function settings_section_callback() {
        echo 'Configure the main settings for the Influencer Gamification plugin.';
    }
    
    /**
     * Callback de campo de configuração de XP por tarefa.
     */
    public function xp_per_task_callback() {
        $settings = get_option( 'influencer_gamification_settings' );
        $value = isset( $settings['xp_per_task'] ) ? $settings['xp_per_task'] : 10;
        echo '<input type="number" name="influencer_gamification_settings[xp_per_task]" value="' . $value . '">';
    }

    /**
     * Adiciona os menus do plugin ao painel de administração.
     */
    public function add_plugin_admin_menu() {
        // Verifica permissões do usuário
        if (!current_user_can('manage_options')) {
            return;
        }
    
        $parent_slug = 'influencer-gamification';
        
        add_menu_page(
            'Influencer Gamification',
            'Influencer Gamification',
            'manage_options',
            $parent_slug,
            array($this, 'display_plugin_admin_page'),
            'dashicons-awards',
            65
        );
    
        add_submenu_page(
            $parent_slug,
            'Influencers',
            'Influencers',
            'manage_options',
            'influencer-gamification-influencers',
            array($this, 'display_influencers_page')
        );
    
        add_submenu_page(
            null,
            'Add Influencer',
            'Add Influencer',
            'manage_options',
            'influencer-gamification-add-influencer',
            array($this, 'display_add_influencer_page')
        );
    
        add_submenu_page(
            $parent_slug,
            'Tasks',
            'Tasks',
            'manage_options',
            'influencer-gamification-tasks',
            array($this, 'display_tasks_page')
        );
    
        add_submenu_page(
            null,
            'Add Task',
            'Add Task',
            'manage_options',
            'influencer-gamification-add-task',
            array($this, 'display_add_task_page')
        );
    
        add_submenu_page(
            $parent_slug,
            'Task Submissions',
            'Task Submissions',
            'manage_options',
            'influencer-gamification-task-submissions',
            array($this, 'display_task_submissions_page')
        );

        add_submenu_page(
            $parent_slug,
            __('Settings', 'influencer-gamification'),
            __('Settings', 'influencer-gamification'),
            'manage_options',
            'influencer-gamification-settings',
            array($this, 'display_plugin_settings_page')
        );
    }
    
    /**
     * Exibe a página de administração do plugin.
     */
    public function display_plugin_admin_page() {
        include_once 'partials/influencer-gamification-admin-display.php';
    }

    /**
     * Exibe a página de influenciadores.
     */
    public function display_influencers_page() {
        $influencers = get_users( array(
            'role'   => 'influencer',
            'orderby' => 'user_registered',
            'order' => 'DESC',
            'fields' => 'all_with_meta',
        ) );
        include_once 'partials/influencer-gamification-influencers-display.php';
    }

    /**
     * Exibe a página de adição de influenciador.
     */
    public function display_add_influencer_page() {
        include_once 'partials/influencer-gamification-add-influencer-display.php';
    }

      /**
     * Exibe a página de configurações do plugin.
     */
    public function display_plugin_settings_page() {
        include_once 'partials/influencer-gamification-admin-settings-display.php';
    }

          /**
     * Não sei oq isso faz.
     */
    public function settings_general_callback() {
        echo '<p>' . __('General settings for the Influencer Gamification plugin.', 'influencer-gamification') . '</p>';
    }
    
    public function points_per_task_callback() {
        $settings = get_option('influencer_gamification_settings');
        $points = isset($settings['points_per_task']) ? $settings['points_per_task'] : 10;
        echo '<input type="number" id="points_per_task" name="influencer_gamification_settings[points_per_task]" value="' . $points . '" min="1">';
    }

    
    /**
     * Exibe a página de adição de tarefa.
     */
    public function display_add_task_page() {
        include_once 'partials/influencer-gamification-add-task-display.php';
    }

    /**
     * Manipula a adição de um novo influenciador.
     */
    public function handle_add_influencer() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'influencer-gamification'));
        }
    
        if (!isset($_POST['ig_add_influencer_nonce']) || 
            !wp_verify_nonce($_POST['ig_add_influencer_nonce'], 'ig_add_influencer')) {
            wp_die(__('Security check failed', 'influencer-gamification'));
        }
    
        $redirect_url = admin_url('admin.php?page=influencer-gamification-influencers');
    
        if (isset($_POST['user_email'])) {
            $user_email = sanitize_email($_POST['user_email']);
            
            if (!is_email($user_email)) {
                wp_safe_redirect(add_query_arg('error', 'invalid_email', $redirect_url));
                exit;
            }
    
            if (email_exists($user_email)) {
                wp_safe_redirect(add_query_arg('error', 'email_exists', $redirect_url));
                exit;
            }
    
            add_filter('wp_mail_from', '__return_false', PHP_INT_MAX);
            add_filter('wp_mail_from_name', '__return_false', PHP_INT_MAX);

            $user_id = wp_create_user(
                $user_email,
                wp_generate_password(),
                $user_email
            );

            remove_filter('wp_mail_from', '__return_false', PHP_INT_MAX);
            remove_filter('wp_mail_from_name', '__return_false', PHP_INT_MAX);
    
            if (is_wp_error($user_id)) {
                wp_safe_redirect(add_query_arg('error', 'creation_failed', $redirect_url));
                exit;
            }
    
            $user = get_user_by('id', $user_id);
            $user->set_role('influencer');
            add_user_meta($user_id, 'ig_points', 0);
            add_user_meta($user_id, 'ig_level', 1);
            
            wp_safe_redirect(add_query_arg('message', 'influencer_added', $redirect_url));
            exit;
        }
    
        wp_safe_redirect(add_query_arg('error', 'unknown', $redirect_url));
        exit;
    }

    /**
     * Exibe as notificações do administrador.
     */
    public function display_admin_notices() {
        $screen = get_current_screen();
        if ($screen->id !== 'influencer-gamification_page_influencer-gamification-influencers') {
            return;
        }
    
        if (isset($_GET['message'])) {
            switch ($_GET['message']) {
                case 'influencer_added':
                    ?>
                    <div class="notice notice-success is-dismissible">
                        <p><?php _e('Influencer successfully added!', 'influencer-gamification'); ?></p>
                    </div>
                    <?php
                    break;
            }
        }
    
        if (isset($_GET['error'])) {
            $error_message = '';
            switch ($_GET['error']) {
                case 'invalid_email':
                    $error_message = __('Invalid email address provided.', 'influencer-gamification');
                    break;
                case 'email_exists':
                    $error_message = __('This email is already registered.', 'influencer-gamification');
                    break;
                case 'creation_failed':
                    $error_message = __('Failed to create new user.', 'influencer-gamification');
                    break;
                default:
                    $error_message = __('An unknown error occurred.', 'influencer-gamification');
                    break;
            }
            ?>
            <div class="notice notice-error is-dismissible">
                <p><?php echo esc_html($error_message); ?></p>
            </div>
            <?php
        }
    }

    /**
     * Sanitiza os níveis de gamificação.
     *
     * @param array $input Os níveis a serem sanitizados.
     * @return array Os níveis sanitizados.
     */
    public function sanitize_levels( $input ) {
        $new_input = array();
    
        foreach ( $input as $level => $points ) {
            if ( is_numeric( $points ) && $points >= 0 ) {
                $new_input[$level] = intval( $points );
            }
        }
    
        return $new_input;
    }
    
    /**
     * Exibe a página de configuração do plugin.
     */
    public function display_plugin_setup_page() {
        ?>
        <div class="wrap">
            <h2><?php _e( 'Influencer Gamification Settings', 'influencer-gamification' ); ?></h2>
            <form method="post" action="options.php">
                <?php
                    settings_fields( 'influencer_gamification' );
                    do_settings_sections( 'influencer-gamification' );
                ?>
                <h3><?php _e( 'Levels', 'influencer-gamification' ); ?></h3>
                <table class="form-table">
                    <?php
                    $levels = get_option( 'influencer_gamification_levels', array() );
                    for ( $i = 1; $i <= 10; $i++ ) :
                    ?>
                        <tr>
                            <th scope="row"><label for="level_<?php echo $i; ?>"><?php printf( __( 'Level %d', 'influencer-gamification' ), $i ); ?></label></th>
                            <td><input type="number" name="influencer_gamification_levels[<?php echo $i; ?>]" id="level_<?php echo $i; ?>" value="<?php echo isset( $levels[$i] ) ? $levels[$i] : ''; ?>" class="regular-text"></td>
                        </tr>
                    <?php endfor; ?>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Manipula a adição de uma nova tarefa.
     */
    public function handle_add_task() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'You do not have sufficient permissions to access this page.' );
        }
    
        if ( isset( $_POST['ig_add_task_nonce'] ) && wp_verify_nonce( $_POST['ig_add_task_nonce'], 'ig_add_task' ) ) {
            $assigned_influencer = isset( $_POST['assigned_influencer'] ) ? intval( $_POST['assigned_influencer'] ) : 0;
            $title = sanitize_text_field( $_POST['title'] );
            $description = sanitize_textarea_field( $_POST['description'] );
            $points = intval( $_POST['points'] );
            $start_date = isset( $_POST['start_date'] ) ? sanitize_text_field( $_POST['start_date'] ) : '';
            $end_date = isset( $_POST['end_date'] ) ? sanitize_text_field( $_POST['end_date'] ) : '';
                
            $task_id = wp_insert_post( array(
                'post_title'   => $title,
                'post_content' => $description,
                'post_status'  => 'publish',
                'post_type'    => 'ig_task',
                'meta_input'  => array(
                    'ig_points' => $points,
                    'ig_assigned_influencer' => $assigned_influencer,
                    'ig_start_date' => $start_date,
                    'ig_end_date' => $end_date,
                ),
            ));
    
            if ( $task_id ) {
                update_post_meta( $task_id, 'ig_points', $points );
            }
        }
    
        wp_safe_redirect( menu_page_url( 'influencer-gamification-tasks', false ) );
        exit;
    }

    /**
     * Manipula a exclusão de uma tarefa.
     */
    public function handle_delete_task() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'You do not have sufficient permissions to access this page.' );
        }
    
        if ( isset( $_GET['task_id'] ) ) {
            $task_id = intval( $_GET['task_id'] );
            wp_delete_post( $task_id, true );
        }
    
        wp_safe_redirect( menu_page_url( 'influencer-gamification-tasks', false ) );
        exit;
    }


            public function handle_delete_influencer() {
                if ( ! current_user_can( 'manage_options' ) ) {
                    wp_die( 'You do not have sufficient permissions to access this page.' );
                }
            
                if ( isset( $_GET['influencer_id'] ) ) {
                    $influencer_id = intval( $_GET['influencer_id'] );
                    wp_delete_user( $influencer_id );
                }
            
                wp_safe_redirect( menu_page_url( 'influencer-gamification-influencers', false ) );
                exit;
            }
            
            /**
             * Exibe a página de tarefas.
             */
            public function display_tasks_page() {
                include_once 'partials/influencer-gamification-tasks-display.php';
            }
        
            /**
             * Enfileira os estilos do plugin.
             */
            public function enqueue_styles() {
                wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/influencer-gamification-admin.css', array(), $this->version, 'all' );
            }
        
            /**
             * Enfileira os scripts do plugin.
             */
            public function enqueue_scripts() {
                wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/influencer-gamification-admin.js', array( 'jquery' ), $this->version, false );
            }
            
            /**
             * Exibe a página de submissões de tarefas.
             */
            public function display_task_submissions_page() {
                include_once 'partials/influencer-gamification-task-submissions-display.php';
            }
        
            /**
             * Salva as caixas de meta da submissão.
             *
             * @param int $post_id O ID da submissão.
             */
            public function save_submission_meta_boxes( $post_id ) {
                if ( ! isset( $_POST['ig_submission_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['ig_submission_meta_box_nonce'], 'ig_save_submission_meta_boxes' ) ) {
                    return $post_id;
                }
            
                if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                    return $post_id;
                }
            
                if ( ! current_user_can( 'edit_post', $post_id ) ) {
                    return $post_id;
                }
            
                $submission = get_post( $post_id );
                if ( $submission->post_type != 'ig_task_submission' ) {
                    return $post_id;
                }
            
                if ( isset( $_POST['ig_submission_status'] ) ) {
                    $status = sanitize_text_field( $_POST['ig_submission_status'] );
                    update_post_meta( $post_id, 'ig_submission_status', $status );
            
                    if ( $status == 'approved' ) {
                        $points = get_post_meta( get_post_meta( $post_id, 'ig_task_id', true ), 'ig_points', true );
                        $user_id = $submission->post_author;
                        $current_points = get_user_meta( $user_id, 'ig_points', true );
                        $new_points = $current_points + $points;
                        update_user_meta( $user_id, 'ig_points', $new_points );
                        
                        $levels = get_option( 'influencer_gamification_levels', array() );
                        $current_level = get_user_meta( $user_id, 'ig_level', true );
            
                        foreach ( $levels as $level => $level_points ) {
                            if ( $new_points >= $level_points && $current_level < $level ) {
                                update_user_meta( $user_id, 'ig_level', $level );
                                wp_mail(
                                    $submission->post_author->user_email,
                                    __( 'Level Up!', 'influencer-gamification' ),
                                    sprintf( __( 'Congratulations, you have reached level %d!', 'influencer-gamification' ), $level )
                                );
                                break;
                            }
                        }
                    }
                }
            }
        
            /**
             * Exibe as caixas de meta da submissão.
             *
             * @param WP_Post $post O objeto da submissão.
             */
            public function display_submission_meta_boxes( $post ) {
                add_meta_box(
                    'ig_submission_status',
                    'Submission Status',
                    array( $this, 'submission_status_meta_box' ),
                    'ig_task_submission',
                    'side',
                    'default'
                );
            }
        
            /**
             * Exibe a caixa de meta de status da submissão.
             *
             * @param WP_Post $post O objeto da submissão.
             */
            public function submission_status_meta_box( $post ) {
                $status = get_post_meta( $post->ID, 'ig_submission_status', true );
                wp_nonce_field( 'ig_save_submission_meta_boxes', 'ig_submission_meta_box_nonce' );
                ?>
                <select name="ig_submission_status">
                    <option value="pending" <?php selected( $status, 'pending' ); ?>>Pending</option>
                    <option value="approved" <?php selected( $status, 'approved' ); ?>>Approved</option>
                    <option value="rejected" <?php selected( $status, 'rejected' ); ?>>Rejected</option>
                </select>
                <?php
            }
        }