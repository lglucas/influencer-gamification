<?php
if ( ! is_user_logged_in() ) {
    wp_redirect( wp_login_url( get_permalink() ) );
    exit;
}

$user = wp_get_current_user();
if ( ! in_array( 'influencer', (array) $user->roles ) ) {
    wp_die( 'You do not have sufficient permissions to access this page.' );
}

if ( ! isset( $_GET['task_id'] ) ) {
    wp_die( 'Invalid task.' );
}

$task_id = intval( $_GET['task_id'] );
$task = get_post( $task_id );

if ( ! $task || $task->post_type !== 'ig_task' || $task->post_status !== 'publish' ) {
    wp_die( 'Invalid task.' );
}
?>

<div class="wrap">
    <h2>Submit Task: <?php echo esc_html( $task->post_title ); ?></h2>

    <form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="ig_submit_task">
        <input type="hidden" name="task_id" value="<?php echo esc_attr( $task_id ); ?>">
        <?php wp_nonce_field( 'ig_submit_task_' . $task_id, 'ig_submit_task_nonce' ); ?>

        <table class="form-table">
            <tr>
                <th scope="row"><label for="proof">Proof</label></th>
                <td><input type="file" name="proof" id="proof" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="comments">Comments</label></th>
                <td><textarea name="comments" id="comments" rows="5" cols="30"></textarea></td>
            </tr>
        </table>

        <?php submit_button( 'Submit Task' ); ?>
    </form>
</div>