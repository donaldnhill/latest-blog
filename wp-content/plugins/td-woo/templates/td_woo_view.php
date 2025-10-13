<?php
/**
 * Template used to render woo template pages on the front end
 * - we start with the content context
 * tdb_state_template has a wp-query already, we only get in this template if a cloud template for woocommerce is set
 */
get_header();
global $wp_query;

// save the content wp_query ... to revert to it at the end of the template
tdb_state_content::set_wp_query($wp_query);

// set the global wp_query as the template one
$wp_query = tdb_state_template::get_wp_query();

// td woo template type
$td_woo_template_type = tdb_state_template::get_template_type();

// additional classes
$additional_classes = '';

global $td_woo_state_single_product_page;

switch( tdb_state_template::get_template_type() ) {

    case 'woo_product':

        // add product additional class
        $additional_classes .= 'product';

        $product_obj = $td_woo_state_single_product_page->get_product();

        // generate product structured data
        if ( WC()->structured_data ) {
            WC()->structured_data->generate_product_data($product_obj);
        }

        break;

    case 'woo_archive':
    case 'woo_search_archive':
    case 'woo_shop_base':

    // generate website structured data
    if ( WC()->structured_data ) {
        WC()->structured_data->generate_website_data();
    }

}

the_post();

// td hook before template run
do_action( 'td_before_template_content_run' );

// run the template
?>
    <div class="td-main-content-wrap td-container-wrap <?php echo $additional_classes ?>">
        <div class="tdc-content-wrap">
			<?php if ( $td_woo_template_type === 'woo_product' ) { ?>
            <div class="td-container">
	            <?php do_action( 'woocommerce_before_single_product' ); ?>
            </div>
            <?php } ?>
			<?php the_content(); ?>
	        <?php if ( $td_woo_template_type === 'woo_product' ) { ?>
                <div class="td-container">
			        <?php do_action( 'woocommerce_after_single_product' ); ?>
                </div>
	        <?php } ?>
        </div>
    </div>
<?php

$wp_query = tdb_state_content::get_wp_query();
$wp_query->rewind_posts();

if ( have_posts() ) {
	the_post();
}

get_footer();
