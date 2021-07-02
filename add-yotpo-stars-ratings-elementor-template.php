<?php
// Shortcode qui va permettre d'afficher la notation avec les étoiles pour YotPo sur un template Elementor, en ajoutant via un widget
// elementor le shortcode [wycan_yotpo_stars_rating]
// Shortcode who allow you to display the YotPo stars rating where you want on an elementor product template.
// Simply add a shortcode elementor widget and add the shortcode [wycan_yotpo_stars_rating]

function wycan_yotpo_stars_single( $atts ) {
if ( function_exists( 'wc_yotpo_show_buttomline' ) )
{ add_action( 'woocommerce_single_product_summary', 'wc_yotpo_show_buttomline', 15 ); }
}
add_shortcode( 'wycan_yotpo_stars_rating', 'wc_yotpo_show_buttomline');
