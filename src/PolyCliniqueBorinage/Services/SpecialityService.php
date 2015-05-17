<?php

namespace PolyCliniqueBorinage\Services;

class SpecialityService extends BaseService {

  public function getAll() {
    return $this->db->fetchAll("SELECT * FROM speciality");
  }

  public function get($id) {
    return $this->db->fetchAssoc("SELECT * FROM speciality WHERE id = :id", array(
      'id' => $id,
      )
    );
  }
}
