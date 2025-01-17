<?php
/**
 * Fornece uma visualização pública para o quadro de líderes
 */

$influencers = get_users(array(
    'meta_key' => 'ig_points',
    'orderby' => 'meta_value_num',
    'order' => 'DESC',
    'number' => $limit,
    'role' => 'influencer'
));
?>

<div class="wrap">
    <h2><?php _e('Quadro de Líderes de Influenciadores', 'influencer-gamification'); ?></h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th scope="col"><?php _e('Classificação', 'influencer-gamification'); ?></th>
                <th scope="col"><?php _e('Nome', 'influencer-gamification'); ?></th>
                <th scope="col"><?php _e('Pontos', 'influencer-gamification'); ?></th>
                <th scope="col"><?php _e('Nível', 'influencer-gamification'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($influencers as $index => $influencer) : ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo $influencer->display_name; ?></td>
                    <td><?php echo get_user_meta($influencer->ID, 'ig_points', true); ?></td>
                    <td><?php echo get_user_meta($influencer->ID, 'ig_level', true); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>