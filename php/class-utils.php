<?php
/**
 * Utilities for Cloudinary.
 *
 * @package Cloudinary
 */

namespace Cloudinary;

use Cloudinary\Settings\Setting;
use Google\Web_Stories\Story_Post_Type;

/**
 * Class that includes utility methods.
 *
 * @package Cloudinary
 */
class Utils {

	/**
	 * Filter an array recursively
	 *
	 * @param array         $input    The array to filter.
	 * @param callable|null $callback The callback to run for filtering.
	 *
	 * @return array
	 */
	public static function array_filter_recursive( array $input, $callback = null ) {
		// PHP array_filter does this, so we'll do it too.
		if ( null === $callback ) {
			$callback = static function ( $item ) {
				return ! empty( $item );
			};
		}

		foreach ( $input as &$value ) {
			if ( is_array( $value ) ) {
				$value = self::array_filter_recursive( $value, $callback );
			}
		}

		return array_filter( $input, $callback );
	}

	/**
	 * Gets the active child setting.
	 *
	 * @return Setting
	 */
	public static function get_active_setting() {
		$settings = get_plugin_instance()->settings;
		$active   = null;
		if ( $settings->has_param( 'active_setting' ) ) {
			$active = $settings->get_setting( $settings->get_param( 'active_setting' ) );
		}

		return $active;
	}

	/**
	 * Detects array keys with dot notation and expands them to form a new multi-dimensional array.
	 *
	 * @param array  $input     The array that will be processed.
	 * @param string $separator Separator string.
	 *
	 * @return array
	 */
	public static function expand_dot_notation( array $input, $separator = '.' ) {
		$result = array();
		foreach ( $input as $key => $value ) {
			if ( is_array( $value ) ) {
				$value = self::expand_dot_notation( $value );
			}

			foreach ( array_reverse( explode( $separator, $key ) ) as $inner_key ) {
				$value = array( $inner_key => $value );
			}

			// phpcs:ignore
			/** @noinspection SlowArrayOperationsInLoopInspection */
			$result = array_merge_recursive( $result, $value );
		}

		return $result;
	}

	/**
	 * Check whether the inputted HTML string is powered by AMP, or if the request is an amp page.
	 * Reference on how to detect an AMP page: https://amp.dev/documentation/guides-and-tutorials/learn/spec/amphtml/?format=websites#ampd.
	 *
	 * @param string|null $html_string Optional: The specific HTML string to check.
	 *
	 * @return bool
	 */
	public static function is_amp( $html_string = null ) {
		if ( ! empty( $html_string ) ) {
			return preg_match( '/<html.+(amp|⚡)+[^>]/', substr( $html_string, 0, 200 ), $found );
		}
		$is_amp = false;
		if ( function_exists( 'amp_is_request' ) ) {
			$is_amp = amp_is_request();
		}

		return $is_amp;
	}

	/**
	 * Check whether the inputted post type is a webstory.
	 *
	 * @param string $post_type The post type to compare to.
	 *
	 * @return bool
	 */
	public static function is_webstory_post_type( $post_type ) {
		return class_exists( Story_Post_Type::class ) && Story_Post_Type::POST_TYPE_SLUG === $post_type;
	}

	/**
	 * Get all the attributes from an HTML tag.
	 *
	 * @param string $tag HTML tag to get attributes from.
	 *
	 * @return array
	 */
	public static function get_tag_attributes( $tag ) {
		$tag    = strstr( $tag, ' ', false );
		$tag    = trim( $tag, '> ' );
		$args   = shortcode_parse_atts( $tag );
		$return = array();
		foreach ( $args as $key => $value ) {
			if ( is_int( $key ) ) {
				$return[ $value ] = 'true';
				continue;
			}
			$return[ $key ] = $value;
		}

		return $return;
	}

