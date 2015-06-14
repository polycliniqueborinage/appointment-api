<?php

namespace PolyCliniqueBorinage\Services;

const BOOKING_SERVICE_ENABLE = '#c5f7c5';
const BOOKING_SERVICE_BUSY = '#b5cbe7';
const BOOKING_SERVICE_OFFLINE = '#f8e9c6';

const BOOKING_SERVICE_FIRST_HOUR = '7:00';
const BOOKING_SERVICE_LAST_HOUR = '19:00';

use Carbon\Carbon;
use Symfony\Component\Security\Core\Util\SecureRandom;

class BookingService extends BaseService {

  /* var $test = new \Doctrine\DBAL\Connection() */
  /* $test->executeQuery() */

  /**
   * Save an event.
   *
   * @param int $id
   *   The agenda id being the inami number.
   * @param string $start
   *   The start datetime.
   * @param string $end
   *   The end datetime.
   *
   * @return array|FALSE
   *   Return the booking if succeed or FALSE if not.
   *
   */
  public function save($id, $start, $end) {

    // Make sur the table exist.
    if (!$this->tablesExist($id)) {
      return FALSE;
    }

    $carbon_start = new Carbon($start);
    $carbon_end = new Carbon($end);

    $slot_proposal = array();
    $slot_proposal['start'] = $carbon_start;
    $slot_proposal['end'] = $carbon_end;

    // Make sur the slot is still vacant.
    $all_busy_slots = $this->getAllBusySlots($id, $carbon_start);

    // For now only allow Ponchon calendar when doing the test.
    if ($id === '11111111111' && $this->isValidSlot($slot_proposal, $all_busy_slots)) {
      // @todo : Add transaction stuff.
      $events = $this->getEvents($id, $carbon_start);

      // Save in the old calendar.
      $slot_old_format = $this->getSlotOldFormat($carbon_start, $carbon_end, $events['length']);
      $this->db->insert('`' . $id . '`', $slot_old_format);
      $event_id = $this->db->lastInsertId();

      // Save in the new calendar.
      $slot_new_format = $this->getSlotNewFormat($carbon_start, $carbon_end, $event_id);
      $this->db->insert('`agenda`', $slot_new_format);
      $event_id = $this->db->lastInsertId();

      // Return the event.
      return $this->get($event_id);
    }
    else {
      return FALSE;
    }

  }

  /**
   * Delete an event.
   *
   * @param string $token
   *   The event token
   *
   * @return array|FALSE
   *   Return the booking if succeed or FALSE if not.
   *
   */
  public function delete($token) {
  }

  /**
   * Get an event.
   *
   * @param int $id
   *   The event id
   *
   * @return array|FALSE
   *   Return the event if succeed or FALSE if not.
   *
   */
  public function get($id) {
    $result = $this->db->fetchAssoc("SELECT `start`, `end`, `token` FROM `agenda` WHERE id = :id", array(
        'id' => $id,
      )
    );
    return $result;
  }

  /**
   * Get weekly calendar.
   *
   * @param int $id
   *   The agenda id.
   *
   * @return array
   *   Return weekly calendar.
   */
  public function getWeeklyCalendar($id) {

    $week = array();
    for ($i = 0 ;$i < 7; $i++) {
      $slots = $this->getAvailableSlots($id, $i);
      $key = 0;
      $reset = TRUE;

      foreach ($slots as $slot) {
        // If slot are not next to each other.
        if (isset($week[$i][$key]['end']) && $slot['start']->ne($week[$i][$key]['end'])) {
          $key ++;
          $reset = TRUE;
        }

        if ($reset) {
          $week[$i][$key]['start'] = $slot['start'];
        }
        $week[$i][$key]['end'] = $slot['end'];

        $reset = FALSE;
      }
    }

    return $week;
  }

