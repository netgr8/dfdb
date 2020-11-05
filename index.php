<?php

/**
 * Plugin Name:       Direct Free Downloads Button
 * Plugin URI:        https://netgr8.com/dfdb.html
 * Description:       Create a button for free downloadable WooCommerce content. You can use this shortcode to place this button in the product description or on the product page.
 * Version:           1.0
 * Requires at least: 5.1
 * Requires PHP:      7.2
 * Author:            Dropka Ádám - NetGreat
 * Author URI:        https://netgr8.com/
 * License:           GPL v3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 */

// Admin menü kiegészítése saját gombbal
add_action('admin_menu', 'netgreatpuginsdfdb');

// Saját menü gombok létrehozása
function netgreatpuginsdfdb() {
    add_menu_page( 'Direct Free Downloads Button', 'DFDB', 'read', 'netgreat_plugins', 'netgreat_plugins', plugins_url('img\logo_white.png',__FILE__), 61 );
};

//Stíluslap hozzáadása - BOOTSTRAP CSS
function adambootstrapcssadd() {
    wp_register_style('adambootstrapcssadd', plugins_url('css\bootstrap.min.css',__FILE__ ));
    wp_enqueue_style('adambootstrapcssadd');
};

add_action( 'admin_init','adambootstrapcssadd');

//Képek változókkal
$logo100 = plugins_url('img\logo-100.png',__FILE__);

// Saját oldal tartalma
function netgreat_plugins() {
    include 'inc/bemutatkozo.php';
};

// Direct Free Downloads Button
function direct_free_downloads_button( $button )
{
    global $product;

    if( $product->is_downloadable() AND $product->get_price() == 0)
    {
        $files = $product->get_files();
        $files = array_keys($files);

        $download_url = home_url('?download_file='.$product->id.'&key='.$files[0].'&free=1' );

        $button = sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="button %s product_type_%s">%s</a>',
            esc_url( $download_url  ),
            esc_attr( $product->id ),
            esc_attr( $product->get_sku() ),
            esc_attr( isset( $quantity ) ? $quantity : 1 ),
            $product->is_purchasable() && $product->is_in_stock() ? '' : '',
            esc_attr( $product->product_type ),
            esc_html( 'Download' )
        );
    }
    return $button;
}
add_filter('woocommerce_loop_add_to_cart_link', 'direct_free_downloads_button', 100);

/**
 * Handles downloading of free Downloadable products
 * @return [type] [description]
 */
function netgreatfreedownloadbuttonproductfile()
{
    $product_id    = absint( $_GET['download_file'] );
    $_product      = wc_get_product( $product_id );

    if( $_product->get_price() == 0 ) {
        WC_Download_Handler::download( $_product->get_file_download_path( filter_var($_GET['key'], FILTER_SANITIZE_STRING)  ), $product_id );
    }
}

if ( isset( $_GET['download_file'] ) && isset( $_GET['key'] ) && $_GET['free'] ) {
    add_action( 'init', 'netgreatfreedownloadbuttonproductfile' );
}

//SAJAT WOOCOMMERCE LETOLTES BUTTON
// function that runs when shortcode is called
function netgreatfreedownloadshortcode() { 

    global $product;
    if( $product->is_downloadable() && $product->get_price() == 0 )
    {
        $id = $product->get_id();
        $sku = $product->get_sku();
        $files = $product->get_files();
        $files = array_keys($files);
        $download_url = home_url('?download_file='.$product->id.'&key='.$files[0].'&free=1' );
        return ('<a href="' . $download_url . '" rel="nofollow" data-product_id="' . $id . '" data-product_sku="' . $sku . '" data-quantity="1" class="button" style="margin-bottom:15px;">Download</a>');
    }
}
// register shortcode: woofreedownload
add_shortcode('woofreedownload', 'netgreatfreedownloadshortcode');