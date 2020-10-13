<?php
/**
 * Product Breadcrumb
 *
 * @package           ProductBreadcrumb
 * @author            FixRunner
 * @license           GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Product Breadcrumb
 * Plugin URI:        https://example.com/plugin-name
 * Description:       Showing custom products breadcrumb on your website. Everything is set automatically, you don't need to do anything.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            FixRunner, dev: Marko R.
 * Author URI:        https://example.com
 * Text Domain:       product-breadcrumb
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( ! class_exists( 'ProductBreadcrumb' ) ) {

	/**
	 * Class ProductBreadcrumb
	 *
	 * @package ProductBreadcrumb
	 * @version 1.0.0
	 * @since 1.0.0
	 * @author Fixrunner, dev: Marko R.
	 */
	class ProductBreadcrumb {

		public function __construct() {
			// Trigger only on non-admin parts
			if ( ! is_admin() ) {
				add_action( 'wp_footer', array( $this, 'pb_show_breadcrumb' ) );
				add_action( 'wp_enqueue_scripts', array( $this, 'pb_enqueue_scripts_single' ) );
				add_action( 'wp_enqueue_scripts', array( $this, 'pb_enqueue_scripts_non_single' ) );
			}
		}

		/**
		 * Place and register all scripts and styles which
		 * will be execute only on non single products page
		 */
		public function pb_enqueue_scripts_non_single() {
			if ( ! is_product() ) {
				wp_enqueue_style(
					'pb_public_css_non_single',
					plugins_url( '/assets/css/pb_public_non_single.css', __FILE__ )
				);

				wp_register_script( 'pb_jquery_public_non_single',
					plugins_url( '/assets/js/pb_jquery_public_non_single.js', __FILE__ ),
					array( 'jquery' )
				);

				wp_enqueue_script( 'pb_jquery_public_non_single',
					plugins_url( '/assets/js/pb_jquery_public_non_single.js', __FILE__ ),
					array( 'jquery' )
				);
			}
		}

		/**
		 * Place and register all scripts and styles which
		 * will be execute only on single products page
		 *
		 * @since 1.0.0
		 */
		public function pb_enqueue_scripts_single() {
			if ( is_product() ) {
				wp_enqueue_style(
					'pb_public_css_single',
					plugins_url( '/assets/css/pb_public_single.css', __FILE__ )
				);

				wp_register_script( 'pb_jquery_public_single',
					plugins_url( '/assets/js/pb_jquery_public_single.js', __FILE__ ),
					array( 'jquery' )
				);

				wp_enqueue_script( 'pb_jquery_public_single',
					plugins_url( '/assets/js/pb_jquery_public_single.js', __FILE__ ),
					array( 'jquery' )
				);
			}
		}

		/**
		 * Getting the domain name
		 *
		 * @since 1.0.0
		 * @return mixed
		 */
		private function pb_get_domain() {
			return '<a href="' . get_site_url() . '">' . ucfirst( $_SERVER[ 'HTTP_HOST' ] ) . '</a>';
		}

		/**
		 * Path for the breadcrumb
		 *
		 * @since 1.0.0
		 */
		private function pb_breadcrumb_path() {
			echo '<p id="pb_product_breadcrumb">' .
				$this->pb_get_domain() .
		        $this->pb_category_loop() .
		        $this->pb_product_name() .
			'</p>';
		}

		/**
		 * Loop through the product categories
		 *
		 * @since 1.0.0
		 * @return string
		 */
		private function pb_category_loop() {
			global $post;
			$pb_category_names = get_the_terms( $post->ID, 'product_cat' );

			foreach ( $pb_category_names as $pb_category_name ) {
				$pb_category = $pb_category_name->name;
				break;
			}
			return ' &gt; <a href="' . get_permalink( $pb_category ) . '">' . $pb_category . '</a>';
		}

		/**
		 * Loop through the products and
		 * getting the name of it
		 *
		 * @since 1.0.0
		 * @return string
		 */
		private function pb_product_name() {
			if ( is_product() ) {
				global $product;

				if ( ! is_a( $product, 'WC_Product' ) ) {
					$product = wc_get_product( get_the_id() );
				}
				return ' &gt; <a href="' . get_permalink( $product->slug ) . '">'. $product->get_name() . '</a>';
			}
		}

		/**
		 * Checking if is the specific page
		 *
		 * @since 1.0.0
		 */
		private function pb_trigger_on_specific_page() {
			if( is_product() || is_product_category() ) {
				$this->pb_breadcrumb_path();
			}
		}

		/**
		 * Pull all necessary functionalities
		 * and trigger the breadcrumb
		 *
		 * @since 1.0.0
		 */
		public function pb_show_breadcrumb() {
			$this->pb_trigger_on_specific_page();
		}
	}

	new ProductBreadcrumb();
}