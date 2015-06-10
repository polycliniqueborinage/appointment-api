<?php

/*
 * #alter table user convert to character set utf8 collate utf8_general_ci;
#alter table speciality convert to character set utf8 collate utf8_general_ci;
SELECT user.ID FROM `user` , `speciality` WHERE speciality.`value` = `user`.speciality AND speciality.id = 1
 */

namespace PolyCliniqueBorinage\Services;

class SpecialityService extends BaseService {

  /**
   * @return array
   *  Return all specialities.
   */
  public function getAll() {
    return $this->db->fetchAll("SELECT `id`, `specialite` as `value`, `icon` FROM `specialites` WHERE `online_booking` = 1 ORDER BY `specialite` ASC");
  }

  /**
   * @param int $id
   *   Speciality id
   *
   * @return array
   *  Return a speciality.
   */
  public function get($id) {
    return $this->db->fetchAssoc("SELECT `id`, `specialite` as `value`, `icon` FROM `specialites` WHERE id = :id AND `online_booking` = 1", array(
      'id' => $id,
      )
    );
  }

  /**
   * @param int $id
   *   Speciality id
   *
   * @return array
   *  Return all doctors for the current speciality.
   */
  public function getDoctors($id) {
    return $this->db->fetchAll("SELECT `medecins`.`id`, `medecins`.`nom` as `familyname`, `medecins`.`prenom` as `firstname`, `medecins`.`length_consult` FROM `medecins` , `specialites` WHERE specialites.`id` = `medecins`.specialite AND `medecins`.`type` = 'interne' and `medecins`.`agenda` ='checked' AND `medecins`.`online_booking` = 1 AND specialites.`id` = :id", array(
        'id' => $id,
      )
    );
  }
}
