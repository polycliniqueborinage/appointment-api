<?php

namespace PolyCliniqueBorinage\Services;

class DoctorService extends BaseService {

  public function getAll() {
    return $this->db->fetchAll("SELECT `id`, `nom` as `familyname`, `prenom` as `firstname`, `length_consult`, `inami`, `specialite` as `speciality` FROM `medecins` WHERE `type` = 'interne' AND `agenda` ='checked' AND `online_booking` = 1 ORDER BY `familyname`, `firstname`");
  }

  public function get($id) {
    return $this->db->fetchAssoc("SELECT `id`, `nom` as `familyname`, `prenom` as `firstname`, `length_consult`, `inami`, `specialite` as `speciality` FROM `medecins` WHERE id = :id AND `type` = 'interne' AND `agenda` ='checked' AND `online_booking` = 1", array(
      'id' => $id,
      )
    );
  }

}
