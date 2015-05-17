<?php

namespace PolyCliniqueBorinage\Services;

class DoctorService extends BaseService {

  public function getAll() {
    return $this->db->fetchAll("SELECT id, firstname, familyname, gender, type, speciality, length_consult FROM user");
  }

  public function get($id) {
    return $this->db->fetchAssoc("SELECT * FROM user WHERE id = :id", array(
      'id' => $id,
      )
    );
  }

  function save($note) {
    $this->db->insert("user", $note);
    return $this->db->lastInsertId();
  }

  function update($id, $note) {
    return $this->db->update('user', $note, ['id' => $id]);
  }

  function delete($id) {
    return $this->db->delete("user", array("id" => $id));
  }

}
