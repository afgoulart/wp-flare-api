<?php
  function render_form($validData) {
      settings_fields( 'flare-manager-settings-group' );
      do_settings_sections( 'flare-manager-settings-group' );

      $flareURL = esc_attr(get_option('flare_url'));
      $resources = populate_all_resouces($flareURL . "/resources", FALSE);
      $path = plugin_dir_url( __FILE__ );
  ?>
  <form class="form-subscription" method="POST" action="?page=new-subscription">
    <input type="hidden" name="flareUrl" value="<?php echo esc_attr( get_option('flare_url') ); ?>/resources" />
    <input type="hidden" name="redirectTo" value="?page=resources" />
    <input type="hidden" name="message-success" value="Subscription created with success!" />
    <table class="form-table">
      <tr valign="top" class="form-field form-required <?php
        if (isset($validData) && $validData['resources']['valid'] == FALSE) { echo " form-invalid"; } ?>">
        <th scope="row"><label for="resources"><?php echo __('Resources', 'wp-flare-api'); ?> <span class="required">*</span>: </label></th>
        <td>
          <select name="resources">
            <option value=""><?php echo __('Select a resource', 'wp-flare-api'); ?></option>
            <?php for ($i=0; $i < count($resources) ; $i++) { ?>
            <option value="<?php echo $resources[$i]['id']; ?>"<?php if (isset($validData) && $validData['resources']['value'] == $resources[$i]['id']) { ?> selected <?php } ?>><?php echo $resources[$i]['value']; ?></option>
            <?php }?>
          </select>
          <?php if (isset($validData) && $validData['resources']['valid'] == FALSE) { ?>
          <div class="error">
            <p><?php echo __('Field', 'wp-flare-api'); ?> "<?php echo __('Resources', 'wp-flare-api'); ?>": <?php echo $validData['resources']['message-error']?></p>
          </div>
          <?php  } ?>
        </td>
      </tr>
      <tr valign="top" class="form-field form-required <?php
        if (isset($validData) && $validData['endpoint']['valid'] == FALSE) { echo " form-invalid"; } ?>">
        <th scope="row"><label for="endpoint"><?php echo __('Endpoint', 'wp-flare-api'); ?> <span class="required">*</span>:</label></th>
        <td>
          <input type="text" name="endpoint" value="<?php if (isset($validData)) { echo $validData['endpoint']['value']; } ?>"  require />
          <?php if (isset($validData) && $validData['endpoint']['valid'] == FALSE) { ?>
          <div class="error">
            <p><?php echo __('Field', 'wp-flare-api'); ?> "<?php echo __('Endpoint', 'wp-flare-api'); ?>": <?php echo $validData['endpoint']['message-error']?></p>
          </div>
          <?php  } ?>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row"><label for="method"><?php echo __('Methods', 'wp-flare-api'); ?> <span class="required">*</span>:</label></th>
        <td>
          <input type="radio" id="method" require name="method" value="GET" /> GET <br/>
          <input type="radio" require name="method" checked value="POST" /> POST <br/>
          <input type="radio" require name="method" value="PUT" /> PUT <br/>
        </td>
      </tr>
      <tr valign="top" class="form-field form-required <?php
        if (isset($validData) && $validData['headers']['valid'] == FALSE) { echo " form-invalid"; } ?>">
        <th scope="row"><label for="headers"><?php echo __('Headers', 'wp-flare-api'); ?>: </label></th>
        <td>
          <textarea cols="50" rows="5" id="headers" name="headers" placeholder="Key : Value" require><?php
            if(isset($validData) && count($validData['headers']['value']) > 0) {
              $tmp = array();
              foreach($validData['headers']['value'] as $key=>$items) {
                foreach($items as $i) {
                  array_push($tmp, $key . ' : ' . $i);
                }
              }
              echo str_replace('\n', PHP_EOL, join('\n', $tmp));
            }?></textarea>
          <?php if (isset($validData) && $validData['headers']['valid'] == FALSE) { ?>
          <div class="error">
            <p><?php echo __('Field', 'wp-flare-api'); ?> "<?php echo __('Headers', 'wp-flare-api'); ?>": <?php echo $validData['headers']['message-error']?></p>
          </div>
          <?php  } ?>
        </td>
      </tr>
      <tr valign="top" class="form-field form-required">
        <th scope="row"><label><?php echo __('Options', 'wp-flare-api'); ?>:</label></th>
        <td>
        <input type="checkbox" id="skipEnvelope" name="skipEnvelope" <?php
          if (isset($validData) && $validData['skipEnvelope']['value'] == TRUE) { ?> checked <?php } ?> value="TRUE"/>
        <label for="skipEnvelope"><?php echo __('Skip Envelope', 'wp-flare-api'); ?></label><br />
          <input type="checkbox" id="sendDocument" name="sendDocument" <?php
            if (isset($validData) && $validData['sendDocument']['value'] == TRUE) { ?> checked <?php } ?> value="TRUE"/>
          <label for="sendDocument"><?php echo __('Send Document', 'wp-flare-api'); ?></label>
        </td>
      </tr>
      <tr valign="top" class="form-field form-required <?php
        if (isset($validData) && $validData['code_success']['valid'] == FALSE) { echo " form-invalid"; } ?>">
        <th scope="row"><label for="code"><?php echo __('Status Code (success)', 'wp-flare-api'); ?> <span class="required">*</span>:</label></th>
        <td>
          <input type="input" id="success" name="code_success" value="<?php
            if (isset($validData) && count($validData['code_success']['value']) > 0) {
              echo join(', ', $validData['code_success']['value']);
            } ?>" placeholder="200, 202, 201" require />
          <?php if (isset($validData) && $validData['code_success']['valid'] == FALSE) { ?>
          <div class="error">
            <p><?php echo __('Field', 'wp-flare-api'); ?> "<?php echo __('Status Code (success)', 'wp-flare-api'); ?>": <?php echo $validData['code_success']['message-error']?></p>
          </div>
          <?php  } ?>
        </td>
      </tr>
      <tr valign="top" class="form-field form-required <?php
        if (isset($validData) && $validData['code_discard']['valid'] == FALSE) { echo " form-invalid"; } ?>">
        <th scope="row"><label for="code"><?php echo __('Status Code (discard)', 'wp-flare-api'); ?> <span class="required">*</span>:</label></th>
        <td>
          <input type="input" id="discard" name="code_discard" value="<?php
            if (isset($validData) && count($validData['code_discard']['value']) > 0) {
              echo join(', ', $validData['code_discard']['value']);
            } ?>" placeholder="400, 404, 500, 503" require />
          <?php if (isset($validData) && $validData['code_discard']['valid'] == FALSE) { ?>
          <div class="error">
            <p><?php echo __('Field', 'wp-flare-api'); ?> "<?php echo __('Status Code (discard)', 'wp-flare-api'); ?>": <?php echo $validData['code_discard']['message-error']?></p>
          </div>
          <?php  } ?>
        </td>
      </tr>
    </table>

    <?php submit_button(); ?>
  </form>
<?php } ?>

