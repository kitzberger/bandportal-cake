<?php

namespace App\Helper;

use it\thecsea\simple_caldav_client\CalDAVClient;
use Cake\Core\Configure;

class CalDAV
{
    private static $caldav;
    private static $calendar;
    private static $user;
    private static $pass;
    private static $url;

    private static function init()
    {
        if (self::$caldav == null) {
            $calendarConfig = Configure::read('Calendar');

            self::$calendar = $calendarConfig['calendar'];
            self::$user     = $calendarConfig['user'];
            self::$pass     = $calendarConfig['pass'];
            self::$url      = $calendarConfig['url'] . self::$user . '/' . self::$calendar . '/';

            self::$caldav = new CalDAVClient(self::$url, self::$user, self::$pass);
            self::$caldav->SetCalendar(self::$calendar);
        }
    }

    /**
     * PUT a (new) event to the remote CalDAV calendar
     *
     * @param  misc $date
     * @return string $uri
     */
    public static function putEvent($date)
    {
        if (empty($date)) {
            throw new \Exception('No $date given!');
        }

        self::init();

        $uri = 'bandportal-' . $date->id . '.ics';
        $url = self::$url . $uri;

        $event = self::buildEvent($date);
        $etag = self::$caldav->DoPUTRequest($url, $event);

        if ($etag) {
            return $uri;
        } else {
            throw new \Exception('Could not put event to remote server!');
        }
    }

    /**
     * DELETE a (given) event from the remote CalDAV calendar
     *
     * @param  misc $date
     * @return boolean
     */
    public static function deleteEvent($date)
    {
        if (empty($date)) {
            throw new \Exception('No $date given!');
        }

        if (empty($date->uri)) {
            throw new \Exception('Date has no uri property!');
        }

        self::init();

        $url = self::$url . $date->uri;

        $return = self::$caldav->DoDELETERequest($url);

        if ($return && substr((string) $return, 0, 2) == 20) {
            return true;
        } else {
            throw new \Exception('Could not delete event from remote server!');
        }
    }

    /**
     * GET a event from the remote CalDAV calendar
     *
     * @param  misc $date
     * @return string $uri
     */
    public static function getEvent($date)
    {
        if (empty($date)) {
            throw new \Exception('No $date given!');
        }

        self::init();

        $uri = 'bandportal-' . $date->id . '.ics';
        $url = self::$url . $uri;

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_USERPWD => self::$user . ':' . self::$pass,
        ]);
        $resp = curl_exec($curl);
        curl_getinfo($curl);
        curl_close($curl);

        if ($resp) {
            return [
                'uri' => $uri,
                'data' => $resp
            ];
        } else {
            throw new \Exception('Could not get event from remote server!');
        }
    }

    private static function buildEvent($date)
    {
        if (empty($date)) {
            throw new \Exception('No $date given!');
        }
        $ts = self::formatTime();

        $propertyNameStart = 'DTSTART';
        $propertyNameEnd = 'DTEND';
        if ($date['is_fullday']) {
            $propertyNameStart .= ';VALUE=DATE';
            $propertyNameEnd .= ';VALUE=DATE';
            $start = self::formatDate($date['begin']);
            $end = $date['end'] ?: $date['begin'];
            $end = strtotime((string) $end);
            $end = self::formatDate($end + 86400);
        } else {
            $start = self::formatTime($date['begin']);
            $end = self::formatTime($date['end'] ?: $date['begin']);
        }

        return 'BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Bandportal//CalDAV Client//EN
BEGIN:VEVENT
UID:bandportal-' . $date['id'] . '
DTSTAMP:' . $ts . '
' . $propertyNameStart . ':' . $start . '
' . $propertyNameEnd . ':' . $end . '
SUMMARY:' . trim($date['title'] . (isset($date['text'])&&$date['text'] ? "\nDESCRIPTION:" . str_replace("\r\n", "\\n", $date['text']) : '') . '
' . ($date['status']<0 ? 'STATUS:CANCELLED' : ($date['status']==1 ? 'STATUS:TENTATIVE' : ($date['status']==2 ? 'STATUS:CONFIRMED' : ''))) . '
' . ($date['location'] ? 'LOCATION:' . $date['location'] : '')) . '
END:VEVENT
END:VCALENDAR';
    }

    /**
     * Formats a timestamp into a format
     *
     * @param  mixed $ts
     * @param  string $format
     * @return string
     */
    private static function formatTime($ts = 0, $format = 'Ymd\THis')
    {
        if (is_int($ts)) {
            if ($ts == 0) {
                $ts = time();
            }
            // Example: 20150208T153000 a.k.a. halber viere am 08. Februar 2015
            $time = date($format, $ts);
            return $time;
        } elseif ($ts instanceof \Cake\I18n\FrozenTime) {
            return $ts->format($format);
        } elseif (strtotime((string) $ts) !== false) {
            $ts = strtotime((string) $ts);
            return date($format, $ts);
        } else {
            throw new \Exception('Unsupported type!');
        }
    }

    private static function formatDate($ts = 0, $format = 'Ymd')
    {
        return self::formatTime($ts, $format);
    }
}
