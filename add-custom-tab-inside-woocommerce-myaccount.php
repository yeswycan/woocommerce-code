<?php
/*Ajout d'un onglet dans le compte client pour demander la facture - Add a new tabs inside the WooCommerce My Account */
function wycan_custom_myaccount_tabs_endpoint() {
    add_rewrite_endpoint( 'facture-client', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'wycan_custom_myaccount_tabs_endpoint' );
/* Ajout de la variable - Add your variable */
function wycan_custom_myaccount_tabs_query_vars( $vars ) {
    $vars[] = 'facture-client';
    return $vars;
}
add_filter( 'woocommerce_get_query_vars', 'wycan_custom_myaccount_tabs_query_vars', 0 );
/* Insérer le point de terminaison dans le menu du compte - Insert the endpoint in the my account menu */
function wycan_custom_myaccount_tabs_link( $items ) {
    $items['facture-client'] = 'Demander une facture'; //Le nom de votre onglet dans le menu - You tab name inside the my account menu
    return $items;
}
add_filter( 'woocommerce_account_menu_items', 'wycan_custom_myaccount_tabs_link' );
/* Ajouter le contenu à ce point de terminaison - Add your custom content to your endpoint */
function wycan_custom_myaccount_tabs_content() {
	echo '<h3>Demander une facture</h3><p>Vous avez effectué une commande sur notre site et vous souhaitez obtenir la facture de celle-ci ? Pas de soucis, il vous suffit de remplir le formulaire ci-dessous et nous reviendrons vers vous au plus vite avec la facture.</i></p>';
	echo do_shortcode( 'insérer votre shortcode ici/insert your shortcode here' ); //Mettre le shortcode que vous avez crée ou le template Elementor par exemple
}
/* Important: remplacer facture-client par ce que vous avez définir comme vars a la ligne 10 - Remplace the variable facture-client by your variable at line 10 */
add_action( 'woocommerce_account_facture-client_endpoint', 'wycan_custom_myaccount_tabs_content' );