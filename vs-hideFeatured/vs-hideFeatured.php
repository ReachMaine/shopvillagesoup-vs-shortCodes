<?php
/*
Plugin Name: VS Hide featured image on specified custom content types
Description: Never display a featured image on specified custom content types , briefs,offers,real estate
Version: 1.1
Author: TylerTork Modified by Ray St. Onge
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Modified for VillageSoup by: Ray St. Onge

This work contains some code from the plugin "Conditionally display featured image on singular pages and posts"
(https://wordpress.org/plugins/conditionally-display-featured-image-on-singular-pages/) -- a great deal has been removed
to create a minimal version that doesn't need an administrative UI, and the fallback stylesheet added.
*/


/**
 * Exit on direct call
 */
defined( 'ABSPATH' ) or die( 'Sorry' );

function tnado_hidefi_load_plugin_css() {
    $plugin_url = plugin_dir_url( __FILE__ );

  //  wp_enqueue_style( 'tnado_hidefi_styles', $plugin_url . '/tnado-styles.css' );
}
//add_action( 'wp_enqueue_scripts', 'tnado_hidefi_load_plugin_css' );

if ( ! class_exists( 'VS_HideFeatured' ) ) {

	class VS_HideFeatured {
		private $post_id; // id of post we've saved to say, report this post as having no featured image.

		public function run() {
            /**
             * Hook into loop_start because it comes after the point where the featured image might have been requested
			 * for opengraph or other metadata, where we still need the featured image URL.
             */
            add_action('loop_start', function ( $wp_query ) {
                if ( $wp_query->is_main_query() ) {
                    add_action( 'the_post', array( &$this, 'set_visibility' ) );
                } else {
	                /* secondary queries should also still be able to pull the featured image URL. */
	                if ( has_filter( 'the_post', array( &$this, 'set_visibility' ) ) ) {
		                remove_action( 'the_post', array( &$this, 'set_visibility' ) );
		                remove_filter( 'get_post_metadata', array( &$this, 'hide_featured_image' ) );
		                $this->post_id = null;
	                }
                }
            });

            /* Remove  featured image from Yoast SEO's schema.org if needed. */
            add_filter('wpseo_schema_graph_pieces', array( &$this, 'set_schema_visibility' ), 10, 2 );
            add_filter( 'twentynineteen_can_show_post_thumbnail', array( &$this, 'twentynineteen' ) );
		}

		/**
		 * Support for the twentynineteen theme
         *
         * @param bool $can_show_thumbnail
		 *
		 * @return bool
		 */
		public function twentynineteen( $can_show_thumbnail ) {
			if ( is_singular() && is_main_query()) {
				return false;
			}

			return $can_show_thumbnail;
		}
        /**
         * Hide the featured image in the Yoast SEO schema.org output.
         *
         * @param array $pieces The schema pieces.
         * @param WPSEO_Schema_Context $context An object with context variables.
         *
         * @return array
         */
        public function set_schema_visibility($pieces, $context) {
            $post_id = $context->id;

            return $this->remove_mainimage_schema_block( $pieces );
        }

        /**
         * Remove the Yoast SEO schema block that carries the main image
         *
         * @param array $pieces
         *
         * @return array
         */
        private function remove_mainimage_schema_block( $pieces ) {
            foreach($pieces as $key => $piece) {
                if ($piece instanceof WPSEO_Schema_MainImage) {
                    unset($pieces[$key]);
                    break;
                }
            }
            return $pieces;
		}

		/**
		 * Hide the featured image on all single.
		 *
		 * @param WP_Post $post as passed by the the_post action
		 */
		public function set_visibility( $post ) {
			// always hide featured image on single post or page.
			if ( is_single( $post->ID ) || is_page( $post->ID ) ) {
				$this->post_id = $post->ID; // remember which post's featured image we're hiding for this specific request.
										// there may be others requested in the course of calculating this page and we don't
										// want to hide them just because they're on a page.
        // Added this conditional around the add_filter from the original plugin
        if ($post->post_type == 'brief' || $post->post_type == 'offer' || $post->post_type == "realestate")
        {
				  add_filter( 'get_post_metadata', array( &$this, 'hide_featured_image' ), 10, 3 );
        }
			}
		}

		/**
		 * Return false to report there is no featured image, if the post ID we're asked about
		 * is the same ID we earlier flagged as associated with the single page that's being loaded.
		 *
		 * @param mixed $value given by the get_post_metadata filter
		 * @param int $object_id
		 * @param string $meta_key
		 *
		 * @return boolean
		 *
		 */
		public function hide_featured_image( $value, $object_id, $meta_key ) {
			if ( '_thumbnail_id' == $meta_key && $object_id === $this->post_id ) {
				return false;
			}
		}
	}
}

if ( ! is_admin() ) {
	add_action( 'wp_enqueue_scripts', 'tnado_hidefi_load_plugin_css' );
	$torknado_hidefeat = new VS_HideFeatured();
	$torknado_hidefeat->run();
}
