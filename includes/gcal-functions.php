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
  public $client;
  public $calendarId = 'primary';
  public $service;
  private $event;
  public $optParams;

  function get_client(){
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

  function gcal_push_event() {
      // Get the API client and construct the service object.
      $this->client = $this->get_client();
      $this->service = new Google_Service_Calendar($this->client);

      $this->event = new Google_Service_Calendar_Event(array(
          'summary' => 'Google I/O 2015',
          'location' => '800 Howard St., San Francisco, CA 94103',
          'description' => 'A chance to hear more about Google\'s developer products.',
          'start' => array(
            'dateTime' => '2020-07-12T09:00:00-07:00',
            'timeZone' => 'America/Los_Angeles',
          ),
          'end' => array(
            'dateTime' => '2020-07-12T17:00:00-07:00',
            'timeZone' => 'America/Los_Angeles',
          ),
          'recurrence' => array(
            'RRULE:FREQ=DAILY;COUNT=2'
          ),
          'attendees' => array(
            array('email' => 'lpage@example.com'),
            array('email' => 'sbrin@example.com'),
          ),
          'reminders' => array(
            'useDefault' => FALSE,
            'overrides' => array(
              array('method' => 'email', 'minutes' => 24 * 60),
              array('method' => 'popup', 'minutes' => 10),
            ),
          ),
        ));

        $this->event = $this->service->events->insert($this->calendarId, $this->event);
  }

  function gcal_get_events() {
      $this->client = $this->get_client();
      $this->service = new Google_Service_Calendar($this->client);

      // Print the next 10 events on the user's calendar.
      $this->optParams = array(
          'maxResults' => 10,
          'orderBy' => 'startTime',
          'singleEvents' => true,
          'timeMin' => date('c'),
      );

      return $this->service->events->listEvents($this->calendarId, $this->optParams)->getItems();
  }
}