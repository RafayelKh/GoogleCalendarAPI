<?php
/**
 * GCAL setup
 *
 * @package GCAL
 */

defined( 'ABSPATH' ) || exit;

require GCAL_ABSPATH . '/vendor/autoload.php';

/**
 * Class working with GoogleCal API.
 */
class GoogleCal {
  /**
   * Instance of Google_Client.
   */
  private $client;

  /**
   * Instance of Google_Service_Calendar.
   */
  private $service;

  /**
   * Class instance
   */
  private static $instance;

  /**
   * Init authorized API client.
   */
  private function __construct() {
    try {
      $this->client = new Google_Client();
      $this->client->setApplicationName( 'Google Calendar API PHP Quickstart' );
      $this->client->setScopes( Google_Service_Calendar::CALENDAR );
      $this->client->setAuthConfig( GCAL_ABSPATH . '/credentials.json' );
      $this->client->setAccessType( 'offline' );
      $this->client->setPrompt( 'select_account consent' );


       // Load previously authorized token from a file, if it exists.
    // The file token.json stores the user's access and refresh tokens, and is
    // created automatically when the authorization flow completes for the first
    // time.
    $tokenPath = 'token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $this->client->setAccessToken($accessToken);
    }

    // If there is no previous token or it's expired.
    if ($this->client->isAccessTokenExpired()) {
        // Refresh the token if possible, else fetch a new one.
        if ($this->client->getRefreshToken()) {
            $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
        } else {
            // Request authorization from the user.
            $authUrl = $this->client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $this->client->fetchAccessTokenWithAuthCode($authCode);
            $this->client->setAccessToken($accessToken);

            // Check to see if there was an error.
            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            }
        }
        // Save the token to a file.
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));
    }

      /** Init service object */
      $this->service = new Google_Service_Calendar( $this->client );
    } catch ( Exception $e ) {
      echo "ERROR";
      var_dump( $e );
    }
  }

  /**
   * Get same class instance.
   */
  public static function get_instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new GoogleCal();
        }

        return self::$instance;
    }


  /**
   * Push events to Google Calendar
   */
  public function push_events( $events ) {
    try {
      $event = new Google_Service_Calendar_Event(
        array(
          'summary' => 'Google I/O 2015',
          'location' => '800 Howard St., San Francisco, CA 94103',
          'description' => 'A chance to hear more about Google\'s developer products.',
          'start' => array(
              'dateTime' => '2020-07-12T09:00:00-07:00',
              'timeZone' => 'America/Los_Angeles',
          ) ,
          'end' => array(
              'dateTime' => '2020-07-12T17:00:00-07:00',
              'timeZone' => 'America/Los_Angeles',
          ) ,
          'recurrence' => array(
              'RRULE:FREQ=DAILY;COUNT=2'
          ) ,
          'attendees' => array(
              array(
                  'email' => 'lpage@example.com'
              ) ,
              array(
                  'email' => 'sbrin@example.com'
              ) ,
          ) ,
          'reminders' => array(
              'useDefault' => false,
              'overrides' => array(
                  array(
                      'method' => 'email',
                      'minutes' => 24 * 60
                  ) ,
                  array(
                      'method' => 'popup',
                      'minutes' => 10
                  ) ,
              ) ,
          ) ,
      ));
  
      $calendarId = 'primary';
      $event = $this->service->events->insert( $calendarId, $event );

      return $event;
    } catch ( Exception $e ) {
      echo "ERROR";
      var_dump( $e );
    }
  }

  /**
   * Get events from Google Calendar
   */
  public function get_events() {
    try {
      // Print the next 10 events on the user's calendar.
      $optParams = array(
        'maxResults' => 10,
        'orderBy' => 'startTime',
        'singleEvents' => true,
        'timeMin' => date('c') ,
      );

      $calendarId = 'primary';
      return $this->service->events->listEvents( $calendarId, $optParams )->getItems();
    } catch ( Exception $e ) {
      echo "ERROR";
      var_dump( $e );
    }
  }
}
