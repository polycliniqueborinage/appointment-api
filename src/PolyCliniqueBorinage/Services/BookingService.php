<?php

namespace PolyCliniqueBorinage\Services;

const BOOKING_SERVICE_ENABLE = '#c5f7c5';
const BOOKING_SERVICE_BUSY = '#b5cbe7';
const BOOKING_SERVICE_OFFLINE = '#f8e9c6';

const BOOKING_SERVICE_FIRST_HOUR = '7:00';
const BOOKING_SERVICE_LAST_HOUR = '19:00';

use Carbon\Carbon;

class BookingService extends BaseService {

  /**
   * Save booking.
   *
   * http://silex.sensiolabs.org/doc/cookbook/json_request_body.html
   *
   * @param string $booking
   * @return array $slots
   *   Available slots.
   */
  function save($booking) {
    $this->db->insert("notes", $booking);
    return $this->db->lastInsertId();
  }

  /**
   * Update booking.
   *
   * @param string $id
   * @param string $booking
   * @return array $slots
   *   Available slots.
   */
  function update($id, $booking) {
    return $this->db->update('notes', $booking, ['id' => $id]);
  }

  /**
   * Delete booking.
   *
   * @param string $id
   */
  function delete($id) {
    return $this->db->delete("notes", array("id" => $id));
  }

