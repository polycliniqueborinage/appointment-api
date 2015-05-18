<?php

namespace PolyCliniqueBorinage\Services;

class BookingService extends BaseService {

  public function get($id) {
    return $this->db->fetchAssoc("SELECT * FROM speciality WHERE id = :id", array(
      'id' => $id,
      )
    );
  }
}
