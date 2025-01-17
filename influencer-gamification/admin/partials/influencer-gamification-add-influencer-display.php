<?php
// Garantimos que este arquivo só pode ser acessado através do WordPress
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <!-- O formulário agora aponta para admin-post.php, que é o endpoint correto para processamento de formulários admin -->
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <!-- Campo de segurança nonce -->
        <?php wp_nonce_field('ig_add_influencer', 'ig_add_influencer_nonce'); ?>
        
        <!-- Campo oculto que identifica a ação do formulário -->
        <input type="hidden" name="action" value="ig_add_influencer">
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="user_email"><?php _e('Email Address', 'influencer-gamification'); ?></label>
                </th>
                <td>
                    <input type="email" name="user_email" id="user_email" class="regular-text" required>
                </td>
            </tr>
        </table>
        
        <?php submit_button(__('Add Influencer', 'influencer-gamification')); ?>
    </form>
</div>