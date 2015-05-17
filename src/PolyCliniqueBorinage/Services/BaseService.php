<?php

namespace PolyCliniqueBorinage\Services;

class BaseService {
  protected $db;

  public function __construct($db) {
    $this->db = $db;
  }
}
