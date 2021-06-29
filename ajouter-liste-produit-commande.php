<?php
// Ajouter dans la liste des commandes les produits + quantite + miniature associés à la commande sous le statut
// Add to the orders list the products + quantity + thumbnail related to the order under the order state
add_action('manage_shop_order_posts_custom_column', 'orders_list_preview_items', 20, 2 );
function orders_list_preview_items($column, $post_id) {
    
    global $the_order, $post;
    
    if ('order_status' === $column) {
        
        // Start list
        echo '<ul class="orders-list-items-preview">';
        
        // Loop through order items
        foreach($the_order->get_items() as $item) {
            
            $product = $item->get_product();
            $img     = wp_get_attachment_url($product->get_image_id());
            
            $name    = $item->get_name();
            $qty     = $item->get_quantity();
            
            echo "<li>
                <img src=\"$img\" />
                <label>$qty</label> $name
            </li>";
        }
        
        // End list
        echo '</ul>';
    }
    
    
}


add_action('admin_head', 'orders_list_preview_css');
function orders_list_preview_css() {
  echo "<style>
    .orders-list-items-preview {
        background-color: #eee;
        padding: 8px 8px 0 5px;
        border-radius: 4px;
    }
    .orders-list-items-preview li {
        padding-left: 55px;
        position: relative;
        padding-bottom: 10px;
        padding-right: 40px;
        padding-top: 0;
        font-size: 10px;
        line-height: 11px;
        min-height: 30px;
    }
    .orders-list-items-preview li label {
        border: 1px solid gray;
        width: 25px;
        display: block;
        text-align: center;
        border-radius: 4px;
        right: 5px;
        top: 0px;
        position: absolute;
        font-size: 12px;
        font-weight: bold;
        padding: 5px 0;
    }
    .orders-list-items-preview img {
        margin: 1px 2px;
        position: absolute;
        left: 0;
        top: 0;
        height: 30px;
        max-height: 30px !important;
    }
  </style>";
}