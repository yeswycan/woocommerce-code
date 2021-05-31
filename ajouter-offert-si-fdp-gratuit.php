<?php

// Ajout OFFERT si prix shipping est gratuit
function custom_label( $label, $method ) {
    if ( $method->cost == 0 ) {
        $label .= " (OFFERT)";
    }

    return $label;
}
add_filter( 'woocommerce_cart_shipping_method_full_label', 'custom_label', 10, 2 );