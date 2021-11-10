<?php

/* Redirection personnalisé aprés un paiement réussi */

add_action( 'woocommerce_thankyou', 'wycan_woo_payment_redirect');

function wycan_woo_payment_redirect( $order_id ){
    $order = wc_get_order( $order_id );
    $url_thank_you_page = '/thank-you-page/';
    if ( ! $order->has_status( 'failed' ) ) {
        wp_safe_redirect( $url_thank_you_page );
        exit;
    }
}

/* Redirection personnalisé aprés un paiement réussi + quand un paiement est refusé */

add_action( 'woocommerce_thankyou', 'wycan_woo_payment_redirect');

function wycan_woo_payment_redirect( $order_id ){
    $order = wc_get_order( $order_id );
    $url_thank_you_page = '/thank-you-page/';
    $url_paiement_failed = '/failed/';
    if ( ! $order->has_status( 'failed' ) ) {
        wp_safe_redirect( $url_thank_you_page );
        exit;
    } else {
        wp_safe_redirect( $url_paiement_failed );
        exit;
    }
}
