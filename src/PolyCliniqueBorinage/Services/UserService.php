<?php

namespace PolyCliniqueBorinage\Services;


class UserService extends BaseService {

  /* var $test = new \Doctrine\DBAL\Connection() */
  /* $test->executeQuery() */

  /**
   * @param string $username
   *   The username
   * @param string $password
   *   The password
   *
   * @return array
   *  Return null or array
   */
  public function get($username, $password) {
    return $this->db->fetchAssoc("SELECT `id`, `token`, `role`, `status` FROM `authentification` WHERE `username` = :username AND `password` = :password ", array(
        'username' => $username,
        'password' => $password,
      )
    );
  }

}
