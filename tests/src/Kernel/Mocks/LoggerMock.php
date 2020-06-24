<?php

namespace Drupal\Tests\gatsby_orchestrator\Kernel\Mocks;

use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Session\AccountInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Mocking the logger service.
 */
class LoggerMock implements LoggerChannelInterface {

  /**
   * List of errors.
   *
   * @var array
   */
  public $errors = [];

  /**
   * List of info.
   *
   * @var array
   */
  public $info = [];

  /**
   * {@inheritDoc}
   */
  public function setRequestStack(RequestStack $requestStack = NULL) {
  }

  /**
   * {@inheritDoc}
   */
  public function setCurrentUser(AccountInterface $current_user = NULL) {
  }

  /**
   * {@inheritDoc}
   */
  public function setLoggers(array $loggers) {
  }

  /**
   * {@inheritDoc}
   */
  public function addLogger(LoggerInterface $logger, $priority = 0) {
  }

  /**
   * {@inheritDoc}
   */
  public function emergency($message, array $context = []) {
  }

  /**
   * {@inheritDoc}
   */
  public function alert($message, array $context = []) {
  }

  /**
   * {@inheritDoc}
   */
  public function critical($message, array $context = []) {
  }

  /**
   * {@inheritDoc}
   */
  public function error($message, array $context = []) {
    $this->errors[] = $message;
  }

  /**
   * {@inheritDoc}
   */
  public function warning($message, array $context = []) {
  }

  /**
   * {@inheritDoc}
   */
  public function notice($message, array $context = []) {
  }

  /**
   * {@inheritDoc}
   */
  public function info($message, array $context = []) {
    $this->info[] = $message;
  }

  /**
   * {@inheritDoc}
   */
  public function debug($message, array $context = []) {
  }

  /**
   * {@inheritDoc}
   */
  public function log($level, $message, array $context = []) {
  }

}
