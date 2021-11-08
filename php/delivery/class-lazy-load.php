<?php
/**
 * Lazy Load.
 *
 * @package Cloudinary
 */

namespace Cloudinary\Delivery;

use Cloudinary\Connect\Api;
use Cloudinary\Delivery_Feature;
use Cloudinary\Plugin;
use Cloudinary\UI\Component\HTML;

/**
 * Class Responsive_Breakpoints
 *
 * @package Cloudinary
 */
class Lazy_Load extends Delivery_Feature {

	/**
	 * Holds the settings slug.
	 *
	 * @var string
	 */
	protected $settings_slug = 'media_display';

	/**
	 * Holds the enabler slug.
	 *
	 * @var string
	 */
	protected $enable_slug = 'use_lazy_load';

	/**
	 * Lazy_Load constructor.
	 *
	 * @param \Cloudinary\Plugin $plugin The main instance of the plugin.
	 */
	public function __construct( Plugin $plugin ) {
		parent::__construct( $plugin );
		add_filter( 'cloudinary_image_tag-disabled', array( $this, 'js_noscript' ), 10, 2 );
	}

	/**
	 * Wrap image tags in noscript to allow no-javascript browsers to get images.
	 *
	 * @param string $tag         The original html tag.
	 * @param array  $tag_element The original tag_element.
	 *
	 * @return string
	 */
	public function js_noscript( $tag, $tag_element ) {

		$options          = $tag_element['atts'];
		$options['class'] = implode( ' ', $options['class'] );

		unset(
			$options['srcset'],
			$options['sizes'],
			$options['loading'],
			$options['src'],
			$options['class']
		);
		$atts = array(
			'data-image' => wp_json_encode( $options ),
		);

		return HTML::build_tag( 'noscript', $atts ) . $tag . HTML::build_tag( 'noscript', null, 'close' );

	}

	/**
	 * Get the placeholder generation transformations.
	 *
	 * @param string $placeholder The placeholder to get.
	 *
	 * @return array
	 */
	public function get_placeholder_transformations( $placeholder ) {

		$transformations = array(
			'predominant' => array(
				array(
					'$currWidth'  => 'w',
					'$currHeight' => 'h',
				),
				array(
					'width'        => 'iw_div_2',
					'aspect_ratio' => 1,
					'crop'         => 'pad',
					'background'   => 'auto',
				),
				array(
					'crop'    => 'crop',
					'width'   => 10,
					'height'  => 10,
					'gravity' => 'north_east',
				),
				array(
					'width'  => '$currWidth',
					'height' => '$currHeight',
					'crop'   => 'fill',
				),
				array(
					'fetch_format' => 'auto',
					'quality'      => 'auto',
				),
			),
			'vectorize'   => array(
				array(
					'effect'       => 'vectorize:3:0.1',
					'fetch_format' => 'svg',
				),
			),
			'blur'        => array(
				array(
					'effect'       => 'blur:2000',
					'quality'      => 1,
					'fetch_format' => 'auto',
				),
			),
			'pixelate'    => array(
				array(
					'effect'       => 'pixelate',
					'quality'      => 1,
					'fetch_format' => 'auto',
				),
			),
		);

		return $transformations[ $placeholder ];
	}

	/**
	 * Add features to a tag element set.
	 *
	 * @param array $tag_element The tag element set.
	 *
	 * @return array
	 */
	public function add_features( $tag_element ) {

		$sizes = array(
			$tag_element['atts']['width'],
			$tag_element['atts']['height'],
		);

		$transformations                             = $this->media->get_transformations_from_string( $tag_element['atts']['src'] );
		$crop_size                                   = array_shift( $transformations ); // We always get a sized url, the first will be the size, which we don't need.
		$tag_element['atts']['data-transformations'] = API::generate_transformation_string( $transformations, $tag_element['type'] );
		if ( true === $tag_element['crop'] ) {
			unset( $crop_size['width'], $crop_size['height'] );
			$sizes[] = Api::generate_transformation_string( array( $crop_size ) );
		}

		// Capture the size.
		$tag_element['atts']['data-size'] = array_filter( $sizes );

		$colors    = $this->config['lazy_custom_color'];
		$animation = array(
			$colors,
		);
		if ( 'on' === $this->config['lazy_animate'] ) {
			preg_match_all( '/(\d\.*)+/', $colors, $matched );
			$fade        = $matched[0];
			$fade[3]     = 0.1;
			$fade        = 'rgba(' . implode( ',', $fade ) . ')';
			$animation[] = $fade;
			$animation[] = $colors;
		}
		$colors = implode( ';', $animation );

		// Add svg placeholder.
		$svg                        = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $tag_element['atts']['width'] . '" height="' . $tag_element['atts']['height'] . '"><rect width="100%" height="100%"><animate attributeName="fill" values="' . $colors . '" dur="2s" repeatCount="indefinite" /></rect></svg>';
		$tag_element['atts']['src'] = 'data:image/svg+xml;utf8,' . $svg;
		if ( isset( $tag_element['atts']['srcset'] ) ) {
			$tag_element['atts']['data-srcset'] = $tag_element['atts']['srcset'];
			$tag_element['atts']['data-sizes']  = $tag_element['atts']['sizes'];
			unset( $tag_element['atts']['srcset'], $tag_element['atts']['sizes'] );
		}

		return $tag_element;

	}

