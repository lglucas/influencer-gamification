<?php

/**
 * Classe responsável pela funcionalidade pública do plugin.
 */
class Influencer_Gamification_Public {

    /**
     * O ID do plugin.
     *
     * @var string $plugin_name
     */
    private $plugin_name;

    /**
     * A versão do plugin.
     *
     * @var string $version
     */
    private $version;

    /**
     * Inicializa a classe e define suas propriedades.
     *
     * @param string $plugin_name O nome do plugin.
     * @param string $version A versão do plugin.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        // Registra os shortcodes
        add_shortcode( 'ig_task_submission', array( $this, 'display_task_submission_page' ) );
        add_shortcode( 'ig_task_submission_form', array( $this, 'display_task_submission_form' ) );
        add_shortcode( 'ig_leaderboard', array( $this, 'leaderboard_shortcode' ) );

        // Registra as ações
        add_action( 'admin_post_ig_submit_task', array( $this, 'handle_submit_task' ) );
        add_action( 'init', array( $this, 'register_task_submission_post_type' ) );
        add_action( 'init', array( $this, 'register_task_post_type' ) );
        add_action( 'widgets_init', array( $this, 'register_widgets' ) );
        add_action( 'show_user_profile', array( $this, 'register_influencer_profile_fields' ) );
        add_action( 'edit_user_profile', array( $this, 'register_influencer_profile_fields' ) );
    }

    // ... Restante do código ...

    /**
     * Callback do shortcode do quadro de líderes.
     *
     * @param array $atts Atributos do shortcode.
     * @return string Saída do shortcode.
     */
    public function leaderboard_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'limit' => 10,
        ), $atts, 'ig_leaderboard' );
    
        $limit = intval( $atts['limit'] );
    
        $influencers = get_users( array(
            'role'    => 'influencer',
            'orderby' => 'meta_value_num',
            'meta_key' => 'ig_points',
            'order'   => 'DESC',
            'number'  => $limit,
        ) );
    
        ob_start();
        include_once 'partials/influencer-gamification-leaderboard-display.php';
        return ob_get_clean();
    }

    /**
     * Manipula o envio de tarefas.
     */
    public function handle_submit_task() {
        if ( ! is_user_logged_in() ) {
            wp_die( __( 'Você precisa estar logado para enviar uma tarefa.', 'influencer-gamification' ) );
        }
    
        $user = wp_get_current_user();
        if ( ! in_array( 'influencer', (array) $user->roles ) ) {
            wp_die( __( 'Você não tem permissões suficientes para enviar uma tarefa.', 'influencer-gamification' ) );
        }
    
        if ( ! isset( $_POST['task_id'], $_POST['ig_submit_task_nonce'] ) ) {
            wp_die( __( 'Requisição inválida.', 'influencer-gamification' ) );
        }
    
        $task_id = intval( $_POST['task_id'] );
        if ( ! wp_verify_nonce( $_POST['ig_submit_task_nonce'], 'ig_submit_task_' . $task_id ) ) {
            wp_die( __( 'Nonce inválido.', 'influencer-gamification' ) );
        }
    
        $task = get_post( $task_id );
        if ( ! $task || $task->post_type !== 'ig_task' || $task->post_status !== 'publish' ) {
            wp_die( __( 'Tarefa inválida.', 'influencer-gamification' ) );
        }
    
        $proof = $_FILES['proof'];
        $comments = sanitize_textarea_field( $_POST['comments'] );
    
        // Aqui você pode realizar as validações necessárias no arquivo de prova e nos comentários
    
        $submission_id = wp_insert_post( array(
            'post_author' => $user->ID,
            'post_title'  => sprintf( __( 'Envio para a tarefa %d por %s', 'influencer-gamification' ), $task_id, $user->user_login ),
            'post_status' => 'pending',
            'post_type'   => 'ig_task_submission',
            'meta_input'  => array(
                'ig_task_id' => $task_id,
                'ig_proof'   => $proof,
                'ig_comments' => $comments,
            ),
        ) );
    
        if ( $submission_id ) {
            // Obtém o número de pontos da configuração
            $points = get_option('influencer_gamification_settings')['points_per_task'];

            // Atualiza os pontos do usuário
            $current_points = get_user_meta($user->ID, 'ig_points', true);
            update_user_meta($user->ID, 'ig_points', $current_points + $points);

            wp_safe_redirect( add_query_arg( 'submission_success', 1, get_permalink( get_page_by_path( 'task-submission' ) ) ) );
        } else {
            wp_die( __( 'Erro ao enviar a tarefa. Por favor, tente novamente.', 'influencer-gamification' ) );
        }
        exit;
    }
    
    /**
 * Registra os widgets do plugin.
 */