  /**
   * Get all the slots available for a day.
   *
   * @param int $id
   *   The agenda id.
   * @param string $date
   *   The start datetime.
   * @param int $consult_length
   *   The length of the slot.
   *
   * @return array $slots
   *   Available slots for the day.
   */
  public function getAvailableSlotsByDate($id, $date, $consult_length = 15) {

    $date = Carbon::createFromFormat('Y-m-d', $date);

    $all_busy_slots = $this->getAllBusySlots($id, $date);

    // Slots candidate.
    $slots = array();

    // Available slots list.
    $available_slots = $this->getAllSlots($date, $consult_length);

    foreach($available_slots as $key => $slot_proposal) {
      if ($this->isValidSlot($slot_proposal, $all_busy_slots)) {
        $all_busy_slots[] = $slot_proposal;
        $slots[] = $slot_proposal;
      }
    }
    return $slots;
  }

  /**
   * Get all the slot available for a week.
   *
   * @param string $id
   * @param string $date
   * @return array $slots
   *   Available slots.
   */
  public function getAvailableSlotsByWeek($id, $date) {
    $date = Carbon::createFromFormat('Y-m-d', $date);

    $slots = array();

    $month = Carbon::create($date->year, $date->month, 1, 0, 0, 0);
    while ($month->weekOfYear == $date->weekOfYear) {
      if ($this->getAvailableSlotsByDate($id, $month->toDateString())) {
        $slots[] = @clone $month;
      }
      $month->addDay();
    }

    return $slots;
  }

  /**
   * Get all the slot available for a month.
   *
   * @param string $id
   * @param string $date
   * @return array $slots
   *   Available slots.
   */
  public function getAvailableSlotsByMonth($id, $date) {
    $date = Carbon::createFromFormat('Y-m-d', $date);
    $month = Carbon::create($date->year, $date->month, 1, 0, 0, 0);
    $slots = array();
    $i = 0;

    while ($month->month == $date->month) {
      if ($this->getAvailableSlotsByDate($id, $month->toDateString())) {
        $slots[$i]['start'] = @clone $month;
        $month->addDay();
        $slots[$i++]['end'] = @clone $month;
      } else {
        $month->addDay();
      }
    }

    return $slots;
  }









  /**
   *  GET SLOTS METHODS.
   */

  /**
   * Get all events for the a specific calendar and date.
   *
   * @param int $id
   *   The agenda id.
   * @param Carbon $date
   *   The carbon date.
   *
   * @return array $events
   *   Events lists and event statistic.
   */
  protected function getEvents($id, Carbon $date) {
    $results = $this->db->fetchAll("SELECT `start`, `end`, `midday`, `length` FROM `" . $id . "` WHERE date = :date", array(
        'date' => $date->toDateString(),
      )
    );

    $events = array();
    $events['count'] = 0;
    $events['length'] = 0;
    $events['events'] = array();

    if (!empty($results)) {
      foreach($results as $key => $result) {
        $events['count'] ++;
        $events['length'] += $result['length'];
        $events['events'][$key]['type'] = 'event';
        $events['events'][$key]['start'] = Carbon::createFromFormat('Y-m-d H:i', $date->toDateString() . ' ' . str_replace("H", ":", $result['start']));
        $events['events'][$key]['end'] = Carbon::createFromFormat('Y-m-d H:i', $date->toDateString() . ' ' . str_replace("H", ":", $result['end']));
      }
    }

    return $events;
  }

  /**
   * Returns the morning slot.
   *
   * @param int $id
   *   The agenda id.
   * @param Carbon $date
   *   The carbon date.
   *
   * @return array $slot
   *   Returns morning slot.
   */
  protected function getMorningSlot($id, Carbon $date) {
    $slot[0]['type'] = 'morning';
    $slot[0]['start'] = Carbon::createFromFormat('Y-m-d H:i', $date->toDateString() . ' ' . '0:00');
    $slot[0]['end'] = Carbon::createFromFormat('Y-m-d H:i', $date->toDateString() . ' ' .BOOKING_SERVICE_FIRST_HOUR);
    return $slot;
  }

  /**
   * Returns evening slot.
   *
   * @param int $id
   *   The agenda id.
   * @param Carbon $date
   *   The carbon date.
   *
   * @return array $slot
   *   Returns evening slot.
   */
  protected function getEveningSlot($id, Carbon $date) {
    $slot[0]['type'] = 'evening';
    $slot[0]['start'] = Carbon::createFromFormat('Y-m-d H:i', $date->toDateString() . ' ' . BOOKING_SERVICE_LAST_HOUR);
    $slot[0]['end'] = Carbon::createFromFormat('Y-m-d H:i', $date->toDateString() . ' ' . '24:00');
    return $slot;
  }

