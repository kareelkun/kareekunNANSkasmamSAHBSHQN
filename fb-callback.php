<?php

if(!session_id()) {
    session_start();
}

ini_set('display_errors', 1);

require_once __DIR__ . '/Facebook/autoload.php';

$config =[
  'app_id' => 'APPIDNYABROH', 
  'app_secret' => 'SECRETNYABROH',
  'default_graph_version' => 'v2.2',
  ];
$fb = new Facebook\Facebook($config);

$helper = $fb->getRedirectLoginHelper();

try {
  $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

if (! isset($accessToken)) {
  if ($helper->getError()) {
    header('HTTP/1.0 401 Unauthorized');
    echo "Error: " . $helper->getError() . "\n";
    echo "Error Code: " . $helper->getErrorCode() . "\n";
    echo "Error Reason: " . $helper->getErrorReason() . "\n";
    echo "Error Description: " . $helper->getErrorDescription() . "\n";
  } else {
    header('HTTP/1.0 400 Bad Request');
    echo 'Bad request';
  }
  exit;
}

// Logged in
// echo '<h3>Access Token</h3>';
// var_dump($accessToken->getValue());

// The OAuth 2.0 client handler helps us manage access tokens
$oAuth2Client = $fb->getOAuth2Client();

// Get the access token metadata from /debug_token
$tokenMetadata = $oAuth2Client->debugToken($accessToken);
// echo '<h3>Metadata</h3>';
// var_dump($tokenMetadata);

// Validation (these will throw FacebookSDKException's when they fail)
$tokenMetadata->validateAppId('472612613116977'); // Replace {app-id} with your app id
// If you know the user ID this access token belongs to, you can validate it here
//$tokenMetadata->validateUserId();
$tokenMetadata->validateExpiration();

if (! $accessToken->isLongLived()) {
  // Exchanges a short-lived access token for a long-lived one
  try {
    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
  } catch (Facebook\Exceptions\FacebookSDKException $e) {
    echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
    exit;
  }

//  echo '<h3>Long-lived</h3>';
//  var_dump($accessToken->getValue());
}

$_SESSION['fb_access_token'] = (string) $accessToken;

try {
  // Returns a `Facebook\FacebookResponse` object
  $response = $fb->get('/me?locale=id_ID&fields=id,name,first_name,last_name,email,location{location},work,gender,birthday', $accessToken);
  $node = $response->getGraphNode();
  $profile = $response->getGraphNode()->asArray();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

$user = $response->getGraphUser();

echo '<h3>FB Profile Info</h3>';

echo 'ID FB: ' . $user['id'];
echo '<br/>Name: ' . $profile['name'];
echo '<br/>Email: ' . $user['email'];
if (!empty($profile['birthday'])) {
echo '<br/>Tanggal Lahir: ' .$profile['birthday']->format('d-m-Y');
} else {
echo '<br/>Tanggal Lahir: Tidak Diketahui';
}
echo '<br/>Gender: ' . $user['gender'];
echo '<br/>Firstname: ' . $user['first_name'];
echo '<br/>Lastname: ' . $user['last_name'];

if (!empty($user['location'])) {
$potong = explode('":', $user['location']);
$potong1 = explode('","latitude', $potong[3]);
$negara = str_replace('"', '', $potong1[0]);
$potong2 = explode('","country', $potong[2]);
$kota = str_replace('"', '', $potong2[0]);
$potong3 = explode(',"longitude', $potong[4]);
$lati = str_replace('"', '', $potong3[0]);
$potong4 = explode('},"id', $potong[5]);
$long = str_replace('"', '', $potong4[0]);
echo '<br/>Negara: '.$negara;
echo '<br/>Kota: '.$kota;
echo '<br/>Lati: '.$lati;
echo '<br/>Long: '.$long;
$_SESSION['NEGARA'] = $negara;
$_SESSION['NEGARA'] = $kota;
$_SESSION['LAN'] = $negara;
$_SESSION['LAT'] = $negara;
}else{
echo '<br/>Lokasi: Tidak Diketahui';
} 

if (!empty($user['work'])) {
$potongwork = explode('":', $user['work']);
$potong1work = explode('"},"location', $potongwork[4]);
$namakerja = str_replace('"', '', $potong1work[0]);
echo '<br/>Work: ' . $namakerja;
$_SESSION['KERJA'] = $namakerja;
}else{
echo '<br/>Pekerjaan: Tidak Diketahui';
} 

?>
