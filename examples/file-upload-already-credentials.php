<?php
/*
 * Copyright 2011 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

include_once __DIR__ . '/../vendor/autoload.php';
include_once "templates/base.php";


$oauth_credentials = "/home/fpp/media/plugins/GoogleDrive/oauth-credentials.json";


/*************************************************
 * Ensure you've downloaded your oauth credentials
 ************************************************/
if (!$oauth_credentials = getOAuthCredentialsFile($oauth_credentials)) {
  echo missingOAuth2CredentialsWarning();
  return;
}

/************************************************
 * The redirect URI is to the current page, e.g:
 * http://localhost:8080/simple-file-upload.php
 ************************************************/
$FAKE_SERVER_NAME = "fppvm1.fpp.com/plugin.php?plugin=GoogleDrive&page=examples/simple-file-upload.php&nopage=1";
$redirect_uri = 'http://' . $FAKE_SERVER_NAME;// . $_SERVER['PHP_SELF'];

$client = new Google_Client();
$client->setAuthConfig($oauth_credentials);
$client->setRedirectUri($redirect_uri);
$client->addScope("https://www.googleapis.com/auth/drive");
$service = new Google_Service_Drive($client);

// add "?logout" to the URL to remove a token from the session
if (isset($_REQUEST['logout'])) {
  unset($_SESSION['upload_token']);
}



/************************************************
 * If we're signed in then lets try to upload our
 * file. For larger files, see fileupload.php.
 ************************************************/
//if ($_SERVER['REQUEST_METHOD'] == 'POST' && $client->getAccessToken()) {
  // We'll setup an empty 1MB file to upload.
  DEFINE("TESTFILE", '/home/fpp/media/plugins/GoogleDrive/testfile-small.txt');
  if (!file_exists(TESTFILE)) {
    $fh = fopen(TESTFILE, 'w');
    fseek($fh, 1024 * 1024);
    fwrite($fh, "THIS IS A TEST FILE", 1);
    fclose($fh);
  

  // This is uploading a file directly, with no metadata associated.
  $file = new Google_Service_Drive_DriveFile();
  $result = $service->files->create(
      $file,
      array(
        'data' => file_get_contents(TESTFILE),
        'mimeType' => 'application/octet-stream',
        'uploadType' => 'media'
      )
  );

  // Now lets try and send the metadata as well using multipart!
  $file = new Google_Service_Drive_DriveFile();
  $file->setName("Hello World!");
  $result2 = $service->files->create(
      $file,
      array(
        'data' => file_get_contents(TESTFILE),
        'mimeType' => 'application/octet-stream',
        'uploadType' => 'multipart'
      )
  );
}
?>

