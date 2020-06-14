<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://formality.dev
 * @since      1.0.0
 *
 * @package    Formality
 * @subpackage Formality/admin
 */

class Formality_Admin {

  private $formality;
  private $version;

  /**
   * Initialize the class and set its properties.
   *
   * @since    1.0.0
   * @param      string    $formality       The name of the plugin.
   * @param      string    $version    The version of this plugin.
   */
  public function __construct( $formality, $version ) {
    $this->formality = $formality;
    $this->version = $version;
    $this->load_dependencies();
  }

  private function load_dependencies() {
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-formality-results.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-formality-notifications.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-formality-tools.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-formality-editor.php';
  }
  
  public function enqueue_styles() {
    wp_enqueue_style( $this->formality . "-admin", plugin_dir_url(__DIR__) . 'dist/styles/formality-admin.css', array(), $this->version, 'all' );
  }

  public function enqueue_scripts() {
    wp_enqueue_script( $this->formality . "-admin", plugin_dir_url(__DIR__) . 'dist/scripts/formality-admin.js', array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wp-plugins', 'wp-edit-post' ), $this->version, false );
    
    wp_localize_script( $this->formality . "-admin", 'formality', array(
      'plugin_url' => str_replace('admin/', '', plugin_dir_url( __FILE__ )),
      'admin_url' => get_admin_url()
    ));

    wp_set_script_translations( $this->formality . "-admin", 'formality', plugin_dir_path( __DIR__ ) . 'languages' );
  }
  
  public function formality_menu() {
    add_menu_page( 'Formality', 'Formality', 'edit_others_posts', 'formality_menu', function() { echo 'Formality'; }, "dashicons-formality", 30 );
  }
  
  public function column_results($columns) {
    $new = array();
    foreach($columns as $key=>$value) {
      if($key=='date') {
        $new['type'] = __('Form type', 'formality');
        $new['results'] = __('Results', 'formality');
      }    
      $new[$key]=$value;
    }  
    return $new;
  }
  
  public function column_results_row( $column, $post_id ) {
    if ($column == 'results'){ 
      $term = get_term_by("slug", "form_" . $post_id, 'formality_tax');
      if($term) {
        $counter = $term->count;
        echo '<a href="' . get_admin_url() . 'edit.php?post_type=formality_result&formality_tax=form_' . $post_id . '">' . $counter . " " . __("results", "formality") . '</a>';
      } else {
        echo __("No results", "formality");
      }
    } else if ($column == 'type'){
      $type = get_post_meta($post_id, "_formality_type");
      if(isset($type[0]) && $type[0]=="conversational") {
        echo __("Conversational", "formality");
      } else {
        echo __("Standard", "formality");
      }
    }
  }

  public function welcome_notice() {
    global $pagenow, $typenow;
    if ('edit.php' === $pagenow && strpos($typenow, 'formality_') !== false) {
      global $wp_post_types;
      $labels = $wp_post_types[$typenow]->labels;
    ?>
      <div class="wrap wrap--formality">
        <h1 class="wp-heading-inline"><a href="#"><i class="dashicons-formality-fo"></i></a><?php echo $labels->name; ?></h1>
        <?php if ($typenow=='formality_form') { ?>
          <?php $plugin_tools = new Formality_Tools( $this->formality, $this->version ); ?>
          <a href="<?php echo admin_url('post-new.php?post_type='.$typenow); ?>" class="page-title-action"><?php echo $labels->add_new; ?></a>
          <?php $welcome = get_option('formality_welcome'); ?>
          <a class="formality-welcome-toggle <?php echo $welcome ? 'close' : 'open'; ?>" href="<?php echo $plugin_tools->toggle_panel_link_url(); ?>"><span><?php _e('Hide panel', 'formality'); ?></span><span><?php _e('Show panel', 'formality'); ?></span> <i class="dashicons-formality"></i></a>
          <div class="welcome-panel<?php echo $welcome ? '' : ' hidden'; ?>">
            <a class="welcome-panel-close formality-welcome-toggle" href="<?php echo $plugin_tools->toggle_panel_link_url(false); ?>"><?php _e('Hide panel', 'formality'); ?></a>
      			<div class="welcome-panel-content">
            	<h2><?php _e('Welcome to Formality!', 'formality'); ?></h2>
              <p class="about-description"><?php _e('Everything is ready to start building your forms:', 'formality'); ?></p>
              <div class="welcome-panel-column-container">
                <div class="welcome-panel-column">
        					<h3><?php _e('Get Started', 'formality'); ?></h3>
                  <a class="button button-primary button-hero" href="<?php echo admin_url('post-new.php?post_type=formality_form'); ?>"><?php _e('Create your first form', 'formality'); ?></a>
                  <p><?php echo sprintf( __('or <a href="%s">generate a couple of sample forms</a> to practice with.', 'formality'), $plugin_tools->generate_sample_link_url() ); ?></p>
                  <p><?php echo sprintf( __('or <a href="%s">import your forms</a> with Wordpress import tool.', 'formality'), admin_url('admin.php?import=wordpress')); ?></p>
                </div>
                <div class="welcome-panel-column">
                  <h3><?php _e('Quick links', 'formality'); ?></h3>
                  <ul>
              		  <li><a href="<?php echo admin_url('edit.php?post_type=formality_form'); ?>" class="welcome-icon welcome-edit-page"><?php _e('Manage your forms', 'formality'); ?></a></li>
                    <li><a href="<?php echo admin_url('edit.php?post_type=formality_result'); ?>" class="welcome-icon welcome-view-site"><?php _e('View your results', 'formality'); ?></a></li>
                    <li><a href="https://formality.dev" class="welcome-icon welcome-learn-more"><?php _e('Learn more about Formality', 'formality'); ?></a></li>
              		</ul>
                </div>
                <div class="welcome-panel-column welcome-panel-last">
                  <h3><?php _e('Support us', 'formality'); ?></h3>
                  <p><?php echo sprintf(__('Subscribe to our newsletter (max once a month) or rate this plugin with a <a href="%s">5 stars review</a> on Wordpress directory.', 'formality'), 'https://wordpress.org/support/plugin/formality/reviews/?filter=5#new-post'); ?></p>
                  <form action="https://michelegiorgi.us14.list-manage.com/subscribe/post?u=faecff7416c1e26364c56ff3d&amp;id=4f37f92e73" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
                  	<input placeholder="<?php _e('Your email address', 'formality'); ?>" type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL">
                  	<input type="submit" value="<?php _e('Subscribe', 'formality'); ?>" name="subscribe" id="mc-embedded-subscribe" class="button">
                    <br><label class="checkbox subfield" for="gdpr_33536"><input type="checkbox" id="gdpr_33536" name="gdpr[33536]" value="Y" class="av-checkbox "><small><?php echo sprintf( __('Accept our <a href="%s">privacy policy</a>.', 'formality'), '#'); ?></small></label>
                  </form>
                </div>
              </div>
        		</div>
          </div>
          <script>
            jQuery('.formality-welcome-toggle').click(function(e){
              e.preventDefault()
              jQuery('.formality-welcome-toggle').toggleClass('close').addClass('loading')
              jQuery('.welcome-panel').toggleClass('hidden')
              const href = this.href
              jQuery.ajax({ url: href }).done(function(data) {
                jQuery('.formality-welcome-toggle').removeClass('loading')
              });
            })
          </script>
        <?php } ?>
        <?php if ((isset( $_GET['formality_task']) || isset( $_POST['formality_task'])) && get_option('formality_notice') ) {
          $notice = get_option('formality_notice');
          echo '<div class="notice notice-'.$notice[0].' is-dismissible"><p>'.$notice[1].'</p></div>';
          delete_option('formality_notice');
        } ?>
      </div>
    <?php }
  }

}