  /**
   * Returns past slot.
   *
   * @return array $slot
   *   Returns past slot.
   */
  protected function getPastSlot() {
    $slot[0]['type'] = 'past';
    $slot[0]['start'] = Carbon::createFromFormat('Y-m-d H:i', '1978-05-29 00:00');
    $slot[0]['end'] = Carbon::createFromFormat('Y-m-d H:i', date('Y-m-d') . ' ' . '24:00');
    return $slot;
  }

  /**
   * Get all the unavailable slots for the a specific calendar and date.
   *
   * @param int $id
   *   The agenda id.
   * @param Carbon $date
   *   The carbon date.
   *
   * @return array $slots
   *   Returns the unavailable slots.
   */
  protected function getUnAvailableSlots($id, Carbon $date) {
    $slots = $this->db->fetchAll("SELECT `id`, `day` FROM `horaire_presence_" . $id . "` WHERE color != :color AND day = :day", array(
        'color' => BOOKING_SERVICE_ENABLE,
        'day' => $date->format('w'),
      )
    );
    foreach($slots as $key => $slot) {
      $slots[$key]['type'] = 'unavailable';
      $slots[$key]['start'] = Carbon::createFromFormat('Y-m-d H:i', $date->toDateString() . ' ' . str_replace("H", ":", $slots[$key]['id']));
      $slots[$key]['end'] = Carbon::createFromFormat('Y-m-d H:i', $date->toDateString() . ' ' . str_replace("H", ":", $slots[$key]['id']))->addMinutes(10);
      unset($slots[$key]['id']);
      unset($slots[$key]['day']);
    }
    return $slots;
  }

  /**
   * Get all the busy slots for the a specific calendar and date.
   * This include the events, morning, evening, past and unavailable slots.
   *
   * @param int $id
   *   The agenda id.
   * @param Carbon $date
   *   The carbon date.
   *
   * @return array $slots
   *   Returns the busy slots.
   */
  protected function getAllBusySlots($id, Carbon $date) {
    // Events list.
    $events = $this->getEvents($id, $date);

    // Morning slot.
    $morning_slot = $this->getMorningSlot($id, $date);

    // Evening slot.
    $evening_slot = $this->getEveningSlot($id, $date);

    // Past slot.
    $past_slot = $this->getPastSlot();

    // Unavailable slots list.
    $unavailable_slots = $this->getUnAvailableSlots($id, $date);

    // All busy slots list.
    $slots = array_merge($events['events'], $morning_slot, $evening_slot, $past_slot, $unavailable_slots);
    return $slots;
  }

  /**
   * Get all available slots for the a specific date and calendar.
   *
   * @param String $id
   * @param int $week_day
   *
   * @return array $events
   *   Events lists.
   */
  protected function getAvailableSlots($id, $week_day) {
    $slots = $this->db->fetchAll("SELECT `id`, `day` FROM `horaire_presence_" . $id . "` WHERE color = :color AND day = :day", array(
        'color' => BOOKING_SERVICE_ENABLE,
        'day' => $week_day,
      )
    );
    foreach($slots as $key => $slot) {
      $slots[$key]['start'] = Carbon::createFromFormat('H:i', str_replace("H", ":", $slots[$key]['id']));
      $slots[$key]['end'] = Carbon::createFromFormat('H:i', str_replace("H", ":", $slots[$key]['id']))->addMinutes(10);
    }
    return $slots;
  }

  /**
   * Get all available slots for the a specific date and calendar.
   *
   * @param Carbon $date
   * @param int $consult_length
   *
   * @return array $events
   *   Events lists.
   */
  protected function getAllSlots(Carbon $date, $consult_length) {
    $slots = array();
    $start = Carbon::createFromFormat('Y-m-d H:i', $date->toDateString() . ' ' .BOOKING_SERVICE_FIRST_HOUR);
    $end = Carbon::createFromFormat('Y-m-d H:i', $date->toDateString() . ' ' .BOOKING_SERVICE_LAST_HOUR);

    $i  = 0;
    while ($start->lt($end)) {
      $carbon_start = @clone $start;
      $carbon_end = @clone $start;
      $slots[$i]['start'] = @clone $carbon_start;
      $slots[$i]['end'] = @clone $carbon_end->addMinutes($consult_length);
      $start->addMinutes(5);
      $i++;
    }
    return $slots;
  }









