<?php

/* Afficher "À partir de" pour les produits variables */
/* Display "From xxx$" for variable products, change line 15 for english words */
add_filter( 'woocommerce_variable_sale_price_html', 'wpm_variation_price_format', 10, 2 );
add_filter( 'woocommerce_variable_price_html', 'wpm_variation_price_format', 10, 2 );

function wpm_variation_price_format( $price, $product ) {
//On récupère le prix min et max du produit variable
$min_price = $product->get_variation_price( 'min', true );
$max_price = $product->get_variation_price( 'max', true );

// Si les prix sont différents on affiche "À partir de ..."
if ($min_price != $max_price){
$price = sprintf( __( 'A partir de %1$s', 'woocommerce' ), wc_price( $min_price ) );
return $price;
// Sinon on affiche juste le prix
} else {
$price = sprintf( __( '%1$s', 'woocommerce' ), wc_price( $min_price ) );
return $price;
}
}
