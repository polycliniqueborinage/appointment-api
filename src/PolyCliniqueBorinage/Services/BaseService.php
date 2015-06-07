<?php

namespace PolyCliniqueBorinage\Services;

class BaseService {
  protected $db;

  public function __construct($db) {
    $this->db = $db;
  }

  /**
   *
   */
  public function tablesExist($table) {
    $schemaManager = $this->db->getSchemaManager();
    if ($schemaManager->tablesExist(array($table)) === TRUE) {
      return TRUE;
    }
    return FALSE;
  }

}
