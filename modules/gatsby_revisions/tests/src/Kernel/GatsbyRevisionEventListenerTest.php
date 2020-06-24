<?php

namespace Drupal\Tests\gatsby_revisions\Kernel;

use Drupal\gatsby_revisions\Entity\GatsbyRevision;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\gatsby_orchestrator\Kernel\Mocks\LoggerMock;
use Drupal\Tests\gatsby_orchestrator\Kernel\MockTraits;

/**
 * Test description.
 *
 * @group gatsby_revisions
 */
class GatsbyRevisionEventListenerTest extends KernelTestBase {

  use MockTraits;

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
    $payload = new \stdClass();
    $payload->revisionId = 1234;
    $payload->status = 'failed';
    $payload->data = "pizza";

    $gatsby_revision = GatsbyRevision::create([
      'title' => $this->randomString(),
      'gatsby_revision_number' => $payload->revisionId,
    ]);

    $gatsby_revision->save();

    $gatsby_revision->getCacheContexts();

    // Call the plugin with the payload.
    $this->revisionCreationHandler->handle($payload);

    $reloaded_entity = GatsbyRevision::load($gatsby_revision->id());
    $this->assertEquals('pizza', $reloaded_entity->get('error')->value);
    $this->assertEquals(GatsbyRevision::STATUS_FAILED, $reloaded_entity->get('status')->value);
    $this->assertStringMatchesFormat('The gatsby revision, %s, set with the status failed: %s.', $this->mockLogger->info[0]);
  }

  /**
   * Testing when a revision creation has succeeded.
   */
  public function testSuccessOfRevisionCreation() {
    $payload = new \stdClass();
    $payload->revisionId = 1234;
    $payload->status = 'succeeded';

    $gatsby_revision = GatsbyRevision::create([
      'title' => $this->randomString(),
      'gatsby_revision_number' => $payload->revisionId,
    ]);

    $gatsby_revision->save();

    $gatsby_revision->getCacheContexts();

    // Call the plugin with the payload.
    $this->revisionCreationHandler->handle($payload);

    $reloaded_entity = GatsbyRevision::load($gatsby_revision->id());
    $this->assertEmpty($reloaded_entity->get('error')->value);
    $this->assertEquals(GatsbyRevision::STATUS_PASSED, $reloaded_entity->get('status')->value);
    $this->assertStringMatchesFormat('The gatsby revision, %s, set with the status success.', $this->mockLogger->info[0]);
  }

}
