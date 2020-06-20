<?php

namespace Drupal\Tests\gatsby_orchestrator\Kernel\Mocks;

use Drupal\Core\Messenger\MessengerInterface;

/**
 *
 */
class MessengerMock implements MessengerInterface {

  public $messages = [];
  public $errors = [];

  /**
   *
   */
  public function addMessage($message, $type = self::TYPE_STATUS, $repeat = FALSE) {
    $this->messages[] = $message;
  }

  /**
   *
   */
  public function addStatus($message, $repeat = FALSE) {
  }

  /**
   *
   */
  public function addError($message, $repeat = FALSE) {
    $this->errors[] = $message;
  }

  /**
   *
   */
  public function addWarning($message, $repeat = FALSE) {
  }

  /**
   *
   */
  public function all() {
  }

  /**
   *
   */
  public function messagesByType($type) {
  }

  /**
   *
   */
  public function deleteAll() {
  }

  /**
   *
   */
  public function deleteByType($type) {
  }

}
