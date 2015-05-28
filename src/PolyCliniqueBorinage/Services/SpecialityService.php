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
    return $this->db->fetchAll("SELECT `id`, `value`, `label_fr`, `icon` FROM speciality WHERE `online_booking` = 1");
  }

  /**
   * @param int $id
   *   Speciality id
   *
   * @return array
   *  Return a speciality.
   */
  public function get($id) {
    return $this->db->fetchAssoc("SELECT `id`, `value`, `label_fr`, `icon` FROM speciality WHERE id = :id AND `online_booking` = 1", array(
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
    return $this->db->fetchAll("SELECT `user`.`id`, `user`.`firstname`, `user`.`familyname`, `user`.`gender`, `user`.`type`, `user`.`speciality`, `user`.`length_consult`, `user`.`inami` FROM `user` , `speciality` WHERE speciality.`value` = `user`.speciality AND speciality.id = :id", array(
        'id' => $id,
      )
    );
  }
}
