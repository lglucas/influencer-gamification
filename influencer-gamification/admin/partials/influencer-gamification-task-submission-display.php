<?php
if ( ! is_user_logged_in() ) {
    wp_redirect( wp_login_url( get_permalink() ) );
    exit;
}

$user_id = get_current_user_id();
if ( ! in_array( 'influencer', (array) $user->roles ) ) {
    wp_die( 'You do not have sufficient permissions to access this page.' );
}

$tasks = get_posts( array(
    'post_type'   => 'ig_task',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'meta_query' => array(
        'relation' => 'OR',
        array(
            'key' => 'ig_assigned_influencer',
            'value' => $user_id,
        ),
        array(
            'key' => 'ig_assigned_influencer',
            'value' => '0',
        ),
    ),
) );
?>

<div class="wrap">
    <h2>Tasks</h2>
    <?php if ( ! empty( $tasks ) ) : ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th scope="col" id="title" class="manage-column">Title</th>
                    <th scope="col" id="description" class="manage-column">Description</th>
                    <th scope="col" id="points" class="manage-column">Points</th>
                    <th scope="col" id="actions" class="manage-column">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $tasks as $task ) : ?>
                    <tr>
                        <td><?php echo esc_html( $task->post_title ); ?></td>
                        <td><?php echo esc_html( $task->post_content ); ?></td>
                        <td><?php echo esc_html( get_post_meta( $task->ID, 'ig_points', true ) ); ?></td>
                        <td>
                            <a href="<?php echo esc_url( add_query_arg( 'task_id', $task->ID ) ); ?>">Submit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>No tasks found.</p>
    <?php endif; ?>
</div>