<?php

class Influencer_Gamification_Widget extends WP_Widget {

    public function __construct() {
        $widget_ops = array( 
            'classname' => 'influencer_gamification_widget',
            'description' => __( 'Display the logged in influencer\'s progress.', 'influencer-gamification' ),
        );
        parent::__construct( 'influencer_gamification_widget', __( 'Influencer Progress', 'influencer-gamification' ), $widget_ops );
    }
 
    public function widget( $args, $instance ) {
        if ( ! is_user_logged_in() || ! in_array( 'influencer', (array) wp_get_current_user()->roles ) ) {
            return;
        }

        $title = apply_filters( 'widget_title', $instance['title'] );

        echo $args['before_widget'];
        if ( ! empty( $title ) ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        $user_id = get_current_user_id();
        $points = get_user_meta( $user_id, 'ig_points', true );
        $level = get_user_meta( $user_id, 'ig_level', true );
        ?>
        <div class="influencer-progress">
            <p><?php printf( __( 'Points: %d', 'influencer-gamification' ), $points ); ?></p>
            <p><?php printf( __( 'Level: %d', 'influencer-gamification' ), $level ); ?></p>
        </div>
        <?php
        echo $args['after_widget'];
    }
            
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = __( 'New title', 'influencer-gamification' );
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <?php 
    }
        
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        return $instance;
    }
}