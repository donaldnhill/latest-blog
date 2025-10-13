<?php
/*
	Plugin Name: tagDiv Shop
	Plugin URI: https://tagdiv.com
	Description: Activate for super powers and features on your WooCommerce website.
	Author: tagDiv
	Version: 2.6 | built on 10.02.2025 10:50
	Author URI: https://tagdiv.com
*/

// hash
define( 'TD_WOO', '2852a64b55307dfd7353b7b76ce6ba63' );

// version check
require_once('td_woo_version_check.php');

// don't run anything else in the plugin, if the tagDiv Composer plugin is not active
if ( ! defined('TD_COMPOSER' ) || ! defined('TD_CLOUD_LIBRARY'  ) ) {

	if ( ! defined('TD_COMPOSER'  ) && ! defined('TD_CLOUD_LIBRARY'  ) ) { // both
		add_action( 'admin_notices', function (){
            $td_brand = (defined('TD_COMPOSER') && class_exists( 'td_util' )) ? td_util::get_wl_val('tds_wl_brand', 'tagDiv') : 'tagDiv';
            ?>
			<div class="notice notice-error is-dismissible td-plugins-deactivated-notice">
                <p style="">The <b><?php echo $td_brand ?> Shop</b> plugin requires both <b><?php echo $td_brand ?> Composer</b> and <b><?php echo $td_brand ?> Cloud Library</b> plugin!
				<br>Please check the theme plugins section to <em>update/install/activate</em> theme plugins.</p>
				<p><a class="" href="admin.php?page=td_theme_plugins">Go to Theme Plugins</a></p>
			</div>
			<?php
		});
	} elseif ( ! defined('TD_CLOUD_LIBRARY'  ) ) { // no cloud
		add_action( 'admin_notices', function (){
            $td_brand = (defined('TD_COMPOSER') && class_exists( 'td_util' )) ? td_util::get_wl_val('tds_wl_brand', 'tagDiv') : 'tagDiv';
            ?>
			<div class="notice notice-error is-dismissible td-plugins-deactivated-notice">
                <p style="">The <b><?php echo $td_brand ?> Shop</b> plugin requires the <b><?php echo $td_brand ?> Cloud Library</b> plugin!
                    <br>Please check the theme plugins section to <em>update/install/activate</em> theme plugins.</p>
				<p><a class="" href="admin.php?page=td_theme_plugins">Go to Theme Plugins</a></p>
			</div>
			<?php
		});
	} else {
		add_action( 'admin_notices', function (){ // no composer
            $td_brand = (defined('TD_COMPOSER') && class_exists( 'td_util' )) ? td_util::get_wl_val('tds_wl_brand', 'tagDiv') : 'tagDiv';
            ?>
			<div class="notice notice-error is-dismissible td-plugins-deactivated-notice">
                <p style="">The <b><?php echo $td_brand ?> Shop</b> plugin requires the <b><?php echo $td_brand ?> Composer</b> plugin!
                    <br>Please check the theme plugins section to <em>update/install/activate</em> theme plugins.</p>
				<p><a class="" href="admin.php?page=td_theme_plugins">Go to Theme Plugins</a></p>
			</div>
			<?php
		});
	}

	return;
}

// WooCommerce plugin check
if ( !in_array('woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	add_action( 'admin_notices', function (){
		?>
        <div class="notice notice-error is-dismissible td-plugins-deactivated-notice">
            <h3><?php echo td_util::get_wl_val('tds_wl_brand', 'tagDiv')?> Shop Notice</h3>
            <p>The <b><?php echo td_util::get_wl_val('tds_wl_brand', 'tagDiv')?> Shop</b> plugin requires <a href="https://wordpress.org/plugins/woocommerce/" target="_blank"><b>WooCommerce</b></a> plugin to be installed and active! <br>Please install/activate <b>WooCommerce</b> plugin.</p>
        </div>
		<?php
	});

	return;
}


// the deploy mode: dev or deploy - it's set to deploy automatically on deploy
define("TD_WOO_DEPLOY_MODE", 'deploy');
define("TD_WOO_USE_LESS", false);

