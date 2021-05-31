<?php
// Afficher les produits par ordre alphabetique des UGS dans les commandes et bons de livraison + factures
add_filter( 'woocommerce_order_get_items', 'filter_order_get_items_by_sku', 10, 3 );
function filter_order_get_items_by_sku( $items, $order, $types ) {
    if( count($items) > 1 ) {
        $item_skus = $sorted_items = array();

        // Parcourir les elements de la commande en boucle
        foreach( $items as $items_id => $item ){
            // Vérifier les produits avant WC 3.3
            if( $item->is_type('line_item') ){
                $product = $item->get_product(); // Obtenir l'objet produit
                
                if ($product instanceof WC_Product) {
                    $item_skus[$product->get_sku()] = $items_id;
                }
            }
        }

        // Seulement pour les produits qui ont un SKU/UGS
        if( ! empty($item_skus) ) {
            // Afficher dans l'ordre ASC en fonction des SKU;
            ksort($item_skus); // Utiliser krsort() pour afficher en DESC

            // Loop through sorted $item_skus array
            foreach( $item_skus as $sku => $item_id ){
                // Afficher les produits dans le bon ordre
                $sorted_items[$item_id] = $items[$item_id];
            }
            $items = $sorted_items;
        }
    }
    return $items;
}
