<?php
/*
    Plugin Name: WP Flare API
    Plugin URI: http://github.com/afgoulart/wp-flare-api
    Description: Manager Options to Flare application
    Version: 1.0.0
    Author: André Filipe Goulart
*/
// create custom plugin settings menu
add_action('admin_menu', 'flare_manager_create_menu');

function load_scripts() {
    wp_enqueue_script( 'wp-flare-scripts', plugin_dir_url( __FILE__ ) . '/js/main.js' );
    wp_enqueue_style( 'wp-flare-styles', plugin_dir_url( __FILE__ ) . '/css/main.css' );
}
add_action( 'admin_enqueue_scripts', 'load_scripts' );

function flare_manager_create_menu() {
    //create new top-level menu
    add_menu_page(
        __('Flare Settigns API', 'wp-flare-api'),
        __('Flare Manager', 'wp-flare-api'),
        'edit_themes',
        'flare_setting_page',
        'flare_manager_settings_page',
        '',
        7
    );
    add_submenu_page(
        'flare_setting_page',
        __('Resources List', 'wp-flare-api'),
        __('Resources List', 'wp-flare-api'),
        'edit_themes',
        'resources',
        'resources_list'
    );
    add_submenu_page(
        'flare_setting_page',
        __('New Resource', 'wp-flare-api'),
        __('New Resource', 'wp-flare-api'),
        'edit_themes',
        'new-resource',
        'new_resource'
    );
    add_submenu_page(
        'flare_setting_page',
        __('New Subscription', 'wp-flare-api'),
        __('New Subscription', 'wp-flare-api'),
        'edit_themes',
        'new-subscription',
        'new_subscription'
    );

    //call register settings function
    add_action( 'admin_init', 'register_flare_manager_settings' );
}

function resources_list(){
    include_once('flare-resources-list.php');
}

function getSubscriptions($url, $id) {
  if (!isset($url)) {
    return array();
  }
  $field = array();

  $args = array(
      'timeout'     => 5,
      'redirection' => 5,
      'httpversion' => '1.0',
      'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
      'blocking'    => true,
      'headers'     => array(),
      'cookies'     => array(),
      'body'        => null,
      'compress'    => false,
      'decompress'  => true,
      'sslverify'   => true,
      'stream'      => false,
      'filename'    => null
  );

  $response = wp_remote_get( $url, $args );
}

function populate_all_resouces($url) {
    if (!isset($url)) {
        return;
    }
    $field = array();

    $args = array(
        'timeout'     => 5,
        'redirection' => 5,
        'httpversion' => '1.0',
        'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
        'blocking'    => true,
        'headers'     => array(),
        'cookies'     => array(),
        'body'        => null,
        'compress'    => false,
        'decompress'  => true,
        'sslverify'   => true,
        'stream'      => false,
        'filename'    => null
    );

    $response = wp_remote_get( $url, $args );
    if ( is_array( $response ) ) {
      $body = json_decode($response['body'], true); // use the content
      if (!isset($body['resources'])) {
          return $field;
      }

      $resources = $body['resources'];
      foreach ($resources as $value) {
        $label = $value['addresses'][0] . $value['path'];
        $option = array(
            'id' => $value['id'],
            'value' => $label
        );
        array_push($field, $option);
      }
    }

    return $field;
  }

function new_resource() {
    include_once('flare-resource-new.php');
}
function new_subscription() {
    include_once('flare-subscription-new.php');
}

function register_flare_manager_settings() {
    register_setting( 'flare-manager-settings-group', 'flare_url' );
}

function isEmpty($var) { return (!$var); }
function getValueTextarea($values) {
  $values = str_replace("\r","",$values);
  $values = trim($values);
  return array_values(array_filter(explode("\n", $values)));
}

function checkURL($value) {
  $url = filter_var($value, FILTER_SANITIZE_URL);
  $url_parsed = parse_url($value);
  if (isset($url_parsed['scheme']) && $url_parsed['scheme'] != 'http' && $url_parsed['scheme'] != 'https') {
    return false;
  }

  $temp_url = $url_parsed['scheme'] . $url_parsed['host'];
  if (filter_var($url, FILTER_VALIDATE_URL) === FALSE && filter_var($temp_url, FILTER_VALIDATE_URL) === FALSE) {
    return false;
  }

  return true;
}

