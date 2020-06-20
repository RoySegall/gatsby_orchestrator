<?php

namespace Drupal\Tests\gatsby_revisions\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\gatsby_orchestrator\Kernel\Mocks\LoggerMock;

/**
 * Test description.
 *
 * @group gatsby_revisions
 */
class GatsbyRevisionEventListenerTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'gatsby_orchestrator',
    'gatsby_revisions',
    'options',
  ];

  /**
   * The event listener plugin.
   *
   * @var \Drupal\gatsby_orchestrator\GatsbyEventListenerPluginBase
   */
  protected $revisionCreationHandler;

  /**
   * The mocked logger service.
   *
   * @var \Drupal\Tests\gatsby_orchestrator\Kernel\Mocks\LoggerMock
   */
  protected $mockLogger;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('gatsby_revision');
    $event_listener_plugin = $this->container->get('plugin.manager.gatsby_event_listener');

    $this->mockLogger = new LoggerMock();

    $this->revisionCreationHandler = $event_listener_plugin->createInstance('revision_creation');
    $this->revisionCreationHandler->setLogger($this->mockLogger);
  }

  /**
   * Testing when there's no revision with the matching ID.
   */
  public function testHandlingNonExistingRevision() {

    $payload = new \stdClass();
    $payload->revisionId = 123456789;
    $payload->status = 'failed';

    $this->revisionCreationHandler->handle($payload);

    $this->assertEquals("A notification for the gatsby revision with the ID 123456789 was sent but there is no record in the DB for a revision like that", $this->mockLogger->errors[0]);
  }

  /**
   * Testing the case where the failure of the occurred.
   */
  public function testFailureOfRevisionCreation() {
    $this->pass('a');
  }

  /**
   * Testing when a revision creation has succeeded.
   */
  public function testSuccessOfRevisionCreation() {
    $this->pass('a');
  }

}
