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
	 *
	 * @var Object
	 */
	private $client;

	/**
	 * Instance of Google_Service_Calendar.
	 *
	 * @var Object
	 */
	private $service;

	/**
	 * Authorized token file path.
	 *
	 * @var String
	 */
	private $token_path;

	/**
	 * Google API redirect URL
	 *
	 * @var String
	 */
	private $redirect_uri;

	/**
	 * Class instance
	 *
	 * @var Object
	 */
	private static $instance;

	/**
	 * Init authorized API client.
	 *
	 * @throws Exception If some problem appeared during API requests.
	 */
	private function __construct() {
		try {
			$this->token_path   = GCAL_ABSPATH . 'token.json';
			$this->redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/testing.php';

			$this->client = new Google_Client();
			$this->client->setRedirectUri( $this->redirect_uri );
			$this->client->setApplicationName( 'Google Calendar API WP Plugin' );
			$this->client->setScopes( Google_Service_Calendar::CALENDAR );
			$this->client->setAuthConfig( GCAL_ABSPATH . '/credentials.json' );
			$this->client->setAccessType( 'offline' );
			$this->client->setPrompt( 'select_account consent' );

			if ( file_exists( $this->token_path ) ) {
				$access_token = json_decode( file_get_contents( $this->token_path ), true );
				if ( ! empty( $access_token ) ) {
					$this->client->setAccessToken( $access_token );
				}
			}

			/** If there is no previous token or it's expired. */
			if ( $this->client->isAccessTokenExpired() ) {
				$this->update_access_token();
			}

			/** Init service object */
			$this->service = new Google_Service_Calendar( $this->client );
		} catch ( Exception $e ) {
			/** TODO: Log errors */
			echo 'ERROR';
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
	 * Get access token from refresh one
	 * Or Generate token and write it to the file.
	 *
	 * @throws Exception If some problem appeared during API requests.
	 */
	private function update_access_token() {
		try {
			if ( $this->client->getRefreshToken() ) {
				$this->client->fetchAccessTokenWithRefreshToken( $this->client->getRefreshToken() );
			} else {
				/** Request authorization from the user. */
				$auth_url = $this->client->createAuthUrl();
				printf( "Please Verify your account: \n%s\n", $auth_url );

				$auth_code = ! empty( $_GET['code'] ) ? $_GET['code'] : '';

				/** Exchange authorization code for an access token. */
				$access_token = $this->client->fetchAccessTokenWithAuthCode( $auth_code );
				$this->client->setAccessToken( $access_token );

				// Check to see if there was an error.
				if ( array_key_exists( 'error', $access_token ) ) {
					throw new Exception( join( ', ', $access_token ) );
				}
			}

			file_put_contents( $this->token_path, json_encode( $this->client->getAccessToken() ) );

		} catch ( Exception $e ) {
			/** TODO: Log errors */
			echo 'ERROR --------------------------';
			var_dump( $e );
		}
	}

	/**
	 * Push events to Google Calendar
	 */
	public function push_events( $events ) {
		try {
			if ( $this->client->isAccessTokenExpired() ) {
				$this->update_access_token( $this->token_path );
			}

			/** TODO: Change event form function argument. */
			$event = new Google_Service_Calendar_Event(
				array(
					'summary'     => 'Google I/O 2015',
					'location'    => '800 Howard St., San Francisco, CA 94103',
					'description' => 'A chance to hear more about Google\'s developer products.',
					'start'       => array(
						'dateTime' => '2020-07-12T09:00:00-07:00',
						'timeZone' => 'America/Los_Angeles',
					),
					'end'         => array(
						'dateTime' => '2020-07-12T17:00:00-07:00',
						'timeZone' => 'America/Los_Angeles',
					),
					'recurrence'  => array(
						'RRULE:FREQ=DAILY;COUNT=2',
					),
					'attendees'   => array(
						array(
							'email' => 'lpage@example.com',
						),
						array(
							'email' => 'sbrin@example.com',
						),
					),
					'reminders'   => array(
						'useDefault' => false,
						'overrides'  => array(
							array(
								'method'  => 'email',
								'minutes' => 24 * 60,
							),
							array(
								'method'  => 'popup',
								'minutes' => 10,
							),
						),
					),
				)
			);

			$calendar_id = 'primary';
			$result      = $this->service->events->insert( $calendar_id, $event );

			return $result;
		} catch ( Exception $e ) {
			echo 'ERROR';
			var_dump( $e );
		}
	}

	/**
	 * Get events from Google Calendar
	 */
	public function get_events() {
		try {
			if ( $this->client->isAccessTokenExpired() ) {
				$this->update_access_token( $this->token_path );
			}

			// Print the next 10 events on the user's calendar.
			/** TODO: Change params form function argument. */
			$opt_params = array(
				'maxResults'   => 10,
				'orderBy'      => 'startTime',
				'singleEvents' => true,
				'timeMin'      => date( 'c' ),
			);

			$calendar_id = 'primary';
			return $this->service->events->listEvents( $calendar_id, $opt_params )->getItems();
		} catch ( Exception $e ) {
			echo 'ERROR';
			var_dump( $e );
		}
	}
}