function isValid($type, $value, &$requestValid){
  $valid = TRUE;

  switch ($type) {
    case 'resources':
      if (isEmpty($value)) {
        $msg = __("This field is required!", "wp-flare-api");
        $valid = FALSE;
      }
      break;
    case 'headers':
      $value = getValueTextarea($value);
      if (is_array($value) && count($value) > 0) {
        $tmp = array();
        foreach ($value as $opt) {
          $opt = split(' : ', $opt);
          if ($valid == TRUE && count($opt) != 2) {
            $valid = FALSE;
            $msg = __('This field is require url valid!', 'wp-flare-api');
          } else {
            if (isset($tmp[$opt[0]])) {
              array_push($tmp[$opt[0]], $opt[1]);
            } else {
              $tmp[$opt[0]] = array($opt[1]);
            }
          }
        }
        $value = $tmp;
      }
      break;
    case 'code_success':
    case 'code_discard':
      if(!isEmpty($value)){
        $value = str_replace(', ', ',', $value);
        $value = array_map('intval', explode(',', $value));
      } else {
        $msg = __("This field is required!", "wp-flare-api");
        $valid = FALSE;
        $value = array();
      }
      break;
    case 'endpoint':
      if (!isset($value)) {
        $valid = FALSE;
        $msg = __("This field is required!", "wp-flare-api");
      } else if (!checkURL($value)) {
        $valid = FALSE;
        $msg = __('This field is require url valid!', 'wp-flare-api');
      } else {
        $value = urldecode($value);
      }
      break;
    case 'addresses':
      $value = getValueTextarea($value);
      if (is_array($value) && count($value) > 0) {
        foreach ($value as $opt) {
          if ($valid == TRUE && checkURL($opt) == FALSE ) {
            $valid = FALSE;
            $msg = __('This field is require url valid!', 'wp-flare-api');
          }
        }
      } else {
        $msg = __("This field is required!", "wp-flare-api");
        $valid = FALSE;
      }
      break;
    case 'path':
      if (isEmpty($value)) {
        $msg = __("This field is required!", "wp-flare-api");
        $valid = FALSE;
      }
      break;
    case 'field_type':
      if (isEmpty($value)) {
        $msg = __("This field is required!", "wp-flare-api");
        $valid = FALSE;
      }
      break;
    case 'field':
      if (isEmpty($value)) {
        $msg = __("This field is required!", "wp-flare-api");
        $valid = FALSE;
      }
      break;
    case 'format':
      if (isEmpty($value)) {
        $msg = __("This field is required!", "wp-flare-api");
        $valid = FALSE;
      }
      break;
    case 'sendDocument':
      $value = isset($value);
      break;
    case 'skipDocument':
      $value = isset($value);
      break;
  }

  if ($requestValid == TRUE) {
    $requestValid = $valid;
  }
  return array(
    'valid' => $valid,
    'value' => $value,
    'message-error' => $msg
  );
}

function flare_manager_settings_page() {
?>
<div id="wpwrap">
    <div class="wrap">
        <h1 class="wp-heading-inline">Flare Manager</h1>
        <a href="?page=new-resource" class="page-title-action">Add new resource</a>
        <a href="?page=new-subscription" class="page-title-action">Add new subscription</a>
        <a href="?page=resources" class="page-title-action">Resources list</a>
        <hr class="wp-header-end">
        <form method="post" action="options.php">
            <?php settings_fields( 'flare-manager-settings-group' ); ?>
            <?php do_settings_sections( 'flare-manager-settings-group' ); ?>
            <table class="form-table">
                <tr valign="top">
                <th scope="row">Flare URL</th>
                <td><input type="text" name="flare_url" value="<?php echo esc_attr( get_option('flare_url') ); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
</div>
<?php } ?>
