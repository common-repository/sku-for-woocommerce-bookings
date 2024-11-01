<?php 

/*
 Plugin Name: SKU for WooCommerce Bookings
 Plugin URI: https://profiles.wordpress.org/rynald0s
 Description: This plugin adds SKUs to your WooCommerce Bookings products. It also allows these SKUs to be searchable throughout your site.
 Author: Rynaldo Stoltz
 Author URI: http:rynaldo.com
 Version: 1.3
 License: GPLv3 or later License
 URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

add_filter('init', 'searchbysku_init', 11);

function searchbysku_init() {
  include_once 'sku-search-for-wc-bookings.php';
  include_once 'wp-filters-extra.php';
  }

/**
 * Adds the custom product tab.
 */

add_filter( 'woocommerce_product_data_tabs', 'custom_product_booking_sku_tab' );
 
function custom_product_booking_sku_tab($tabs) {
    $tabs['booking-sku'] = array(
        'label'     => __( 'Booking SKU', 'woocommerce' ),
        'target'    => 'booking_sku_options',
        'class'     => array( 'show_if_booking'  ),
    );
 
    return $tabs;
}
 
/**
 * Contents of the booking SKU options product tab.
 */

add_filter( 'woocommerce_product_data_panels', 'booking_sku_options_product_tab_content' );
 
function booking_sku_options_product_tab_content() {
    global $post;
 
    ?><div id='booking_sku_options' class='panel woocommerce_options_panel'><?php
 
        ?><div class='options_group'><?php
 
            woocommerce_wp_text_input( array(
                'id'                => '_booking_sku',
                'label'             => __( 'Booking SKU', 'woocommerce' ),
                'desc_tip'          => 'true',
                'description'       => __( 'Enter the booking SKU number.', 'woocommerce' ),
                'type'              => 'text',
            ) );
 
        ?></div>
 
    </div><?php
 
}
 
/**
 * Save the custom fields.
 */

add_action( 'woocommerce_single_product_summary', 'show_booking_sku_single_product', 40 );
 
function save_booking_sku_option_fields($post_id) {
    if ( isset( $_POST['_booking_sku'] ) ) :
        update_post_meta( $post_id, '_booking_sku', absint( $_POST['_booking_sku'] ) );
    endif;
     
}
add_action( 'woocommerce_process_product_meta_booking', 'save_booking_sku_option_fields'  );
  
function show_booking_sku_single_product() {
global $product;
 
?>
 
<div class="product_meta">
   <?php if ( $product->get_meta('_booking_sku') || $product->is_type( 'booking' ) )  : ?>
      <span class="sku_wrapper"><?php esc_html_e( 'Booking SKU:', 'woocommerce' ); ?> <span class="sku"><?php echo ( $sku = $product->get_meta('_booking_sku') ) ? $sku : esc_html__( 'N/A', 'woocommerce' ); ?></span></span>
   <?php endif; ?>
   </div>
   <?php
}
 
/**
 * Build column for product page
 */

add_filter('manage_edit-product_columns', 'add_booking_sku_column');
 
function add_booking_sku_column($columns){
    $columns['booking_sku'] = 'Booking SKU';
    return $columns;
}
 
/**
 * Populate the column in product page
 */

add_action( 'manage_posts_custom_column', 'populate_booking_sku_column' );

function populate_booking_sku_column( $column_name ){
    global $product;
  
    if( $column_name == 'booking_sku' ) {
        if ( $product->get_meta('_booking_sku') || $product->is_type( 'booking' ) )  {
        echo ( $sku = $product->get_meta('_booking_sku'));
        }
    }
}
 
/**
 * Show the Booking SKU in cart table
 */
 
add_filter( 'woocommerce_cart_item_name', 'showing_booking_sku_in_cart_items', 99, 3 );
function showing_booking_sku_in_cart_items( $item_name, $cart_item, $cart_item_key  ) {
    // The WC_Product object
    $product = $cart_item['data'];
    // Get the booking SKU
    $sku = $product->get_meta('_booking_sku');
    // If the booking sku doesn't exist
    if(empty($sku)) return $item_name;
    // Add the booking sku
    $item_name .= '<br><small class="product-sku">' . __( "Booking SKU: ", "woocommerce") . $sku . '</small>';
 
    return $item_name;
}
 
/**
 * Show the Booking SKU in order data
 */

add_action( 'woocommerce_admin_order_item_values', 'booking_sku_order_item_values', 10, 3 );
 
function booking_sku_order_item_headers( $order ) {
    echo '<th class="line_sku sortable" data-sort="your-sort-option">SKU</th>';
}
 
add_action( 'woocommerce_admin_order_item_headers', 'booking_sku_order_item_headers', 10, 1 );
 
// Add content
function booking_sku_order_item_values( $product, $item, $item_id ) {
    if ($product) { 
        $sku = $product->get_meta('_booking_sku');     
        echo '<td class="sku_wrapper">' . $sku . '</td>';                
    }
}
