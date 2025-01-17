<div class="wrap">
    <h1>Tasks</h1>
    <a href="<?php menu_page_url( 'influencer-gamification-add-task', true ) ?>" class="page-title-action">Add New</a>
    <hr class="wp-header-end">
    
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
            <?php 
            $tasks = get_posts( array(
                'post_type'   => 'ig_task',
                'post_status' => 'publish',
                'posts_per_page' => -1,
            ) );

            foreach ( $tasks as $task ) : 
            ?>
                <tr>
                    <td><?php echo esc_html( $task->post_title ); ?></td>
                    <td><?php echo esc_html( $task->post_content ); ?></td>
                    <td><?php echo esc_html( get_post_meta( $task->ID, 'ig_points', true ) ); ?></td>
                    <td>
                        <a href="<?php echo esc_url( admin_url( 'post.php?post=' . $task->ID . '&action=edit' ) ); ?>">Edit</a> | 
                        <a href="<?php echo esc_url( admin_url( 'admin-post.php?action=ig_delete_task&task_id=' . $task->ID ) ); ?>" onclick="return confirm('Are you sure you want to delete this task?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>