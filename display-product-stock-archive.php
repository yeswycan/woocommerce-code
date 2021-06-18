<?php
// Afficher le stock des produits sur la page d'archive des produits (catégorie)

add_action( 'woocommerce_after_shop_loop_item', 'wycan_show_stock_shop', 15 );
function wycan_show_stock_shop() {
   global $product;
   echo wc_get_stock_html( $product );
}