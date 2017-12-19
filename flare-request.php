<?php
  function sendRequestFlare($postData) {
    $flareURL = $postData['flareURL'];
    $redirectTo = $postData['redirectTo'];
    $messageSuccess = $postData['messageSuccess'];
    $rootPath = dirname(dirname(dirname(__DIR__)));

    require_once( $rootPath . '/wp-config.php');
    require_once( $rootPath . '/wp-includes/class-http.php');
    require_once( $rootPath . '/wp-includes/http.php');

    $response = wp_remote_post( $flareURL, array(
      'method' => 'POST',
      'timeout' => 45,
      'redirection' => 5,
      'httpversion' => '1.0',
      'blocking' => true,
      'headers' => array(
        'Content-Type' => 'application/json'
      ),
      'body' => json_encode($postData['content'], JSON_UNESCAPED_SLASHES),
      'cookies' => array()
    ));

    if ( is_array( $response ) ) {
      $responseJSON = json_decode($response['body'], true); // use the content
      if (isset($responseJSON['error'])){
        $result =  array(
          'status' => $response['response']['code'],
          'error' => $response['response']['code'],
          'message' => $responseJSON['error']['detail'],
          'data' => $postData
        );
      } else {
        $result = array(
          'status' => 200,
          'redirectTo' => $redirectTo,
          'message' => $messageSuccess
        );
      }
    } else {
      $result = array(
        'error' => 500,
        'message' => '"' . $flareURL . '" - '. __('Gateway Timeout', 'wp-flare-api'),
        'data' => $postData
      );
    }

    ?>
    <div class="notice notice-<?php if (isset($result['error'])) { echo 'error'; } else { echo 'success'; }?>">
      <pre><?php echo $result["message"]; ?></pre>
    </div>
    <?php
    return $result;
  }
?>