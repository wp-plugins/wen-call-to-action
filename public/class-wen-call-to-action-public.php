<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://wenthemes.com
 * @since      1.0.0
 *
 * @package    WEN_Call_To_Action
 * @subpackage WEN_Call_To_Action/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WEN_Call_To_Action
 * @subpackage WEN_Call_To_Action/public
 * @author     WEN Themes <info@wenthemes.com>
 */
class WEN_Call_To_Action_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WEN_Call_To_Action_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WEN_Call_To_Action_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wen-call-to-action-public.css', array(), $this->version, 'all' );

	}

  /**
   * Register custom post type.
   *
   * @since    1.0.0
   */
  public function custom_post_types(){

    // Register Call To Action Post Type
    $labels = array(
      'name'               => _x( 'Call To Actions', 'post type general name', 'wen-call-to-action' ),
      'singular_name'      => _x( 'Call To Action', 'post type singular name', 'wen-call-to-action' ),
      'menu_name'          => _x( 'Call To Action', 'admin menu', 'wen-call-to-action' ),
      'name_admin_bar'     => _x( 'Call To Action', 'add new on admin bar', 'wen-call-to-action' ),
      'add_new'            => _x( 'Add New', 'wen_cta', 'wen-call-to-action' ),
      'add_new_item'       => __( 'Add New Call To Action', 'wen-call-to-action' ),
      'new_item'           => __( 'New Call To Action', 'wen-call-to-action' ),
      'edit_item'          => __( 'Edit Call To Action', 'wen-call-to-action' ),
      'view_item'          => __( 'View Call To Action', 'wen-call-to-action' ),
      'all_items'          => __( 'All Call To Actions', 'wen-call-to-action' ),
      'search_items'       => __( 'Search Call To Actions', 'wen-call-to-action' ),
      'parent_item_colon'  => __( 'Parent Call To Actions:', 'wen-call-to-action' ),
      'not_found'          => __( 'No call to actions found.', 'wen-call-to-action' ),
      'not_found_in_trash' => __( 'No call to actions found in Trash.', 'wen-call-to-action' )
    );

    $args = array(
      'labels'             => $labels,
      'public'             => false,
      'publicly_queryable' => false,
      'show_ui'            => true,
      'show_in_menu'       => true,
      'query_var'          => false,
      'capability_type'    => 'post',
      'has_archive'        => false,
      'hierarchical'       => false,
      'menu_icon'          => 'dashicons-megaphone',
      'supports'           => array( 'title', 'editor' )
    );

    register_post_type( WEN_CALL_TO_ACTION_POST_TYPE_CTA, $args );

  }

  /**
   * Callback function of shortcode `wen_cta`.
   *
   * @since    1.0.0
   */
  public function shortcode_cb_wen_cta( $atts, $content = "" ){

    $atts = shortcode_atts( array(
        'id' => '',
    ), $atts, 'WLS' );

    $atts['id'] = absint($atts['id']);

    $is_valid_cta = $this->check_if_valid_cta( $atts );

    if ( ! $is_valid_cta ) {
      return __( 'CTA not found', 'wen-call-to-action' );
    }

    // Fetch default template
    $cta_theme = $this->get_default_cta_theme();
    $cta_theme = apply_filters( 'wen_call_to_action_filter_cta_theme', $cta_theme, $atts['id'] );

    // Bail if template is empty
    if ( empty( $cta_theme ) ) {
      return;
    }

    ob_start();

    $content_display = $this->replace_cta_placeholders( $cta_theme, $atts );
    // nspre($cta_theme);

    echo $content_display;

    $output = ob_get_contents();
    ob_end_clean();
    return $output;

  }

  /**
   * Return default CTA template.
   *
   * @since    1.0.0
   */
  function replace_cta_placeholders( $template, $args ){

    $post_id = $args['id'];
    $post_obj = get_post( $post_id );

    if ( null == $post_obj ) {
      return $template;
    }

    // Meta
    $cta_button_text = get_post_meta( $post_id, '_cta_button_text', true );
    $cta_button_url = get_post_meta( $post_id, '_cta_button_url', true );
    $cta_button_open_new_window = get_post_meta( $post_id, '_cta_button_open_new_window', true );
    $cta_custom_class = get_post_meta( $post_id, '_cta_custom_class', true );

    // Prepare classes array
    $custom_class_array = array(
      'wen-cta-wrap'
    );
    if ( ! empty( $cta_custom_class ) ) {
      $custom_class_array[] = $cta_custom_class;
    }
    $cta_custom_class = apply_filters( 'wen_call_to_action_filter_custom_class', $custom_class_array, $post_id );

    $custom_class_text = '';
    if ( ! empty( $cta_custom_class ) ) {
      array_walk( $cta_custom_class, 'sanitize_key' );
      $custom_class_text = implode( ' ', $cta_custom_class );
    }

    // Preparing search replace array
    $search_array = array();
    $replace_array = array();

    // Title
    $search_array[] = '{{title}}';
    $title_content = '';
    if ( ! empty( $post_obj->post_title ) ) {
      $title_content = '<div class="wen-cta-title">' . esc_html( $post_obj->post_title ) . '</div><!-- .wen-cta-title -->';
    }
    $replace_array[] = $title_content;

    // Description
    $search_array[] = '{{description}}';
    $description_content = '';
    if ( ! empty( $post_obj->post_content ) ) {
      $description_content = '<div class="wen-cta-content">' . apply_filters( 'the_content', $post_obj->post_content ) . '</div><!-- .wen-cta-content -->';
    }
    $replace_array[] = $description_content;

    // Button
    $search_array[] = '{{button}}';
    // Preparing button
    $button_content = '';
    if ( ! empty( $cta_button_text ) && ! empty( $cta_button_url ) ) {
      $button_content = '';
      $button_content .= '<a class="wen-cta-button" href="' . esc_url( $cta_button_url ) . '"';
      if ( 1 == $cta_button_open_new_window ) {
        $button_content .= ' target="_blank" ';
      }
      $button_content .= '>';
      $button_content .= esc_html( $cta_button_text );
      $button_content .= '</a>';
    }
    $replace_array[] = $button_content;

    // Custom class
    $search_array[] = '{{custom_class}}';
    $replace_array[] = $custom_class_text;

    // Custom ID
    $search_array[] = '{{custom_id}}';
    $replace_array[] = 'wen-cta-' . $post_obj->ID;

    $output = '';
    $output = str_replace( $search_array, $replace_array, $template );

    return $output;

  }

  /**
   * Return default CTA template.
   *
   * @since    1.0.0
   */
  function get_default_cta_theme(){

    $output = '';

    $output .= '<div id="{{custom_id}}" class="{{custom_class}}">';
      $output .= '<div class="wen-cta-inner">';
        $output .= '{{title}}';
        $output .= '{{description}}';
        $output .= '<div class="wen-cta-button-wrap">';
          $output .= '{{button}}';
        $output .= '</div><!-- .wen-cta-button-wrap -->';
      $output .= '</div><!-- .wen-cta-inner -->';
    $output .= '</div>';

    $output = apply_filters( 'wen_call_to_action_filter_default_cta_theme', $output );

    return $output;

  }

  /**
   * Add extra custom class in CTA wrap.
   *
   * @since    1.0.0
   */
  function add_extra_custom_class( $classes, $post_id ){

    $cta_theme = get_post_meta( $post_id, '_cta_theme', true );

    if ( ! empty( $cta_theme ) && 'no-style' != $cta_theme ) {

      $classes[] = 'wen-cta-template-' . esc_attr( $cta_theme );

    }

    return $classes;

  }

  /**
   * Check if given id is valid CTA.
   *
   * @since    1.0.0
   */
  private function check_if_valid_cta( $args ){

    $output = false;

    if ( isset($args['id']) && intval( $args['id'] ) > 0  ) {

      $cta = get_post( intval( $args['id'] ) );

      if ( ! empty( $cta ) && WEN_CALL_TO_ACTION_POST_TYPE_CTA == $cta->post_type ) {
        $output = true;
      }
    }
    return $output;

  }



}
