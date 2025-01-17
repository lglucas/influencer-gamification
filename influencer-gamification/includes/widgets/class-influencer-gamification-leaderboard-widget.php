<?php
/**
 * Widget do quadro de líderes dos influenciadores.
 */
class Influencer_Gamification_Leaderboard_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'influencer_gamification_leaderboard',
            __('Quadro de Líderes', 'influencer-gamification'),
            array(
                'description' => __('Exibe o ranking dos influenciadores', 'influencer-gamification'),
            )
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        $title = !empty($instance['title']) ? apply_filters('widget_title', $instance['title']) : __('Quadro de Líderes', 'influencer-gamification');
        $limit = !empty($instance['limit']) ? absint($instance['limit']) : 5;

        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        $influencers = get_users(array(
            'role' => 'influencer',
            'meta_key' => 'ig_points',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
            'number' => $limit
        ));

        if (!empty($influencers)) {
            echo '<div class="influencer-leaderboard">';
            echo '<table class="leaderboard-table">';
            echo '<thead><tr>';
            echo '<th>' . __('Posição', 'influencer-gamification') . '</th>';
            echo '<th>' . __('Nome', 'influencer-gamification') . '</th>';
            echo '<th>' . __('Pontos', 'influencer-gamification') . '</th>';
            echo '</tr></thead><tbody>';

            foreach ($influencers as $index => $influencer) {
                $points = get_user_meta($influencer->ID, 'ig_points', true);
                echo '<tr>';
                echo '<td>' . ($index + 1) . '</td>';
                echo '<td>' . esc_html($influencer->display_name) . '</td>';
                echo '<td>' . esc_html($points) . '</td>';
                echo '</tr>';
            }

            echo '</tbody></table></div>';
        } else {
            echo '<p>' . __('Nenhum influenciador encontrado.', 'influencer-gamification') . '</p>';
        }

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Quadro de Líderes', 'influencer-gamification');
        $limit = isset($instance['limit']) ? absint($instance['limit']) : 5;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Título:', 'influencer-gamification'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" 
                   name="<?php echo $this->get_field_name('title'); ?>" type="text" 
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Número de influenciadores:', 'influencer-gamification'); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id('limit'); ?>" 
                   name="<?php echo $this->get_field_name('limit'); ?>" type="number" 
                   step="1" min="1" value="<?php echo esc_attr($limit); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['limit'] = (!empty($new_instance['limit'])) ? absint($new_instance['limit']) : 5;
        return $instance;
    }
}