<?php

namespace Drupal\Tests\gatsby_orchestrator\Kernel;

use Drupal\Core\Config\Config;
use Drupal\Core\Messenger\Messenger;
use Drupal\gatsby_orchestrator\GatsbyOrchestratorGatsbyHealth;

/**
 * Boilerplate mocks objects.
 *
 * @package Drupal\Tests\gatsby_orchestrator\Kernel
 */
trait MockTraits {

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
   * Set the gatsby health mock object.
   *
   * @return \Drupal\gatsby_orchestrator\GatsbyOrchestratorGatsbyHealth|\PHPUnit\Framework\MockObject\MockObject
   *   The mock object.
   */
  public function setGatsbyHealthMock() {
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
  public function setGatsbySettingsMock() {
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
  public function setMessengerMock() {
    $this->messengerMock = $this
      ->getMockBuilder(Messenger::class)
      ->disableOriginalConstructor()
      ->getMock();

    return $this->messengerMock;
  }

}
