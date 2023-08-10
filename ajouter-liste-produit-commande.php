// Ajouter les en-têtes de colonnes personnalisées
function custom_order_column_headers($columns) {
    $columns['products'] = 'Produits';
    $columns['quantity'] = 'Quantité';
    return $columns;
}
add_filter('manage_edit-shop_order_columns', 'custom_order_column_headers');

// Remplir les colonnes avec les données
function custom_order_column_data($column) {
    global $post;

    if ($column == 'products') {
        $order = wc_get_order($post->ID);
        $items = $order->get_items();

        echo '<ul>';
        foreach ($items as $item_id => $item_data) {
            $product = $item_data->get_product();
            echo '<li>' . $product->get_name() . '</li>';
        }
        echo '</ul>';
    }

    if ($column == 'quantity') {
        $order = wc_get_order($post->ID);
        $items = $order->get_items();

        echo '<ul>';
        foreach ($items as $item_id => $item_data) {
            echo '<li>' . $item_data->get_quantity() . '</li>';
        }
        echo '</ul>';
    }
}
add_action('manage_shop_order_posts_custom_column', 'custom_order_column_data');

// Rendre les colonnes triables
function custom_order_column_sortable($columns) {
    $columns['products'] = 'products';
    $columns['quantity'] = 'quantity';
    return $columns;
}
add_filter('manage_edit-shop_order_sortable_columns', 'custom_order_column_sortable');
