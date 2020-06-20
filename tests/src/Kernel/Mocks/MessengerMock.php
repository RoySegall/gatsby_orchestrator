<?php

namespace Drupal\Tests\gatsby_orchestrator\Kernel\Mocks;

use Drupal\Core\Messenger\MessengerInterface;

/**
 * A mock object to the messenger service.
 */
class MessengerMock implements MessengerInterface {

  /**
   * List of messages.
   *
   * @var array
   */
  public $messages = [];

  /**
   * List of errors.
   *
   * @var array
   */
  public $errors = [];

  /**
   * {@inheritDoc}
   */
  public function addMessage($message, $type = self::TYPE_STATUS, $repeat = FALSE) {
    $this->messages[] = $message;
  }

  /**
   * {@inheritDoc}
   */
  public function addStatus($message, $repeat = FALSE) {
  }

  /**
   * {@inheritDoc}
   */
  public function addError($message, $repeat = FALSE) {
    $this->errors[] = $message;
  }

  /**
   * {@inheritDoc}
   */
  public function addWarning($message, $repeat = FALSE) {
  }

  /**
   * {@inheritDoc}
   */
  public function all() {
  }

  /**
   * {@inheritDoc}
   */
  public function messagesByType($type) {
  }

  /**
   * {@inheritDoc}
   */
  public function deleteAll() {
  }

  /**
   * {@inheritDoc}
   */
  public function deleteByType($type) {
  }

}
