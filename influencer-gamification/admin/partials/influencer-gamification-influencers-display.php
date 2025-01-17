<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <h1>Influencers</h1>
    <a href="<?php menu_page_url( 'influencer-gamification-add-influencer', true ) ?>" class="page-title-action">Add New</a>
    <hr class="wp-header-end">
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th scope="col" id="name" class="manage-column">Name</th>
                <th scope="col" id="email" class="manage-column">Email</th>
                <th scope="col" id="points" class="manage-column">Points</th>
                <th scope="col" id="level" class="manage-column">Level</th>
                <th scope="col" id="actions" class="manage-column">Actions</th> 
            </tr>
        </thead>
        <tbody>
        <?php foreach ( $influencers as $influencer ) : ?>
    <tr>
        <td><?php echo esc_html( $influencer->display_name ); ?></td>
        <td><?php echo esc_html( $influencer->user_email ); ?></td>
        <td><?php echo esc_html( get_user_meta( $influencer->ID, 'ig_points', true ) ); ?></td>
        <td><?php echo esc_html( get_user_meta( $influencer->ID, 'ig_level', true ) ); ?></td>
        <td>
    <a href="<?php echo esc_url( admin_url( 'user-edit.php?user_id=' . $influencer->ID ) ); ?>">Edit</a> |
    <a href="<?php echo esc_url( admin_url( 'admin-post.php?action=ig_delete_influencer&influencer_id=' . $influencer->ID ) ); ?>" onclick="return confirm('Are you sure you want to delete this influencer?');">Delete</a>
</td>
    </tr>
<?php endforeach; ?>
        </tbody>
    </table>
</div>