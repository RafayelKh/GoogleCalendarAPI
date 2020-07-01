<?php
/**
 * GCAL setup
 *
 * @package GCAL
 */

defined( 'ABSPATH' ) || exit;

require GCAL_ABSPATH . '/vendor/autoload.php';

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
class GoogleCal{
  private $client;

  private static $instances = [];

  protected function __construct() {}

  protected function __clone() {}

  public function __wakeup(){
	throw new \Exception("Cannot unserialize a singleton.");
  }

  public static function getInstance(): Singleton
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static;
        }

        return self::$instances[$cls];
    }

  public function get_client(){
    try {
      $this->client = new Google_Client()
      $this->client->setApplicationName('Google Calendar API PHP Quickstart');
      $this->client->setScopes(Google_Service_Calendar::CALENDAR);
      $this->client->setAuthConfig(GCAL_ABSPATH . '/credentials.json');
      $this->client->setAccessType('offline');
      $this->client->setPrompt('select_account consent');

      // Load previously authorized token from a file, if it exists.
      // The file token.json stores the user's access and refresh tokens, and is
      // created automatically when the authorization flow completes for the first
      // time.
      // $tokenPath = 'token.json';
      // if (file_exists($tokenPath)) {
      //     $accessToken = json_decode(file_get_contents($tokenPath), true);
      //     $client->setAccessToken($accessToken);
      // }

      // If there is no previous token or it's expired.
      // if ($client->isAccessTokenExpired()) {
      //     // Refresh the token if possible, else fetch a new one.
      //     if ($client->getRefreshToken()) {
      //         $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
      //     } else {
      //         // Request authorization from the user.
      //         $authUrl = $client->createAuthUrl();
      //         printf("Open the following link in your browser:\n%s\n", $authUrl);
      //         print 'Enter verification code: ';
      //         $authCode = trim(fgets(STDIN));

      //         // Exchange authorization code for an access token.
      //         $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
      //         $client->setAccessToken($accessToken);

      //         // Check to see if there was an error.
      //         if (array_key_exists('error', $accessToken)) {
      //             throw new Exception(join(', ', $accessToken));
      //         }
      //     }
      //     // Save the token to a file.
      //     if (!file_exists(dirname($tokenPath))) {
      //         mkdir(dirname($tokenPath), 0700, true);
      //     }
      //     file_put_contents($tokenPath, json_encode($client->getAccessToken()));
      // }
      return $this->client;
    } catch ( Exception $e ) {
      var_dump( $e );
    }
  }
}
