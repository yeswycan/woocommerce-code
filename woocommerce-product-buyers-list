<?php
/**
 * Plugin Name: WooCommerce Product Buyers List
 * Description: Ajoute un onglet dans WooCommerce pour afficher les acheteurs d'un produit spécifique avec filtre de date.
 * Version: 1.0.0
 * Author: Wycan
 * Author URI: https://wycan.fr
 * Text Domain: wc-product-buyers
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class WC_Product_Buyers_List {

    public function __construct() {
        add_action('admin_menu', [$this, 'add_menu_page']);
        add_action('admin_post_export_buyers_csv', [$this, 'export_buyers_csv']);
    }

    public function add_menu_page() {
        add_menu_page(
            __('Product Buyers', 'wc-product-buyers'),
            __('Product Buyers', 'wc-product-buyers'),
            'manage_woocommerce',
            'wc-product-buyers',
            [$this, 'render_page'],
            'dashicons-groups'
        );
    }

    public function render_page() {
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Product Buyers', 'wc-product-buyers') . '</h1>';

        // Form to select a product and date range
        echo '<form method="GET" action="">';
        echo '<input type="hidden" name="page" value="wc-product-buyers">';
        echo '<label for="product_id">' . esc_html__('Select a Product:', 'wc-product-buyers') . '</label> ';
        $this->product_dropdown();

        echo '<label for="start_date" style="margin-left:20px;">' . esc_html__('Start Date:', 'wc-product-buyers') . '</label>'; 
        echo '<input type="date" name="start_date" value="' . esc_attr($_GET['start_date'] ?? '') . '">';

        echo '<label for="end_date" style="margin-left:20px;">' . esc_html__('End Date:', 'wc-product-buyers') . '</label>'; 
        echo '<input type="date" name="end_date" value="' . esc_attr($_GET['end_date'] ?? '') . '">';

        echo '<input type="submit" class="button-primary" value="' . esc_attr__('Show Buyers', 'wc-product-buyers') . '" style="margin-left:20px;">';
        echo '</form>';

        if (isset($_GET['product_id']) && !empty($_GET['product_id'])) {
            $product_id = (int) $_GET['product_id'];
            $start_date = $_GET['start_date'] ?? '';
            $end_date = $_GET['end_date'] ?? '';

            // Export Button
            echo '<div style="display: flex; justify-content: flex-end; margin-top: 10px;">';
            echo '<form method="POST" action="' . admin_url('admin-post.php') . '">';
            echo '<input type="hidden" name="action" value="export_buyers_csv">';
            echo '<input type="hidden" name="product_id" value="' . esc_attr($product_id) . '">';
            echo '<input type="hidden" name="start_date" value="' . esc_attr($start_date) . '">';
            echo '<input type="hidden" name="end_date" value="' . esc_attr($end_date) . '">';
            echo '<input type="submit" class="button-secondary" value="' . esc_attr__('Export Users', 'wc-product-buyers') . '">';
            echo '</form>';
            echo '</div>';

            $this->display_buyers_list($product_id, $start_date, $end_date);
        }

        echo '</div>';
    }

    private function product_dropdown() {
        $args = [
            'limit' => -1, // Aucun limite pour récupérer tous les produits
            'orderby' => 'name', // Trier par nom pour faciliter la sélection
            'order' => 'ASC',
        ];

        $products = wc_get_products($args);

        echo '<select name="product_id" id="product_id">';
        echo '<option value="">' . esc_html__('Select a product', 'wc-product-buyers') . '</option>';

        if (!empty($products)) {
            foreach ($products as $product) {
                $selected = (isset($_GET['product_id']) && (int) $_GET['product_id'] === $product->get_id()) ? 'selected' : '';
                echo '<option value="' . esc_attr($product->get_id()) . '" ' . $selected . '>' . esc_html($product->get_name()) . '</option>';
            }
        } else {
            echo '<option value="">' . esc_html__('No products found', 'wc-product-buyers') . '</option>';
        }

        echo '</select>';
    }

    private function display_buyers_list($product_id, $start_date, $end_date) {
        global $wpdb;

        $query = "SELECT DISTINCT pm.meta_value AS email, om.meta_value AS first_name, onm.meta_value AS last_name
                  FROM {$wpdb->prefix}woocommerce_order_items AS oi
                  INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim
                    ON oi.order_item_id = oim.order_item_id
                  INNER JOIN {$wpdb->prefix}posts AS p
                    ON oi.order_id = p.ID
                  INNER JOIN {$wpdb->prefix}postmeta AS pm
                    ON oi.order_id = pm.post_id AND pm.meta_key = '_billing_email'
                  INNER JOIN {$wpdb->prefix}postmeta AS om
                    ON oi.order_id = om.post_id AND om.meta_key = '_billing_first_name'
                  INNER JOIN {$wpdb->prefix}postmeta AS onm
                    ON oi.order_id = onm.post_id AND onm.meta_key = '_billing_last_name'
                  WHERE oim.meta_key = '_product_id' AND oim.meta_value = %d";

        $params = [$product_id];

        if (!empty($start_date)) {
            $query .= " AND DATE(p.post_date) >= %s";
            $params[] = $start_date;
        }

        if (!empty($end_date)) {
            $query .= " AND DATE(p.post_date) <= %s";
            $params[] = $end_date;
        }

        $results = $wpdb->get_results($wpdb->prepare($query, $params));

        if ($results) {
            echo '<h2>' . esc_html__('Buyers List', 'wc-product-buyers') . '</h2>';
            echo '<table class="widefat fixed">';
            echo '<thead><tr><th>' . esc_html__('First Name', 'wc-product-buyers') . '</th><th>' . esc_html__('Last Name', 'wc-product-buyers') . '</th><th>' . esc_html__('Email', 'wc-product-buyers') . '</th></tr></thead>';
            echo '<tbody>';

            foreach ($results as $buyer) {
                echo '<tr>';
                echo '<td>' . esc_html($buyer->first_name) . '</td>';
                echo '<td>' . esc_html($buyer->last_name) . '</td>';
                echo '<td>' . esc_html($buyer->email) . '</td>';
                echo '</tr>';
            }

            echo '</tbody></table>';
        } else {
            echo '<p>' . esc_html__('No buyers found for this product.', 'wc-product-buyers') . '</p>';
        }
    }

    public function export_buyers_csv() {
        if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
            wp_die(__('Invalid product ID', 'wc-product-buyers'));
        }

        $product_id = (int) $_POST['product_id'];
        $start_date = $_POST['start_date'] ?? '';
        $end_date = $_POST['end_date'] ?? '';

        global $wpdb;

        $query = "SELECT DISTINCT pm.meta_value AS email, om.meta_value AS first_name, onm.meta_value AS last_name
                  FROM {$wpdb->prefix}woocommerce_order_items AS oi
                  INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim
                    ON oi.order_item_id = oim.order_item_id
                  INNER JOIN {$wpdb->prefix}posts AS p
                    ON oi.order_id = p.ID
                  INNER JOIN {$wpdb->prefix}postmeta AS pm
                    ON oi.order_id = pm.post_id AND pm.meta_key = '_billing_email'
                  INNER JOIN {$wpdb->prefix}postmeta AS om
                    ON oi.order_id = om.post_id AND om.meta_key = '_billing_first_name'
                  INNER JOIN {$wpdb->prefix}postmeta AS onm
                    ON oi.order_id = onm.post_id AND onm.meta_key = '_billing_last_name'
                  WHERE oim.meta_key = '_product_id' AND oim.meta_value = %d";

        $params = [$product_id];

        if (!empty($start_date)) {
            $query .= " AND DATE(p.post_date) >= %s";
            $params[] = $start_date;
        }

        if (!empty($end_date)) {
            $query .= " AND DATE(p.post_date) <= %s";
            $params[] = $end_date;
        }

        $results = $wpdb->get_results($wpdb->prepare($query, $params));

        if (empty($results)) {
            wp_die(__('No buyers found for this product.', 'wc-product-buyers'));
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=buyers_list.csv');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['First Name', 'Last Name', 'Email']);

        foreach ($results as $buyer) {
            fputcsv($output, [$buyer->first_name, $buyer->last_name, $buyer->email]);
        }

        fclose($output);
        exit;
    }
}

new WC_Product_Buyers_List();
