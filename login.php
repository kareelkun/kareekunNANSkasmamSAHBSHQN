<?php

session_start();
ini_set('display_errors', 1);

require_once __DIR__ . '/Facebook/autoload.php';

$fb = new Facebook\Facebook([
  'app_id' => 'APP-IDNYA-BROH', 
  'app_secret' => 'KODE-SECRET-BROH',
  'default_graph_version' => 'v2.2',
  ]);

$helper = $fb->getRedirectLoginHelper();

$permissions = ['email,publish_actions,user_posts,user_photos'];
$loginUrl = $helper->getLoginUrl('http://kareelkun.ga/fb-callback.php', $permissions);

echo '<center><a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a></center><br/>';

?>