<div id="wpwrap">
  <div class="wrap">
    <h1 class="wp-heading-inline"><?php echo __('New Subscription', 'wp-flare-api'); ?></h1>
    <a href="?page=new-resource" class="page-title-action"><?php echo __('Add new resource', 'wp-flare-api') ?></a>
    <a href="?page=resources" class="page-title-action"><?php echo __('Resources list', 'wp-flare-api') ?></a>
    <hr class="wp-header-end">
    <?php
    if (isset($_POST["flareUrl"])) {
      require_once('flare-request.php');

      $sendDocument = isset($_POST['sendDocument']);
      $skipEnvelope = isset($_POST['skipEnvelope']);

      $requestValid = TRUE;
      $validData = array(
        'resources' => isValid('resources', $_POST['resources'], $requestValid),
        'endpoint' => isValid('endpoint', $_POST['endpoint'], $requestValid),
        'method' => isValid('method', $_POST['method'], $requestValid),
        'headers' => isValid('headers', $_POST['headers'], $requestValid),
        'code_success' => isValid('code_success', $_POST['code_success'], $requestValid),
        'code_discard' => isValid('code_discard', $_POST['code_discard'], $requestValid),
        'sendDocument' => array('value' => $sendDocument),
        'skipEnvelope' => array('value' => $skipEnvelope)
      );

      if ($requestValid == TRUE) {
        $postData = array(
          "flareURL" => $_POST["flareUrl"] . '/' . $_POST['resources'] . '/subscriptions',
          "redirectTo" => $_POST["redirectTo"],
          "messageSuccess" => $_POST["message-success"],
          "content" => array(
            "endpoint" => array(
              "url" => $validData['endpoint']['value'],
              "headers" => $validData['headers']['value'],
              "method" => $validData['method']['value']
            ),
            "delivery" => array(
              "success" => $validData['code_success']['value'],
              "discard" => $validData['code_discard']['value']
            ),
            "sendDocument" => $validData['sendDocument']['value'],
            "skipEnvelope" => $validData['skipEnvelope']['value'],
          )
        );

        $response = sendRequestFlare($postData);
        if (!isset($response['error'])) {
          unset($validData);
        }
      }
    }
    render_form($validData);
?>
  </div>
</div>