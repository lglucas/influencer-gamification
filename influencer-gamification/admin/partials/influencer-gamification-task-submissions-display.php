<div class="wrap">
    <h1>Task Submissions</h1>
    <hr class="wp-header-end">
    
    <?php
    $submissions = get_posts( array(
        'post_type'   => 'ig_task_submission',
        'post_status' => 'pending',
        'numberposts' => -1,
    ) );

    if ( $submissions ) :
    ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th scope="col" class="manage-column">Influencer</th>
                    <th scope="col" class="manage-column">Task</th>
                    <th scope="col" class="manage-column">Submitted</th>
                    <th scope="col" class="manage-column">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $submissions as $submission ) : ?>
                    <tr>
                        <td><?php echo get_the_author_meta( 'display_name', $submission->post_author ); ?></td>
                        <td><?php echo get_the_title( get_post_meta( $submission->ID, 'ig_task_id', true ) ); ?></td>
                        <td><?php echo $submission->post_date; ?></td>
                        <td>
                            <a href="<?php echo esc_url( admin_url( 'post.php?post=' . $submission->ID . '&action=edit' ) ); ?>">Review</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>No pending task submissions found.</p>
    <?php endif; ?>
</div>