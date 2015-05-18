<?php

namespace PolyCliniqueBorinage\Services;

const BOOKING_ENABLE = '#c5f7c5';
const BOOKING_BUSY = '#b5cbe7';
const BOOKING_OFFLINE = '#f8e9c6';

const BOOKING_MONDAY = '1';
const BOOKING_TUESDAY = '2';
const BOOKING_WEDNESDAY = '3';
const BOOKING_THURSDAY = '4';
const BOOKING_FRIDAY = '5';
const BOOKING_SATERDAY = '6';
const BOOKING_SUNDAY = '0';

use Carbon\Carbon;


class BookingService extends BaseService {

  public function get($id) {

    $consult_length = 15;

    $date = Carbon::today();

    $events = $this->db->fetchAll("SELECT `start`, `end` FROM `11111111111` WHERE date = :date", array(
        'date' => $date->toDateString(),
      )
    );
    print_r($events);

    $slots = $this->db->fetchAll("SELECT `id`, `day` FROM `horaire_presence_11111111111` WHERE color = :color AND day = :day", array(
        'color' => BOOKING_ENABLE,
        'day' => $date->format('w'),
      )
    );
    print_r($slots);

    return $this->db->fetchAll("SELECT * FROM horaire_presence_11111111111 WHERE color = :color", array(
        'color' => BOOKING_ENABLE,
      )
    );


  }
}