  /**
   *  HELPERS METHODS.
   */

  /**
   * Check if a slot if a possible candidate.
   *
   * @param Carbon $proposed_slot
   * @param array $busy_slots
   *
   * @return bool
   *   Whether or not the slot is valid.
   */
  private function isValidSlot($proposed_slot, $busy_slots) {
    if (!empty($busy_slots)) {
      foreach ($busy_slots as $busy_slot) {
        if ($this->datesOverlap($proposed_slot, $busy_slot)) {
          return FALSE;
        }
      }
    }
    return TRUE;
  }

  /**
   * Check whether the date intervals overlapse.
   * @param Carbon $date1
   * @param Carbon $date2
   *
   * @return bool
   *   Whether or not the date intervals overlap.
   */
  private function datesOverlap($date1, $date2) {
    $start1 = $date1['start'];
    $end1 = $date1['end'];
    $start2 = $date2['start'];
    $end2 = $date2['end'];
    return !($end1 <= $start2 or $end2 <= $start1);
  }

  /**
   * Get the the available slot in the old poly format.
   *
   * For info 5 minutes slot is 18 pixels height.
   *
   * @param Carbon $carbon_start
   *   Carbon format start datetime
   * @param Carbon $carbon_end
   *   Carbon format end datetime
   * @param int $events_length
   *  The length (in pixel) of all events.
   * @return array $slot
   *   Return old format slot.
   */
  private function getSlotOldFormat(Carbon $carbon_start, Carbon $carbon_end, $events_length) {

    $secondsMidnight7h = 7 * 3600;
    $secondsMidnight13h = 13 * 3600;

    $slot = array();
    $slot['caisse'] = 'online';
    $slot['new'] = 'std';
    $slot['date'] = $carbon_start->toDateString();
    $slot['start'] = str_replace(":", "H", $carbon_start->format('H:i'));
    $slot['end'] = str_replace(":", "H", $carbon_end->format('H:i'));
    $slot['length'] = 3/50 * ($carbon_end->secondsSinceMidnight() - $carbon_start->secondsSinceMidnight());

    if ($secondsMidnight13h >= $carbon_start->secondsSinceMidnight()) {
      $midday = 'morning';
      $slot['midday'] = $midday;
      $slot['position'] = ( 3/50 * ($carbon_start->secondsSinceMidnight() - $secondsMidnight7h));
      $slot['id'] = $midday . ( 3/50 * ($carbon_start->secondsSinceMidnight() - $secondsMidnight7h));
      $slot['top'] = $slot['position'] - $events_length;
    } else {
      $midday = 'afternoon';
      $slot['midday'] = $midday;
      $slot['position'] = ( 3/50 * ($carbon_start->secondsSinceMidnight() - $secondsMidnight13h));
      $slot['id'] = $midday . ( 3/50 * ($carbon_start->secondsSinceMidnight() - $secondsMidnight13h));
      $slot['top'] = $slot['position'] - $events_length;
    }

    return $slot;
  }

  /**
   * Get the the available slot in the old poly format.
   *
   * For info 5 minutes slot is 18 pixels height.
   *
   * @param Carbon $carbon_start
   *   Carbon format start datetime
   * @param Carbon $carbon_end
   *   Carbon format end datetime
   * @param int $id
   *  The id in the old calendar
   * @return array $slot
   *   Return old format slot.
   */
  private function getSlotNewFormat(Carbon $carbon_start, Carbon $carbon_end, $id) {
    $generator = new SecureRandom();
    $random = $generator->nextBytes(64);
    $token = bin2hex($random);

    $slot['start'] = $carbon_start->toDateTimeString();
    $slot['end'] = $carbon_end->toDateTimeString();
    $slot['agenda_id'] = $id;
    $slot['event_id'] = $id;
    $slot['user_id'] = $id;
    $slot['token'] = $token;

    return $slot;
  }

}
