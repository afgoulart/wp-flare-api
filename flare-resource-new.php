<?php
function render_form($validData) {
  settings_fields( 'flare-manager-settings-group' );
  do_settings_sections( 'flare-manager-settings-group' );

  $path = plugin_dir_url( __FILE__ );
?>
  <form class="form-resource" method="POST" action="?page=new-resource">
    <input type="hidden" name="flareUrl" value="<?php echo esc_attr( get_option('flare_url') ); ?>/resources" />
    <input type="hidden" name="redirectTo" value="?page=resources" />
    <input type="hidden" name="message-success" value="Resource created with success!" />

    <table class="form-table">
      <tr valign="top" class="form-field form-required <?php
        if (isset($validData) && $validData['addresses']['valid'] == FALSE) { echo " form-invalid"; } ?>">
        <th scope="row"><label for="addresses"><?php echo __('Address', 'wp-flare-api'); ?> <span class="required">*</span>:</label></th>
        <td>
          <textarea cols="50" rows="5" id="addresses" aria-require="true" name="addresses"><?php if(isset($validData)) { echo join('\n', $validData['addresses']['value']); }?></textarea>
          <?php if (isset($validData) && $validData['addresses']['valid'] == FALSE) { ?>
          <div class="error">
            <p><?php echo __('Field', 'wp-flare-api'); ?> "<?php echo __('Address', 'wp-flare-api'); ?>": <?php echo $validData['addresses']['message-error']?></p>
          </div>
          <?php  } ?>
        </td>
      </tr>
      <tr valign="top" class="form-field form-required <?php
        if (isset($validData) && $validData['path']['valid'] == FALSE) { echo " form-invalid"; } ?>">
        <th scope="row"><label for="path"><?php echo __('Path', 'wp-flare-api');?> <span class="required">*</span>:</label></th>

        <td>
          <input type="text" name="path"
                  value="<?php
                  if(isset($validData)) {
                    echo $validData['path']['value'];
                  }?>"
                  placeholder="/path/service/{id}" aria-require="true" />
          <?php if (isset($validData) && $validData['path']['valid'] == FALSE) { ?>
          <div class="error">
            <p><?php echo __('Field', 'wp-flare-api'); ?> "<?php echo __('Path', 'wp-flare-api');?>": <?php echo $validData['path']['message-error']?></p>
          </div>
          <?php } ?>
        </td>
      </tr>
      <tr valign="top" class="form-field form-required <?php if (isset($validData) && $validData['field_type']['valid'] == FALSE) { echo " form-invalid"; } ?>">
        <th scope="row"><label for="field_type"><?php echo __('Field Type', 'wp-flare-api'); ?>:</label></th>
        <td>
          <?php
            $checkValueInput = 'date';
            if (isset($validData) && isset($validData['field_type']['value'])) {
              $checkValueInput = $validData['field_type']['value'];
            }
          ?>
          <input type="radio" id="field_type" <?php if ($checkValueInput == 'date') { ?> checked <?php } ?> name="field_type" value="date" /> Date<br/>
          <input type="radio" <?php if ($checkValueInput == 'integer') { ?> checked <?php } ?> name="field_type" value="integer" /> Numeric
        </td>
      </tr>
      <tr valign="top" class="form-field form-required <?php if (isset($validData) && $validData['field']['valid'] == FALSE) { echo " form-invalid"; } ?>">
        <th scope="row"><label for="field"><?php echo __('Field modified', 'wp-flare-api'); ?> <span class="required">*</span>:</label></th>
        <td>
          <input type="text" name="field" value="<?php if(isset($validData) && isset($validData['field']['value'])) { echo $validData['field']['value']; }?>" placeholder="updateAt" aria-require="true" />
          <?php if (isset($validData) && $validData['field']['valid'] == FALSE) { ?>
          <div class="error">
            <p><?php echo __('Field', 'wp-flare-api'); ?> "<?php echo __('Field modified', 'wp-flare-api'); ?>": <?php echo $validData['field']['message-error']?></p>
          </div>
          <?php } ?>
        </td>
      </tr>
      <tr valign="top" <?php if ($checkValueInput == 'integer') { ?> style="display:none" <?php } ?> class="form-field form-required field-format<?php if (isset($validData) && $validData['format']['valid'] == FALSE) { echo " form-invalid"; } ?>" data-on-visible='{"input_name": "field_type", "value": "date"}'>
        <th scope="row"><label for="format"><?php echo __('Format field', 'wp-flare-api'); ?> <span class="required">*</span>:</label></th>
        <td><input type="text" name="format" value="<?php if(isset($validData) && isset($validData['format']['value'])) { echo $validData['format']['value']; }?>" placeholder="2006-01-02T15:04:05Z07:00" aria-require="true" />
          <?php if (isset($validData) && $validData['format']['valid'] == FALSE) { ?>
          <div class="error">
            <p><?php echo __('Field', 'wp-flare-api'); ?> "<?php echo __('Format field', 'wp-flare-api'); ?>": <?php echo $validData['format']['message-error']?></p>
          </div>
          <?php } ?>
        </td>
      </tr>
    </table>

    <?php submit_button(); ?>
  </form>
<?php
}
?>

<div id="wpwrap">
  <div class="wrap">
    <h1 class="wp-heading-inline"><?php echo __('New Resource', 'wp-flare-api'); ?></h1>
    <a href="?page=new-subscription" class="page-title-action"><?php echo __('Add new subscription', 'wp-flare-api') ?></a>
    <a href="?page=resources" class="page-title-action"><?php echo __('Resources list', 'wp-flare-api') ?></a>
    <hr class="wp-header-end">
    <?php
    if (isset($_POST["flareUrl"])) {
      require_once('flare-request.php');

      $requestValid = TRUE;
      $validData = array(
        'addresses' => isValid('addresses', $_POST["addresses"], $requestValid),
        'path' => isValid('path', $_POST["path"], $requestValid),
        'field_type' => isValid('field_type', $_POST["field_type"], $requestValid),
        'field' => isValid('field', $_POST["field"], $requestValid),
      );

      if ($validData['field_type']['value'] == 'integer') {
        $validData['format'] = array(
          'valid'=> true,
          'value' => $_POST['format']
        );
      } else {
        $validData['format'] = isValid('format', $_POST["format"], $requestValidFormat);
      }

      if ($requestValid == TRUE) {
        $postData = array(
          "flareURL" => $_POST["flareUrl"],
          "redirectTo" => $_POST["redirectTo"],
          "messageSuccess" => $_POST["message-success"],
          "content" => array(
            "addresses" => $validData["addresses"]['value'],
            "path" => $validData["path"]['value'],
            "change" => array(
              "kind" => $validData["field_type"]['value'],
              "field" => $validData["field"]['value'],
              "format" => $validData["format"]['value'],
            )
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