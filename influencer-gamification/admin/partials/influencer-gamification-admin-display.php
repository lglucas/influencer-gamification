<div class="wrap">
    <h1>Influencer Gamification Settings</h1>
    <form method="post" action="options.php">
        <?php 
        settings_fields( 'influencer_gamification_options' );
        do_settings_sections( 'influencer-gamification' ); 
        submit_button();
        ?>
    </form>
</div>