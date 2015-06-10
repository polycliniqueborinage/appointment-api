<?php

// https://sebastian-bergmann.de/archives/881-Testing-Your-Privates.html

namespace PolyCliniqueBorinage\Services;

use Carbon\Carbon;

class BookingServiceTest extends \PHPUnit_Framework_TestCase {

  /**
   * @covers BookingService::datesOverlap
   */
  public function testDatesOverlap() {

    $db = NULL;

    $booking_service = new BookingService($db);

    date_default_timezone_set("Europe/Brussels");

    // Test $date1 < $date2.
    $date1['carbon_start'] = Carbon::createFromFormat('H:i', '12:00');
    $date1['carbon_end'] = Carbon::createFromFormat('H:i', '12:10');
    $date2['carbon_start'] = Carbon::createFromFormat('H:i', '13:00');
    $date2['carbon_end'] = Carbon::createFromFormat('H:i', '13:10');

    // Assert
    $this->assertFalse($booking_service->datesOverlap($date1, $date2));

    // Test $date1 <= $date2.
    $date1['carbon_start'] = Carbon::createFromFormat('H:i', '12:00');
    $date1['carbon_end'] = Carbon::createFromFormat('H:i', '12:10');
    $date2['carbon_start'] = Carbon::createFromFormat('H:i', '12:10');
    $date2['carbon_end'] = Carbon::createFromFormat('H:i', '12:20');

    // Assert
    $this->assertFalse($booking_service->datesOverlap($date1, $date2));

    // Test $date1 > $date2.
    $date1['carbon_start'] = Carbon::createFromFormat('H:i', '13:00');
    $date1['carbon_end'] = Carbon::createFromFormat('H:i', '13:10');
    $date2['carbon_start'] = Carbon::createFromFormat('H:i', '12:10');
    $date2['carbon_end'] = Carbon::createFromFormat('H:i', '12:20');

    // Assert
    $this->assertFalse($booking_service->datesOverlap($date1, $date2));

    // Test $date1 >= $date2.
    $date1['carbon_start'] = Carbon::createFromFormat('H:i', '12:20');
    $date1['carbon_end'] = Carbon::createFromFormat('H:i', '12:40');
    $date2['carbon_start'] = Carbon::createFromFormat('H:i', '12:10');
    $date2['carbon_end'] = Carbon::createFromFormat('H:i', '12:20');

    // Assert
    $this->assertFalse($booking_service->datesOverlap($date1, $date2));


    // Test overlap.
    $date1['carbon_start'] = Carbon::createFromFormat('H:i', '12:00');
    $date1['carbon_end'] = Carbon::createFromFormat('H:i', '13:00');
    $date2['carbon_start'] = Carbon::createFromFormat('H:i', '12:30');
    $date2['carbon_end'] = Carbon::createFromFormat('H:i', '13:30');

    // Assert
    $this->assertTrue($booking_service->datesOverlap($date1, $date2));

    // Test overlap.
    $date1['carbon_start'] = Carbon::createFromFormat('H:i', '12:00');
    $date1['carbon_end'] = Carbon::createFromFormat('H:i', '13:00');
    $date2['carbon_start'] = Carbon::createFromFormat('H:i', '12:20');
    $date2['carbon_end'] = Carbon::createFromFormat('H:i', '12:40');

    // Assert
    $this->assertTrue($booking_service->datesOverlap($date1, $date2));

    // Test overlap.
    $date1['carbon_start'] = Carbon::createFromFormat('H:i', '12:00');
    $date1['carbon_end'] = Carbon::createFromFormat('H:i', '13:00');
    $date2['carbon_start'] = Carbon::createFromFormat('H:i', '11:30');
    $date2['carbon_end'] = Carbon::createFromFormat('H:i', '12:30');

    // Assert
    $this->assertTrue($booking_service->datesOverlap($date1, $date2));

    // Test overlap.
    $date1['carbon_start'] = Carbon::createFromFormat('H:i', '12:00');
    $date1['carbon_end'] = Carbon::createFromFormat('H:i', '13:00');
    $date2['carbon_start'] = Carbon::createFromFormat('H:i', '12:00');
    $date2['carbon_end'] = Carbon::createFromFormat('H:i', '13:00');

    // Assert
    $this->assertTrue($booking_service->datesOverlap($date1, $date2));
  }

  /**
   * @covers BookingService::isValidSlot
   */
  public function testisValidSlot() {

    $db = NULL;

    $booking_service = new BookingService($db);

    date_default_timezone_set("Europe/Brussels");

    // Test valid with one busy slot.
    $proposal['carbon_start'] = Carbon::createFromFormat('H:i', '12:00');
    $proposal['carbon_end'] = Carbon::createFromFormat('H:i', '13:00');

    $busy_slots[0]['carbon_start'] = Carbon::createFromFormat('H:i', '10:00');
    $busy_slots[0]['carbon_end'] = Carbon::createFromFormat('H:i', '11:00');

    // Assert.
    $this->assertTrue($booking_service->isValidSlot($proposal, $busy_slots));

    // Test valid with one multiple slot.
    $proposal['carbon_start'] = Carbon::createFromFormat('H:i', '12:00');
    $proposal['carbon_end'] = Carbon::createFromFormat('H:i', '13:00');

    $busy_slots[0]['carbon_start'] = Carbon::createFromFormat('H:i', '10:00');
    $busy_slots[0]['carbon_end'] = Carbon::createFromFormat('H:i', '11:00');
    $busy_slots[1]['carbon_start'] = Carbon::createFromFormat('H:i', '14:00');
    $busy_slots[1]['carbon_end'] = Carbon::createFromFormat('H:i', '15:00');
    $busy_slots[2]['carbon_start'] = Carbon::createFromFormat('H:i', '17:00');
    $busy_slots[2]['carbon_end'] = Carbon::createFromFormat('H:i', '18:00');

    // Assert.
    $this->assertTrue($booking_service->isValidSlot($proposal, $busy_slots));

    // Test non valid with one busy slot.
    $proposal['carbon_start'] = Carbon::createFromFormat('H:i', '12:00');
    $proposal['carbon_end'] = Carbon::createFromFormat('H:i', '13:00');

    $busy_slots[0]['carbon_start'] = Carbon::createFromFormat('H:i', '10:00');
    $busy_slots[0]['carbon_end'] = Carbon::createFromFormat('H:i', '14:00');

    // Assert.
    $this->assertFalse($booking_service->isValidSlot($proposal, $busy_slots));

    // Test non valid with multiple busy slot.
    $proposal['carbon_start'] = Carbon::createFromFormat('H:i', '10:00');
    $proposal['carbon_end'] = Carbon::createFromFormat('H:i', '10:10');

    $busy_slots[0]['carbon_start'] = Carbon::createFromFormat('H:i', '10:00');
    $busy_slots[0]['carbon_end'] = Carbon::createFromFormat('H:i', '11:00');
    $busy_slots[1]['carbon_start'] = Carbon::createFromFormat('H:i', '14:00');
    $busy_slots[1]['carbon_end'] = Carbon::createFromFormat('H:i', '15:00');
    $busy_slots[2]['carbon_start'] = Carbon::createFromFormat('H:i', '17:00');
    $busy_slots[2]['carbon_end'] = Carbon::createFromFormat('H:i', '18:00');

    // Assert.
    $this->assertFalse($booking_service->isValidSlot($proposal, $busy_slots));
  }

}