	/**
	 * Register front end hooks.
	 */
	public function register_assets() {
		wp_register_script( 'cld-lazy-load', $this->plugin->dir_url . 'js/lazy-load.js', null, $this->plugin->version, false );
	}

	/**
	 * Apply front end filters on the enqueue_assets hook.
	 */
	public function enqueue_assets() {
		wp_enqueue_script( 'cld-lazy-load' );
		$config = $this->config; // Get top most config.

		if ( 'off' !== $config['lazy_placeholder'] ) {
			$config['placeholder'] = API::generate_transformation_string( $this->get_placeholder_transformations( $config['lazy_placeholder'] ) );
		}
		$config['base_url'] = $this->media->base_url;
		wp_add_inline_script( 'cld-lazy-load', 'var CLDLB = ' . wp_json_encode( $config ), 'before' );
	}

	/**
	 * Check if component is active.
	 *
	 * @return bool
	 */
	public function is_active() {

		return ! is_admin();
	}

	/**
	 * Add the settings.
	 *
	 * @param array $pages The pages to add to.
	 *
	 * @return array
	 */
	public function register_settings( $pages ) {

		$pages['lazy_loading'] = array(
			'page_title'          => __( 'Lazy loading', 'cloudinary' ),
			'menu_title'          => __( 'Lazy loading', 'cloudinary' ),
			'priority'            => 5,
			'requires_connection' => true,
			'sidebar'             => true,
			'settings'            => array(
				array(
					'type'        => 'panel',
					'title'       => __( 'Lazy Loading', 'cloudinary' ),
					'priority'    => 9,
					'option_name' => 'media_display',
					array(
						'type' => 'tabs',
						'tabs' => array(
							'image_setting' => array(
								'text' => __( 'Settings', 'cloudinary' ),
								'id'   => 'settings',
							),
							'image_preview' => array(
								'text' => __( 'Preview', 'cloudinary' ),
								'id'   => 'preview',
							),
						),
					),
					array(
						'type' => 'row',
						array(
							'type'   => 'column',
							'tab_id' => 'settings',
							array(
								'type'               => 'on_off',
								'description'        => __( 'Enable lazy loading', 'cloudinary' ),
								'tooltip_text'       => __( 'Lazy loading delays the initialization of your web assets to improve page load times.', 'cloudinary' ),
								'optimisation_title' => __( 'Lazy loading', 'cloudinary' ),
								'slug'               => 'use_lazy_load',
								'default'            => 'on',
							),
							array(
								'type'      => 'group',
								'condition' => array(
									'use_lazy_load' => true,
								),
								array(
									'type'         => 'text',
									'title'        => __( 'Lazy loading threshold', 'cloudinary' ),
									'tooltip_text' => __( 'How far down the page to start lazy loading assets.', 'cloudinary' ),
									'slug'         => 'lazy_threshold',
									'attributes'   => array(
										'style'            => array(
											'width:100px;display:block;',
										),
										'data-auto-suffix' => '*px;em;rem;vh',
									),
									'default'      => '100px',
								),
								array(
									'type'    => 'tag',
									'element' => 'hr',
								),
								array(
									'type'        => 'color',
									'title'       => __( 'Pre-loader color', 'cloudinary' ),
									'description' => __(
										'On page load, the pre-loader is used to fill the space while the image is downloaded, preventing content shift.',
										'cloudinary'
									),
									'slug'        => 'lazy_custom_color',
									'default'     => 'rgba(153,153,153,0.5)',
								),
								array(
									'type'        => 'on_off',
									'description' => __( 'Pre-loader animation', 'cloudinary' ),
									'slug'        => 'lazy_animate',
									'default'     => 'on',
								),
								array(
									'type'    => 'tag',
									'element' => 'hr',
								),
								array(
									'type'        => 'radio',
									'title'       => __( 'Placeholder generation type', 'cloudinary' ),
									'description' => __(
										"Placeholders are low-res representations of the image, that's loaded below the fold. They are then replaced with the actual image, just before it comes into view.",
										'cloudinary'
									),
									'slug'        => 'lazy_placeholder',
									'default'     => 'blur',
									'condition'   => array(
										'use_lazy_load' => true,
									),
									'options'     => array(
										'blur'        => __( 'Blur', 'cloudinary' ),
										'pixelate'    => __( 'Pixelate', 'cloudinary' ),
										'vectorize'   => __( 'Vectorize', 'cloudinary' ),
										'predominant' => __( 'Dominant Color', 'cloudinary' ),
										'off'         => __( 'Off', 'cloudinary' ),
									),
								),
							),
						),
						array(
							'type'      => 'column',
							'tab_id'    => 'preview',
							'class'     => array(
								'cld-ui-preview',
							),
							'condition' => array(
								'use_lazy_load' => true,
							),
							array(
								'type'    => 'lazyload_preview',
								'title'   => __( 'Preview', 'cloudinary' ),
								'slug'    => 'lazyload_preview',
								'default' => CLOUDINARY_ENDPOINTS_PREVIEW_IMAGE . 'w_600/sample.jpg',
							),
						),
					),

				),
			),
		);

		return $pages;
	}
}
