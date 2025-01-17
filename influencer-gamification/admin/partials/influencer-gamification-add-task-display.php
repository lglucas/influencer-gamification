<div class="wrap">
    <h1>Add Task</h1>

    <form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
        <input type="hidden" name="action" value="ig_add_task">
        <?php wp_nonce_field( 'ig_add_task', 'ig_add_task_nonce' ); ?>
        <table class="form-table">
    <tr>
        <th scope="row"><label for="assigned_influencer">Assigned Influencer</label></th>
        <td>
            <select name="assigned_influencer" id="assigned_influencer">
                <option value="">All Influencers</option>
                <?php
                $influencers = get_users( array( 'role' => 'influencer' ) );
                foreach ( $influencers as $influencer ) {
                    echo '<option value="' . $influencer->ID . '">' . $influencer->display_name . '</option>';
                }
                ?>
            </select>
        </td>
    </tr>
    <!-- Outros campos da tarefa aqui -->

        <table class="form-table">
            <tr>
                <th scope="row"><label for="title">Title</label></th>
                <td><input type="text" name="title" id="title" class="regular-text" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="description">Description</label></th>
                <td><textarea name="description" id="description" rows="5" cols="30" required></textarea></td>
            </tr>
            <tr>
                <th scope="row"><label for="points">Points</label></th>
                <td><input type="number" name="points" id="points" class="regular-text" required></td>
            </tr>
            <tr>
    <th scope="row"><label for="start_date">Start Date</label></th>
    <td><input type="date" name="start_date" id="start_date" class="regular-text"></td>
</tr>
<tr>
    <th scope="row"><label for="end_date">End Date</label></th>
    <td><input type="date" name="end_date" id="end_date" class="regular-text"></td>
</tr>
        </table>

        <?php submit_button( 'Add Task' ); ?>
    </form>
</div>