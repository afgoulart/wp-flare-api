<?php
  function get_flare_resources($resourceID) {
    global $wp_version;
    $flareURL = get_option('flare_url');

    if (!isset($flareURL)) {
      return array(
        'status' => 'error',
        'message'   => 'Flare URL not defined.'
      );
    }

    if (!isset($resourceID)) {
      $url = $flareURL . '/resources';
    } else {
      $url = $flareURL . '/resources' . '/' . $resourceID . '/subscriptions';
    }
    $args = array(
      'timeout'     => 5,
      'redirection' => 5,
      'httpversion' => '1.0',
      'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
      'blocking'    => true,
      'headers'     => array(),
      'cookies'     => array(),
      'body'        => null
    );

    $response = wp_remote_get($url);

    if ( is_array( $response ) ) {
      $body = json_decode($response['body']);
      return $body;
    }

    return;
  }

  function get_all_resources() {
    $resources = get_flare_resources(null);
    ?>
    <div id="wpwrap">
      <div class="wrap">
        <h1 class="wp-heading-inline"><?php echo __('Resources list', 'wp-flare-api');?></h1>
        <a href="?page=new-resource" class="page-title-action"><?php echo __('Add new resource', 'wp-flare-api') ?></a>
        <a href="?page=new-subscription" class="page-title-action"><?php echo __('Add new subscription', 'wp-flare-api') ?></a>

        <table class="widefat fixed" cellspacing="0">
          <thead>
            <tr>
              <th id="columnname" class="manage-column column-columnname column-id" scope="col">ID</th>
              <th id="columnname" class="manage-column column-columnname column-addresses" scope="col"><?php echo __('Addresses', 'wp-flare-api') ?></th>
              <th id="columnname" class="manage-column column-columnname column-path" scope="col"><?php echo __('Path', 'wp-flare-api') ?></th>
              <th id="columnname" class="manage-column column-columnname column-subscriptions" scope="col"><?php echo __('Subscriptions', 'wp-flare-api') ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
              if( isset($resources) ) {
                foreach($resources->resources as $key=>$resource) {
                  $subscriptions = get_flare_resources($resource->id);
                  $subscriptions = $subscriptions->subscriptions;
                  $addresses = json_encode($resource->addresses, JSON_UNESCAPED_SLASHES);
                  ?>
            <tr valign="top" <?php if ($key % 2 === 0) { ?> class="alternate" <?php } ?>>
                <td class="column-columnname"><?php echo $resource->id; ?></td>
                <td class="column-columnname">
                  <label class="truncate"><?php echo join('</label><label class="truncate">', $resource->addresses); ?></label>
                </td>
                <td class="column-columnname"><?php echo $resource->path; ?></td>
                <td class="column-columnname" colspan="3">
                  <div class="subscriptions">
                  <?php
                    $total = count($subscriptions);
                    foreach($subscriptions as $key=>$subscription) {
                      ?>
                      <label class="truncate">
                      <?php
                      if($key < 5) {
                        echo $subscription->endpoint->url;
                        if (($key+1) < $total ) {
                          echo '<br>';
                        }
                      }
                      ?>
                      </label>
                      <?php
                    }
                  ?>
                  </div>
                </td>
            </tr>
            <?php
                }
              }
            ?>
          </tbody>
          <tfoot>
            <tr>
              <th id="columnname" class="manage-column column-columnname column-id" scope="col">ID</th>
              <th id="columnname" class="manage-column column-columnname column-addresses" scope="col"><?php echo __('Addresses', 'wp-flare-api') ?></th>
              <th id="columnname" class="manage-column column-columnname column-path" scope="col"><?php echo __('Path', 'wp-flare-api') ?></th>
              <th id="columnname" class="manage-column column-columnname column-subscriptions" scope="col"><?php echo __('Subscriptions', 'wp-flare-api') ?></th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
    <?php
  }

  get_all_resources();
?>