define('TD_WOO_DIR', dirname( __FILE__ ));
define('TD_WOO_URL', plugins_url('td-woo'));

define('TD_WOO_SCRIPTS_URL', TD_WOO_URL . '/assets/js');
define('TD_WOO_SCRIPTS_VER', '?ver=' . TD_WOO);

add_action( 'td_global_after', 'td_woo_td_global_after');
function td_woo_td_global_after() {

	// check active theme and automatically disable the plugin if the active theme doesn't support it
	if ( td_woo_version_check::is_active_theme_compatible() === false ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
	}

	// check PHP version
    //if ( td_woo_version_check::is_php_compatible() === false ) {
    //    return;
    //}

	// check theme version
	if ( td_woo_version_check::is_theme_version_compatible() === false ) {
		return;
	}

	add_action('tdc_init', 'td_woo_on_init');
	function td_woo_on_init() {
		require_once( 'includes/td_woo_functions.php' );
	}

	// panel settings
	if ( is_admin() &&
	     array_key_exists('theme_panel', td_global::$all_theme_panels_list ) &&
	     array_key_exists('panels', td_global::$all_theme_panels_list['theme_panel'] )
	) {

        $separator_panel = 'td-panel-separator-plugin';

        if ( ! in_array( $separator_panel, td_global::$all_theme_panels_list['theme_panel']['panels'] ) ) {
            td_global::$all_theme_panels_list['theme_panel']['panels'][$separator_panel] = array(
                'text' => 'PLUGINS\' SETTINGS',
                'type' => 'separator',
            );
        }

		td_global::$all_theme_panels_list['theme_panel']['panels']['td-woo-plugin'] = array(
			'text' => 'SHOP',
			'ico_class' => 'td-ico-multi',
			'file' => TD_WOO_DIR . '/includes/panel/td_panel_settings.php',
			'type' => 'in_theme',
		);
	}
}

add_action( 'td_wp_booster_loaded', 'tdc_plugin_init2' );
function tdc_plugin_init2() {
    // remove redirect and ajax from checkout page to cart page when in composer
    if ( td_util::tdc_is_live_editor_iframe() || td_util::tdc_is_live_editor_ajax() ) {
        add_filter( 'woocommerce_checkout_redirect_empty_cart', '__return_false' );
        function disable_woo_checkout_script(){
            wp_dequeue_script( 'wc-checkout' );
        }
        add_action( 'wp_enqueue_scripts', 'disable_woo_checkout_script' );
   }
}

add_action( 'tdw_menu_login_data', function() {

    $output = '<ul class="tdw-wml-menu-list">';
        $output .= '<li><a class="' . wc_get_account_menu_item_classes( 'dashboard' ) . '" href="' . get_permalink( wc_get_page_id( 'myaccount' ) ) . '">' . __td('My woo account', TD_THEME_NAME) . '</a></li>';
        $output .= '<li><a class="' . wc_get_account_menu_item_classes( 'orders' ) . '" href="' . wc_get_account_endpoint_url( get_option( 'woocommerce_myaccount_orders_endpoint', 'orders' ) ) . '">' . __td('Orders', TD_THEME_NAME) . '</a></li>';
		$output .= '<li><a class="' . wc_get_account_menu_item_classes( 'donwloads' ) . '" href="' . wc_get_account_endpoint_url( get_option( 'woocommerce_myaccount_downloads_endpoint', 'downloads' ) ) . '">' . __td('Downloads', TD_THEME_NAME) . '</a></li>';
        $output .= '<li><a class="' . wc_get_account_menu_item_classes( 'edit-address' ) . '" href="' . wc_get_account_endpoint_url( get_option( 'woocommerce_myaccount_edit_address_endpoint', 'edit-address' ) ) . '">' . __td('Addresses', TD_THEME_NAME) . '</a></li>';
        $output .= '<li><a class="' . wc_get_account_menu_item_classes( 'edit-account' ) . '" href="' . wc_get_account_endpoint_url( get_option( 'woocommerce_myaccount_edit_account_endpoint', 'edit-account' ) ) . '">' . __td('Account settings', TD_THEME_NAME) . '</a></li>';
    $output .= '</ul>';

    echo $output;
});
