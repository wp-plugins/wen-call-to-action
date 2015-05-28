<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://wenthemes.com
 * @since      1.0.0
 *
 * @package    WEN_Call_To_Action
 * @subpackage WEN_Call_To_Action/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WEN_Call_To_Action
 * @subpackage WEN_Call_To_Action/admin
 * @author     WEN Themes <info@wenthemes.com>
 */
class WEN_Call_To_Action_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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
    $screen = get_current_screen();
    if ( WEN_CALL_TO_ACTION_POST_TYPE_CTA == $screen->id ) {
      wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wen-call-to-action-admin.css', array(), $this->version, 'all' );
    }

	}

  /**
   * Manage column head in admin listing.
   *
   * @since    1.0.0
   */
  function usage_column_head( $columns ){

    $new_columns['cb']     = '<input type="checkbox" />';
    $new_columns['title']  = $columns['title'];
    $new_columns['id']     = _x( 'ID', 'column name', 'wen-call-to-action-admin' );
    $new_columns['usage']  = __( 'Usage', 'wen-call-to-action-admin' );
    $new_columns['date']   = $columns['date'];
    return $new_columns;

  }

  /**
   * Content for extra column in admin listing.
   *
   * @since    1.0.0
   */
  function usage_column_content( $column_name, $post_id ){

    switch ( $column_name ) {
      case 'id':
        echo $post_id;
        break;

      case 'usage':
        echo '<code>[wen_cta id="' . $post_id . '"]</code>';
        break;

      default:
        break;
    }

  }

  /**
   * Hide publishing actions in edit page.
   *
   * @since    1.0.0
   */
  public function hide_publishing_actions() {
    global $post;
    if ( WEN_CALL_TO_ACTION_POST_TYPE_CTA != $post->post_type ) {
      return;
    }
    ?>
    <style type="text/css">
    #misc-publishing-actions,#minor-publishing-actions{
      display:none;
    }
    </style>
    <?php
    return;
  }

  /**
   * Customize post row actions.
   *
   * @since    1.0.0
   */
  function customize_row_actions( $actions, $post ){

    if ( WEN_CALL_TO_ACTION_POST_TYPE_CTA == $post->post_type ) {

      unset( $actions['inline hide-if-no-js'] );

    }

    return $actions;

  }

  /**
   * Add meta boxes.
   *
   * @since    1.0.0
   */
  function add_cta_meta_boxes( $post_type ){

    // Bail if not our post type
    if ( $post_type != WEN_CALL_TO_ACTION_POST_TYPE_CTA ) {
      return;
    }

    $screens = array( WEN_CALL_TO_ACTION_POST_TYPE_CTA );

    foreach ( $screens as $screen ) {

      add_meta_box(
        'wen_call_to_action_detail_content_id',
        __( 'Call To Action Info', 'wen-call-to-action' ),
        array( $this,'cta_meta_box_callback' ),
        $screen,
        'side',
        'high'
      );
      add_meta_box(
        'wen_call_to_action_usage_content_id',
        __( 'Usage', 'wen-call-to-action' ),
        array( $this, 'usage_meta_box_callback' ),
        $screen,
        'side'
      );
      add_meta_box(
        'wen_call_to_action_style_content_id',
        __( 'Call To Action Design', 'wen-call-to-action' ),
        array( $this, 'cta_design_meta_box_callback' ),
        $screen,
        'normal',
        'high'
      );

    }

  }

  /**
   * Callback for CTA design metabox.
   *
   * @since    1.0.0
   */
  function cta_design_meta_box_callback( $post ){

    $cta_custom_class = get_post_meta( $post->ID, '_cta_custom_class', true );
    $cta_theme     = get_post_meta( $post->ID, '_cta_theme', true );

    ?>

    <?php wp_nonce_field( plugin_basename( __FILE__ ), 'wen_cta_design_nonce' ); ?>

    <div id="main-cta-detail-wrap">
      <div class="field-row">
        <div class="field-label">
          <?php _e( 'Theme', 'wen-call-to-action' ); ?>
        </div><!-- .field-label -->
        <div class="field-content">
          <select name="_cta_theme">
            <option value="default"><?php _e( 'Default', 'wen-call-to-action' ); ?></option>
            <option value="no-style" <?php selected( $cta_theme, 'no-style' ); ?>><?php _e( 'No Style', 'wen-call-to-action' ); ?></option>
            <option value="blue-sky" <?php selected( $cta_theme, 'blue-sky' ); ?>><?php _e( 'Blue Sky', 'wen-call-to-action' ); ?></option>
          </select><br />
          <ul>
            <li><strong><em><?php _e( 'Default', 'wen-call-to-action' ); ?></em></strong>&nbsp;<em>:</em>&nbsp;<em><?php _e( 'Basic style will be applied.', 'wen-call-to-action' ); ?></em></li>
            <li><strong><em><?php _e( 'No Style', 'wen-call-to-action' ); ?></em></strong>&nbsp;<em>:</em>&nbsp;<em><?php _e( 'No style will be applied. Select this option if you want to add styling from your theme.', 'wen-call-to-action' ); ?></em></li>
          </ul>
        </div><!-- .field-content -->
      </div><!-- .field-row -->
      <div class="field-row">
        <div class="field-label">
          <?php _e( 'Custom Class', 'wen-call-to-action' ); ?>
        </div><!-- .field-label -->
        <div class="field-content">
          <input type="text" name="_cta_custom_class" value="<?php echo esc_attr( $cta_custom_class) ?>" />
          <br/><em><?php _e( 'This class will be added in the wrapper div of the Call To Action.', 'wen-call-to-action' ); ?></em>
        </div><!-- .field-content -->
      </div><!-- .field-row -->
    </div><!-- #main-cta-detail-wrap -->
    <?php

  }

  /**
   * Callback for CTA detail metabox.
   *
   * @since    1.0.0
   */
  function cta_meta_box_callback( $post ){

    $cta_button_text            = get_post_meta( $post->ID, '_cta_button_text', true );
    $cta_button_url             = get_post_meta( $post->ID, '_cta_button_url', true );
    $cta_button_open_new_window = get_post_meta( $post->ID, '_cta_button_open_new_window', true );
    ?>

    <?php wp_nonce_field( plugin_basename( __FILE__ ), 'wen_cta_detail_nonce' ); ?>

    <div id="main-cta-detail-wrap">
      <div class="field-row">
        <div class="field-label">
          <?php _e( 'Button Text', 'wen-call-to-action' ); ?>
        </div><!-- .field-label -->
        <div class="field-content">
          <input type="text" name="_cta_button_text" value="<?php echo esc_attr( $cta_button_text) ?>" />
          <br/><em><?php _e( 'Enter button text', 'wen-call-to-action' ); ?></em>
        </div><!-- .field-content -->
      </div><!-- .field-row -->
      <div class="field-row">
        <div class="field-label">
          <?php _e( 'Button URL', 'wen-call-to-action' ); ?>
        </div><!-- .field-label -->
        <div class="field-content">
          <input type="text" name="_cta_button_url" value="<?php echo esc_url( $cta_button_url) ?>" />
          <br/><em><?php _e( 'Enter full URL', 'wen-call-to-action' ); ?></em>
        </div><!-- .field-content -->
      </div><!-- .field-row -->
      <div class="field-row">
        <div class="field-label">
          <?php _e( 'Open in New Window', 'wen-call-to-action' ); ?>
        </div><!-- .field-label -->
        <div class="field-content">
          <input type="hidden" name="_cta_button_open_new_window" value="0" />
          <input type="checkbox" name="_cta_button_open_new_window" value="1" <?php checked( $cta_button_open_new_window, 1 ); ?> />
          <?php _e( 'Check to enable', 'wen-call-to-action' ); ?>
        </div><!-- .field-content -->
      </div><!-- .field-row -->

    </div><!-- #main-cta-detail-wrap -->
    <?php

  }

  /**
   * Save CTA detail metabox fields.
   *
   * @since    1.0.0
   */
  function save_cta_detail_meta_box( $post_id ){

    if ( WEN_CALL_TO_ACTION_POST_TYPE_CTA != get_post_type( $post_id ) ) {
      return $post_id;
    }

    // Bail if we're doing an auto save
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    // if our nonce isn't there, or we can't verify it, bail
    if ( ! isset( $_POST['wen_cta_detail_nonce'] ) || ! wp_verify_nonce( $_POST['wen_cta_detail_nonce'], plugin_basename( __FILE__ ) ) )
        return $post_id;

    // if our current user can't edit this post, bail
    if( ! current_user_can( 'edit_post' , $post_id ) )
      return $post_id;

    // get posted data
    $cta_button_text            = sanitize_text_field( $_POST['_cta_button_text'] );
    $cta_button_url             = esc_url( $_POST['_cta_button_url'] );
    $cta_button_open_new_window = esc_attr( $_POST['_cta_button_open_new_window'] );

    // save now
    update_post_meta( $post_id, '_cta_button_text', $cta_button_text );
    update_post_meta( $post_id, '_cta_button_url', $cta_button_url );
    update_post_meta( $post_id, '_cta_button_open_new_window', $cta_button_open_new_window );

    return $post_id;

  }
  /**
   * Save CTA design metabox fields.
   *
   * @since    1.0.0
   */
  function save_cta_design_meta_box( $post_id ){

    if ( WEN_CALL_TO_ACTION_POST_TYPE_CTA != get_post_type( $post_id ) ) {
      return $post_id;
    }

    // Bail if we're doing an auto save
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    // if our nonce isn't there, or we can't verify it, bail
    if ( ! isset( $_POST['wen_cta_design_nonce'] ) || ! wp_verify_nonce( $_POST['wen_cta_design_nonce'], plugin_basename( __FILE__ ) ) )
        return $post_id;

    // if our current user can't edit this post, bail
    if( ! current_user_can( 'edit_post' , $post_id ) )
      return $post_id;

    // get posted data
    $cta_custom_class = sanitize_key( $_POST['_cta_custom_class'] );
    $cta_theme     = sanitize_key( $_POST['_cta_theme'] );

    // save now
    update_post_meta( $post_id, '_cta_custom_class', $cta_custom_class );
    update_post_meta( $post_id, '_cta_theme', $cta_theme );

    return $post_id;

  }


  /**
   * Callback for usage metabox.
   *
   * @since    1.0.0
   */
  function usage_meta_box_callback( $post ){

    ?>
    <h4><?php _e( 'Shortcode', 'wen-call-to-action' ); ?></h4>
    <p><?php _e( 'Copy and paste this shortcode directly into any WordPress post or page.', 'wen-call-to-action' ); ?></p>
    <textarea class="large-text code" readonly="readonly"><?php echo '[wen_cta id="'.$post->ID.'"]'; ?></textarea>

    <h4><?php _e( 'Template Include', 'wen-call-to-action' ); ?></h4>
    <p><?php _e( 'Copy and paste this code into a template file to include the slider within your theme.', 'wen-call-to-action' ); ?></p>
    <textarea class="large-text code" readonly="readonly">&lt;?php echo do_shortcode("[wen_cta id='<?php echo $post->ID; ?>']"); ?&gt;</textarea>
    <?php

  }





}