  /**
   * Get all the slot available for a day.
   *
   * @param string $id
   * @param string $date
   * @return array $slots
   *   Available slots.
   */
  public function getAvailableSlotsByDate($id, $date) {

    $consult_length = 30;

    $date = Carbon::createFromFormat('Y-m-d', $date);

    // Events list.
    $events = $this->getEvents($id, $date);

    // Morning slot.
    $morning_slot = $this->getMorningSlot($id, $date);

    // Evening slot.
    $evening_slot = $this->getEveningSlot($id, $date);

    // Unavailable slots list.
    $unavailable_slots = $this->getBusySlots($id, $date);

    // All busy slots list.
    $all_busy_slots = array_merge($events, $morning_slot, $evening_slot, $unavailable_slots);

    // Slots candidate.
    $slots = array();

    // Available slots list.
    $available_slots = $this->getAllSlots($date, $consult_length);
    foreach($available_slots as $key => $slot_proposal) {
      if ($this ->isValidSlot($slot_proposal, $all_busy_slots)) {
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

    $consult_length = 30;

    $date = Carbon::createFromFormat('Y-m-d', $date);

    $slots = array();

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
  public function getAvailableSlotsByMonth($id, $date) {

    $consult_length = 30;

    $date = Carbon::createFromFormat('Y-m-d', $date);

    $slots = array();

    return $slots;
  }

  /**
   * Get all events for the a specific date and calendar.
   *
   * @param String $id
   * @param Carbon $date

   * @return array $events
   *   Events lists.
   */
  protected function getEvents($id, Carbon $date) {
    $events = $this->db->fetchAll("SELECT `start`, `end` FROM `" . $id . "` WHERE date = :date", array(
        'date' => $date->toDateString(),
      )
    );

    foreach($events as $key => $event) {
      $events[$key]['type'] = 'event';
      $events[$key]['carbon_start'] = Carbon::createFromFormat('Y-m-d H:i', $date->toDateString() . ' ' . str_replace("H", ":", $events[$key]['start']));
      $events[$key]['carbon_end'] = Carbon::createFromFormat('Y-m-d H:i', $date->toDateString() . ' ' . str_replace("H", ":", $events[$key]['end']));
      unset($events[$key]['start']);
      unset($events[$key]['end']);
    }

    return $events;
  }

  /**
   * Get all busy slots for the a specific date and calendar.
   *
   * @param String $id
   * @param Carbon $date

   * @return array $events
   *   Events lists.
   */
  protected function getBusySlots($id, Carbon $date) {
    $slots = $this->db->fetchAll("SELECT `id`, `day` FROM `horaire_presence_" . $id . "` WHERE color != :color AND day = :day", array(
        'color' => BOOKING_SERVICE_ENABLE,
        'day' => $date->format('w'),
      )
    );
    foreach($slots as $key => $slot) {
      $slots[$key]['type'] = 'busy_slot';
      $slots[$key]['carbon_start'] = Carbon::createFromFormat('Y-m-d H:i', $date->toDateString() . ' ' . str_replace("H", ":", $slots[$key]['id']));
      $slots[$key]['carbon_end'] = Carbon::createFromFormat('Y-m-d H:i', $date->toDateString() . ' ' . str_replace("H", ":", $slots[$key]['id']))->addMinutes(10);
      unset($slots[$key]['id']);
      unset($slots[$key]['day']);
    }
    return $slots;
  }

  /**
   * Get all available slots for the a specific date and calendar.
   *
   * @param String $id
   * @param Carbon $date
   * @param int $consult_length
   *
   * @return array $events
   *   Events lists.
   */
  protected function getAvailableSlots($id, Carbon $date, $consult_length) {
    $slots = $this->db->fetchAll("SELECT `id`, `day` FROM `horaire_presence_" . $id . "` WHERE color = :color AND day = :day", array(
        'color' => BOOKING_SERVICE_ENABLE,
        'day' => $date->format('w'),
      )
    );
    foreach($slots as $key => $slot) {
      $slots[$key]['carbon_start'] = Carbon::createFromFormat('H:i', str_replace("H", ":", $slots[$key]['id']));
      $slots[$key]['carbon_end'] = Carbon::createFromFormat('H:i', str_replace("H", ":", $slots[$key]['id']))->addMinutes($consult_length);
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
      $slots[$i]['carbon_start'] = @clone $carbon_start;
      $slots[$i]['carbon_end'] = @clone $carbon_end->addMinutes($consult_length);
      $start->addMinutes(5);
      $i++;
    }
    return $slots;
  }

  /**
   * Get morning slot.
   *
   * @param String $id
   * @param Carbon $date
   *
   * @return array $slot
   *   Return morning slot.
   */
  protected function getMorningSlot($id, Carbon $date) {
    $slot[0]['type'] = 'morning';
    $slot[0]['carbon_start'] = Carbon::createFromFormat('Y-m-d H:i', $date->toDateString() . ' ' . '0:00');
    $slot[0]['carbon_end'] = Carbon::createFromFormat('Y-m-d H:i', $date->toDateString() . ' ' .BOOKING_SERVICE_FIRST_HOUR);
    return $slot;
  }

  /**
   * Get evening slot.
   *
   * @param String $id
   * @param Carbon $date
   *
   * @return array $slot
   *   Return evening slot.
   */
  protected function getEveningSlot($id, Carbon $date) {
    $slot[0]['type'] = 'evening';
    $slot[0]['carbon_start'] = Carbon::createFromFormat('Y-m-d H:i', $date->toDateString() . ' ' . BOOKING_SERVICE_LAST_HOUR);
    $slot[0]['carbon_end'] = Carbon::createFromFormat('Y-m-d H:i', $date->toDateString() . ' ' . '24:00');
    return $slot;
  }

  /**
   * Check if a slot if a possible candidate.
   * @param Carbon $date_one
   * @param Carbon $date_two
   *
   * @return bool
   *   Wether or not the date intervals overlap
   */
  protected function isValidSlot($proposed_slot, $busy_slots) {
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
   * Check wether the date intervals overlapse.
   * @param Carbon $date1
   * @param Carbon $date2
   *
   * @return bool
   *   Wether or not the date intervals overlap
   */
  protected function datesOverlap($date1, $date2) {
    $start1 = $date1['carbon_start'];
    $end1 = $date1['carbon_end'];
    $start2 = $date2['carbon_start'];
    $end2 = $date2['carbon_end'];
    return !($end1 <= $start2 or $end2 <= $start1);
  }

}
