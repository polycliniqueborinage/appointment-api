<?php

namespace PolyCliniqueBorinage\Services;


class UserService extends BaseService {

  /* var $test = new \Doctrine\DBAL\Connection() */
  /* $test->executeQuery() */

  /**
   * @param string $id
   *   The id
   *
   * @return array
   *  Return null or array
   */
  public function get($id) {
    return $this->db->fetchAssoc("SELECT `id`, `firstname`, `lastname`, `token`, `role`, `status` FROM `authentification` WHERE `id` = :id", array(
        'id' => $id
      )
    );
  }

  /**
   * @param string $username
   *   The username
   * @param string $password
   *   The password
   *
   * @return array
   *  Return null or array
   */
  public function getByCredential($username, $password) {
    return $this->db->fetchAssoc("SELECT `id`, `token`, `role`, `status` FROM `authentification` WHERE `username` = :username AND `password` = :password ", array(
        'username' => $username,
        'password' => $password,
      )
    );
  }

  /**
   * @param array $user
   *   The user details
   *
   * @return array $user
   *  Return null or array
   */
  public function create($user) {
    $this->db->insert('`authentification`', $user);
    $user_id = $this->db->lastInsertId();
    return $user_id;
  }

}