public function register_widgets() {
    // Carrega a classe do widget de progresso
    if (!class_exists('Influencer_Gamification_Widget')) {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/widgets/class-influencer-gamification-widget.php';
    }
    register_widget('Influencer_Gamification_Widget');

    // Carrega a classe do widget de quadro de líderes
    if (!class_exists('Influencer_Gamification_Leaderboard_Widget')) {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/widgets/class-influencer-gamification-leaderboard-widget.php';
    }
    register_widget('Influencer_Gamification_Leaderboard_Widget');
}

    // ... Restante do código ...    
    /**
     * Register custom post type for task submissions.
     */
    public function register_task_submission_post_type() {
        $labels = array(
            'name'                  => _x( 'Task Submissions', 'Post Type General Name', 'influencer-gamification' ),
            'singular_name'         => _x( 'Task Submission', 'Post Type Singular Name', 'influencer-gamification' ),
            'menu_name'             => __( 'Task Submissions', 'influencer-gamification' ),
            'name_admin_bar'        => __( 'Task Submission', 'influencer-gamification' ),
            'archives'              => __( 'Task Submission Archives', 'influencer-gamification' ),
            'attributes'            => __( 'Task Submission Attributes', 'influencer-gamification' ),
            'parent_item_colon'     => __( 'Parent Task Submission:', 'influencer-gamification' ),
            'all_items'             => __( 'All Task Submissions', 'influencer-gamification' ),
            'add_new_item'          => __( 'Add New Task Submission', 'influencer-gamification' ),
            'add_new'               => __( 'Add New', 'influencer-gamification' ),
            'new_item'              => __( 'New Task Submission', 'influencer-gamification' ),
            'edit_item'             => __( 'Edit Task Submission', 'influencer-gamification' ),
            'update_item'           => __( 'Update Task Submission', 'influencer-gamification' ),
            'view_item'             => __( 'View Task Submission', 'influencer-gamification' ),
            'view_items'            => __( 'View Task Submissions', 'influencer-gamification' ),
            'search_items'          => __( 'Search Task Submission', 'influencer-gamification' ),
            'not_found'             => __( 'Not found', 'influencer-gamification' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'influencer-gamification' ),
            'featured_image'        => __( 'Featured Image', 'influencer-gamification' ),
            'set_featured_image'    => __( 'Set featured image', 'influencer-gamification' ),
            'remove_featured_image' => __( 'Remove featured image', 'influencer-gamification' ),
            'use_featured_image'    => __( 'Use as featured image', 'influencer-gamification' ),
            'insert_into_item'      => __( 'Insert into task submission', 'influencer-gamification' ),
            'uploaded_to_this_item' => __( 'Uploaded to this task submission', 'influencer-gamification' ),
            'items_list'            => __( 'Task submissions list', 'influencer-gamification' ),
            'items_list_navigation' => __( 'Task submissions list navigation', 'influencer-gamification' ),
            'filter_items_list'     => __( 'Filter task submissions list', 'influencer-gamification' ),
        );
    
        $args = array(
            'label'                 => __( 'Task Submission', 'influencer-gamification' ),
            'description'           => __( 'Influencer task submissions', 'influencer-gamification' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'author' ),
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => false,
            'menu_position'         => 5,
            'show_in_admin_bar'     => false,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => 'page',
        );
    
        register_post_type( 'ig_task_submission', $args );
    }

    /**
     * Register custom post type for tasks.
     */
    public function register_task_post_type() {
        $labels = array(
            'name'                  => _x( 'Tasks', 'Post Type General Name', 'influencer-gamification' ),
            'singular_name'         => _x( 'Task', 'Post Type Singular Name', 'influencer-gamification' ),
            'menu_name'             => __( 'Tasks', 'influencer-gamification' ),
            'name_admin_bar'        => __( 'Task', 'influencer-gamification' ),
            'archives'              => __( 'Task Archives', 'influencer-gamification' ),
            'attributes'            => __( 'Task Attributes', 'influencer-gamification' ),
            'parent_item_colon'     => __( 'Parent Task:', 'influencer-gamification' ),
            'all_items'             => __( 'All Tasks', 'influencer-gamification' ),
            'add_new_item'          => __( 'Add New Task', 'influencer-gamification' ),
            'add_new'               => __( 'Add New', 'influencer-gamification' ),
            'new_item'              => __( 'New Task', 'influencer-gamification' ),
            'edit_item'             => __( 'Edit Task', 'influencer-gamification' ),
            'update_item'           => __( 'Update Task', 'influencer-gamification' ),
            'view_item'             => __( 'View Task', 'influencer-gamification' ),
            'view_items'            => __( 'View Tasks', 'influencer-gamification' ),
            'search_items'          => __( 'Search Task', 'influencer-gamification' ),
            'not_found'             => __( 'Not found', 'influencer-gamification' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'influencer-gamification' ),
            'featured_image'        => __( 'Featured Image', 'influencer-gamification' ),
            'set_featured_image'    => __( 'Set featured image', 'influencer-gamification' ),
            'remove_featured_image' => __( 'Remove featured image', 'influencer-gamification' ),
            'use_featured_image'    => __( 'Use as featured image', 'influencer-gamification' ),
            'insert_into_item'      => __( 'Insert into task', 'influencer-gamification' ),
            'uploaded_to_this_item' => __( 'Uploaded to this task', 'influencer-gamification' ),
            'items_list'            => __( 'Tasks list', 'influencer-gamification' ),
            'items_list_navigation' => __( 'Tasks list navigation', 'influencer-gamification' ),
            'filter_items_list'     => __( 'Filter tasks list', 'influencer-gamification' ),
        );
    
        $args = array(
            'label'                 => __( 'Task', 'influencer-gamification' ),
            'description'           => __( 'Influencer tasks', 'influencer-gamification' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor' ),
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => false,
            'menu_position'         => 5,
            'show_in_admin_bar'     => false,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => 'page',
        );
    
        register_post_type( 'ig_task', $args );
    }
    
    /**
     * Register influencer profile fields.
     *
     * @param WP_User $user The current WP_User object.
     */
    public function register_influencer_profile_fields( $user ) {
        if ( in_array( 'influencer', (array) $user->roles ) ) {
            ?>
            <h3><?php _e( 'Influencer Gamification', 'influencer-gamification' ); ?></h3>
    
            <table class="form-table">
                <tr>
                    <th><label for="ig_points"><?php _e( 'Points', 'influencer-gamification' ); ?></label></th>
                    <td>
                        <input type="number" name="ig_points" id="ig_points" value="<?php echo esc_attr( get_user_meta( $user->ID, 'ig_points', true ) ); ?>" class="regular-text" readonly />
                    </td>
                </tr>
                <tr>
                    <th><label for="ig_level"><?php _e( 'Level', 'influencer-gamification' ); ?></label></th>
                    <td>
                        <input type="number" name="ig_level" id="ig_level" value="<?php echo esc_attr( get_user_meta( $user->ID, 'ig_level', true ) ); ?>" class="regular-text" readonly />
                    </td>
                </tr>
            </table>
            <?php
        }
    }

}