	/**
	 * Get the depth of an array.
	 *
	 * @param array $array The array to check.
	 *
	 * @return int
	 */
	public static function array_depth( array $array ) {
		$depth = 0;

		foreach ( $array as $value ) {
			if ( is_array( $value ) ) {
				$level = self::array_depth( $value ) + 1;

				if ( $level > $depth ) {
					$depth = $level;
				}
			}
		}

		return $depth;
	}

	/**
	 * Check if the current user can perform a task.
	 *
	 * @param string $task The task to check.
	 *
	 * @return bool
	 */
	public static function user_can( $task ) {

		/**
		 * Filter the capability required for a specific cloudinary task.
		 *
		 * @hook    cloudinary_task_capability_{task}
		 * @since   2.7.6
		 *
		 * @param $capability {string} The capability.
		 *
		 * @default 'manage_options'
		 * @return  {string}
		 */
		$capability = apply_filters( "cloudinary_task_capability_{$task}", 'manage_options' );

		/**
		 * Filter the capability required for cloudinary tasks.
		 *
		 * @hook    cloudinary_task_capability
		 * @since   2.7.6
		 *
		 * @param $capability {string} The current capability for the task.
		 * @param $task       {string} The task.
		 *
		 * @return  {string}
		 */
		$capability = apply_filters( 'cloudinary_task_capability', $capability, $task );

		return current_user_can( $capability );
	}

	/**
	 * Get the Cloudinary relationships table name.
	 *
	 * @return string
	 */
	public static function get_relationship_table() {
		global $wpdb;

		return $wpdb->prefix . 'cloudinary_relationships';
	}

	/**
	 * Install our custom table.
	 */
	public static function install() {
		global $wpdb;

		$table_name      = self::get_relationship_table();
		$charset_collate = $wpdb->get_charset_collate();
		// Setup the sql.
		$sql = "CREATE TABLE $table_name (
	  id int(11) unsigned NOT NULL AUTO_INCREMENT,
	  post_id int(11) DEFAULT NULL,
	  public_id varchar(255) DEFAULT NULL,
	  parent_path varchar(255) DEFAULT NULL,
	  primary_url varchar(255) DEFAULT NULL,
	  sized_url varchar(255) DEFAULT NULL,
	  width int(11) DEFAULT NULL,
	  height int(11) DEFAULT NULL,
	  format varchar(12) DEFAULT NULL,
	  sync_type varchar(45) DEFAULT NULL,
	  post_state varchar(12) DEFAULT NULL,
	  transformations text DEFAULT NULL,
	  PRIMARY KEY (id),
	  UNIQUE KEY sized_url (sized_url),
	  KEY post_id (post_id),
	  KEY parent_path (parent_path),
	  KEY public_id (public_id),
	  KEY sync_type (sync_type)
	) $charset_collate";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		// @todo: get VIP approval.
		dbDelta( $sql ); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.dbDelta_dbdelta
	}

	/**
	 * Gets the URL for opening a Support Request.
	 *
	 * @param string $reason  The reason option slug.
	 * @param string $subject The subject for the request.
	 *
	 * @return string
	 */
	public static function get_support_link( $reason = '', $subject = '' ) {
		$user   = wp_get_current_user();
		$plugin = get_plugin_instance();
		$url    = 'https://support.cloudinary.com/hc/en-us/requests/new';

		if ( empty( $reason ) ) {
			$reason = 'other_help_needed';
		}

		if ( empty( $subject ) ) {
			$subject = sprintf(
				// translators: The plugin version.
				__( 'I need help with Cloudinary WordPress plugin version %s', 'cloudinary' ),
				$plugin->version
			);
		}

		$args = array(
			'request_anonymous_requester_email'  => $user->display_name,
			'request_custom_fields_22246877'     => $user->user_email,
			'request_custom_fields_360007219560' => $plugin->components['connect']->get_cloud_name(),
			'request_custom_fields_360017815680' => $reason,
			'request_subject'                    => $subject,
			'request_description'                => __( 'Please, provide more details on your request, and if possible, attach a System Report', 'cloudinary' ),
		);

		return add_query_arg( array_filter( $args ), $url );
	}
}
