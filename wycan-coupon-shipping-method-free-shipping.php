<?php
/**
 * Plugin Name: Wycan - Free Shipping per Coupon Shipping Method
 * Description: Permet de lier un coupon WooCommerce à une ou plusieurs méthodes de livraison spécifiques pour offrir la livraison gratuite uniquement sur ces méthodes.
 * Author: Wycan
 * Author URI: https://wycan.fr
 * Version: 1.0.1
 * License: GPL-2.0+
 * Text Domain: wycan
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Wycan_Coupon_Shipping_Method_Free_Shipping {

    /**
     * Cache des méthodes de livraison disponibles.
     *
     * @var array|null
     */
    protected $shipping_method_options = null;

    public function __construct() {
        // Champ dans l’admin des coupons
        add_action( 'woocommerce_coupon_options_usage_restriction', array( $this, 'add_coupon_shipping_method_field' ), 10, 2 );
        add_action( 'woocommerce_coupon_options_save', array( $this, 'save_coupon_shipping_method_field' ), 10, 2 );

        // Application de la gratuité sur la méthode sélectionnée
        add_filter( 'woocommerce_package_rates', array( $this, 'apply_free_shipping_for_selected_methods' ), 20, 2 );
    }

    /**
     * Ajoute un champ multiselect dans l’onglet "Restrictions d’utilisation" du coupon.
     */
    public function add_coupon_shipping_method_field( $coupon_id, $coupon ) {
        if ( ! class_exists( 'WC_Shipping_Zones' ) ) {
            return;
        }

        $options = $this->get_all_shipping_method_options();
        if ( empty( $options ) ) {
            return;
        }

        $saved_methods = (array) get_post_meta( $coupon_id, '_wycan_free_shipping_methods', true );

        echo '<div class="options_group">';

        echo '<p class="form-field">';
        echo '<label for="_wycan_free_shipping_methods">' . esc_html__( 'Méthodes de livraison gratuites', 'wycan' ) . '</label>';

        echo '<select id="_wycan_free_shipping_methods" name="_wycan_free_shipping_methods[]" class="wc-enhanced-select" multiple="multiple" style="width:100%;">';

        foreach ( $options as $value => $label ) {
            $selected = in_array( $value, $saved_methods, true ) ? 'selected="selected"' : '';
            echo '<option value="' . esc_attr( $value ) . '" ' . $selected . '>' . esc_html( $label ) . '</option>';
        }

        echo '</select>';

        echo '<span class="description">' . esc_html__( 'Sélectionnez les méthodes de livraison qui deviendront gratuites lorsque ce coupon est appliqué.', 'wycan' ) . '</span>';
        echo '</p>';

        echo '</div>';
    }

    /**
     * Récupère toutes les méthodes de livraison (par zone) sous forme d’options.
     */
    protected function get_all_shipping_method_options() {
        if ( null !== $this->shipping_method_options ) {
            return $this->shipping_method_options;
        }

        $options = array();

        // Zones standards
        $zones = WC_Shipping_Zones::get_zones();
        foreach ( $zones as $zone_data ) {
            $zone      = new WC_Shipping_Zone( $zone_data['id'] );
            $zone_name = $zone->get_zone_name();

            foreach ( $zone->get_shipping_methods() as $instance_id => $method ) {
                if ( ! $method->enabled ) {
                    continue;
                }

                $rate_id = $method->id . ':' . $instance_id; // ex: flat_rate:3
                $label   = sprintf(
                    '%1$s – %2$s (%3$s)',
                    $zone_name,
                    $method->get_title(),
                    $rate_id
                );
                $options[ $rate_id ] = $label;
            }
        }

        // Zone "partout" (ID 0)
        $default_zone = new WC_Shipping_Zone( 0 );
        $zone_name    = $default_zone->get_zone_name();

        foreach ( $default_zone->get_shipping_methods() as $instance_id => $method ) {
            if ( ! $method->enabled ) {
                continue;
            }

            $rate_id = $method->id . ':' . $instance_id;
            $label   = sprintf(
                '%1$s – %2$s (%3$s)',
                $zone_name,
                $method->get_title(),
                $rate_id
            );
            $options[ $rate_id ] = $label;
        }

        $this->shipping_method_options = $options;

        return $this->shipping_method_options;
    }

    /**
     * Sauvegarde des méthodes de livraison liées au coupon.
     */
    public function save_coupon_shipping_method_field( $coupon_id, $coupon ) {
        if ( isset( $_POST['_wycan_free_shipping_methods'] ) ) {
            $methods = (array) wp_unslash( $_POST['_wycan_free_shipping_methods'] );
            $methods = array_map( 'sanitize_text_field', $methods );
            update_post_meta( $coupon_id, '_wycan_free_shipping_methods', $methods );
        } else {
            delete_post_meta( $coupon_id, '_wycan_free_shipping_methods' );
        }
    }

    /**
     * Rend la méthode de livraison gratuite si un coupon appliqué y est associé.
     */
    public function apply_free_shipping_for_selected_methods( $rates, $package ) {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            return $rates;
        }

        if ( empty( WC()->cart ) ) {
            return $rates;
        }

        $applied_coupons = WC()->cart->get_applied_coupons();
        if ( empty( $applied_coupons ) ) {
            return $rates;
        }

        $allowed_methods = array();

        // Récupère toutes les méthodes associées aux coupons appliqués
        foreach ( $applied_coupons as $code ) {
            $coupon = new WC_Coupon( $code );

            if ( ! $coupon || ! $coupon->get_id() ) {
                continue;
            }

            $methods = (array) get_post_meta( $coupon->get_id(), '_wycan_free_shipping_methods', true );
            if ( ! empty( $methods ) ) {
                $allowed_methods = array_merge( $allowed_methods, $methods );
            }
        }

        if ( empty( $allowed_methods ) ) {
            return $rates;
        }

        $allowed_methods = array_unique( array_filter( $allowed_methods ) );

        // Pour chaque méthode de livraison, si elle est dans la liste, on passe le coût à 0
        foreach ( $rates as $rate_id => $rate ) {
            if ( in_array( $rate_id, $allowed_methods, true ) ) {

                // Coût à 0
                if ( method_exists( $rate, 'set_cost' ) ) {
                    $rate->set_cost( 0 );
                } else {
                    $rates[ $rate_id ]->cost = 0;
                }

                // Taxes sur la livraison à 0
                if ( method_exists( $rate, 'get_taxes' ) && method_exists( $rate, 'set_taxes' ) ) {
                    $taxes = $rate->get_taxes();
                    if ( is_array( $taxes ) ) {
                        foreach ( $taxes as $key => $tax ) {
                            $taxes[ $key ] = 0;
                        }
                        $rate->set_taxes( $taxes );
                    }
                } elseif ( isset( $rates[ $rate_id ]->taxes ) && is_array( $rates[ $rate_id ]->taxes ) ) {
                    foreach ( $rates[ $rate_id ]->taxes as $key => $tax ) {
                        $rates[ $rate_id ]->taxes[ $key ] = 0;
                    }
                }
            }
        }

        return $rates;
    }
}

add_action( 'plugins_loaded', function() {
    load_plugin_textdomain( 'wycan', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

    if ( class_exists( 'WooCommerce' ) ) {
        new Wycan_Coupon_Shipping_Method_Free_Shipping();
    }
} );
