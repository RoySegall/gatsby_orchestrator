<?php

namespace Drupal\Tests\gatsby_orchestrator\Kernel;

use Drupal\Core\Config\Config;
use Drupal\Core\Messenger\Messenger;
use Drupal\gatsby_orchestrator\GatsbyOrchestratorGatsbyHealth;
use Psr\Log\LoggerInterface;

/**
 * Boilerplate mocks objects.
 *
 * This class holds all the mock helpers that we need for mocking elements.
 *
 * @package Drupal\Tests\gatsby_orchestrator\Kernel
 */
trait GatsbyOrchestrateMockObjectsHelper {

  /**
   * The mocked messenger object.
   *
   * @var \PHPUnit\Framework\MockObject\MockObject|\Drupal\Core\Messenger\Messenger
   */
  public $messengerMock;

  /**
   * The gatsby service mock.
   *
   * @var \PHPUnit\Framework\MockObject\MockObject|GatsbyOrchestratorGatsbyHealth
   */
  public $gatsbyHealthMock;

  /**
   * The gatsby settings mock.
   *
   * @var \PHPUnit\Framework\MockObject\MockObject|\Drupal\Core\Config\Config
   */
  public $gatsbySettingsMock;

  /**
   * The mocked logger service.
   *
   * @var \PHPUnit\Framework\MockObject\MockObject|\Psr\Log\LoggerInterface
   */
  protected $loggerMock;

  /**
   * Set the gatsby health mock object.
   *
   * @return \Drupal\gatsby_orchestrator\GatsbyOrchestratorGatsbyHealth|\PHPUnit\Framework\MockObject\MockObject
   *   The mock object.
   */
  public function getAndSetGatsbyHealthMock() {
    $this->gatsbyHealthMock = $this
      ->getMockBuilder(GatsbyOrchestratorGatsbyHealth::class)
      ->disableOriginalConstructor()
      ->getMock();

    return $this->gatsbyHealthMock;
  }

  /**
   * Set the gatsby settings mock.
   *
   * @return \Drupal\Core\Config\Config|\PHPUnit\Framework\MockObject\MockObject
   *   The mock object.
   */
  public function getAndSetGatsbySettingsMock() {
    $this->gatsbySettingsMock = $this
      ->getMockBuilder(Config::class)
      ->disableOriginalConstructor()
      ->getMock();

    return $this->gatsbySettingsMock;
  }

  /**
   * Set the messenger mock.
   *
   * @return \Drupal\Core\Messenger\Messenger|\PHPUnit\Framework\MockObject\MockObject
   *   The mock object.
   */
  public function getAndSetMessengerMock() {
    $this->messengerMock = $this
      ->getMockBuilder(Messenger::class)
      ->disableOriginalConstructor()
      ->getMock();

    return $this->messengerMock;
  }

  /**
   * Get the mock of the logger service.
   *
   * @return \PHPUnit\Framework\MockObject\MockObject|\Psr\Log\LoggerInterface
   *   The mocked log object.
   */
  public function getAndSetLoggerMock() {
    $this->loggerMock = $this
      ->getMockBuilder(LoggerInterface::class)
      ->getMock();

    return $this->loggerMock;
  }

}
