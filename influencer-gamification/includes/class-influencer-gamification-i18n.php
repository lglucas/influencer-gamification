<?php

class Influencer_Gamification_i18n {

    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'influencer-gamification',
            false,
            dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
        );
    }

}