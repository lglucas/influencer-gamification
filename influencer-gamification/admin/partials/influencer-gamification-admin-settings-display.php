<div class="wrap">
    <h1><?php _e('Influencer Gamification Settings', 'influencer-gamification'); ?></h1>
    <form method="post" action="options.php">
        <?php
        settings_fields('influencer_gamification_settings');
        do_settings_sections('influencer_gamification_settings');
        submit_button();
        ?>
    </form>
</div>