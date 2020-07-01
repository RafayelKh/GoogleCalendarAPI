<?php

defined('ABSPATH') || exit;

require GCAL_ABSPATH . '/vendor/autoload.php';
require GCAL_ABSPATH . './class-gcal.php';

function gcal_push_event(){
    // Get the API client and construct the service object.
    $client = GoogleCal::get_client();
    $service = new Google_Service_Calendar($client);

    $event = new Google_Service_Calendar_Event(array(
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

    $calendarId = 'primary'
    $event = $service->events->insert($calendarId, $event);
}

function gcal_get_events(){
    $client = GoogleCal::get_client();
    $service = new Google_Service_Calendar($client);

    // Print the next 10 events on the user's calendar.
    $optParams = array(
        'maxResults' => 10,
        'orderBy' => 'startTime',
        'singleEvents' => true,
        'timeMin' => date('c') ,
    );

    $calendarId = 'primary'
    return $service->events->listEvents($calendarId, $optParams)->getItems();
}

