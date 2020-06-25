<?php

namespace Drupal\Tests\gatsby_revisions\Kernel;

use Drupal\gatsby_orchestrator\GatsbyOrchestratorGatsbyHealth;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\gatsby_orchestrator\Kernel\GatsbyOrchestrateMockObjectsHelper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

/**
 * Testing the GatsbyRevisionsQuery plugin.
 *
 * @group gatsby_revisions
 */
class GatsbyRevisionsQueryTest extends KernelTestBase {

  use GatsbyOrchestrateMockObjectsHelper;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'gatsby_orchestrator',
    'gatsby_revisions',
    'options',
  ];

  /**
   * The revision query object.
   *
   * @var \Drupal\gatsby_revisions\Plugin\GatsbyOrchestrate\GatsbyRevisionsQuery
   */
  protected $revisionQueryHandler;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installEntitySchema('gatsby_revision');
    $event_listener_plugin = $this->container->get('plugin.manager.gatsby_orchestrate');
    $this->revisionQueryHandler = $event_listener_plugin->createInstance('get_revisions');

    $this->revisionQueryHandler
      ->setGatsbyHealth($this->getAndSetGatsbyHealthMock())
      ->setGatsbySettings($this->getAndSetGatsbySettingsMock())
      ->setMessenger($this->getAndSetMessengerMock());
  }

  /**
   * Testing no request will be send when gatsby is down.
   */
  public function testRequestSendingWhenServiceIsDown() {

    $this->gatsbyHealthMock
      ->expects($this->once())
      ->method('checkGatsbyHealth')
      ->will($this->returnValue(GatsbyOrchestratorGatsbyHealth::GATSBY_SERVICE_DOWN));

    $this->gatsbySettingsMock->expects($this->never())->method('get');

    $this->assertNull($this->revisionQueryHandler->orchestrate());
  }

  /**
   * Testing when gatsby is up but the server returned a bad response.
   */
  public function testRequestSendingWhenServiceIsUpAndErroredResponse() {
    $this->gatsbyHealthMock
      ->expects($this->once())
      ->method('checkGatsbyHealth')
      ->will($this->returnValue(GatsbyOrchestratorGatsbyHealth::GATSBY_SERVICE_UP));

    $this->gatsbySettingsMock
      ->expects($this->once())
      ->method('get')
      ->with('server_url')
      ->willReturn('dummy_address');

    $this
      ->messengerMock
      ->expects($this->once())
      ->method('addError')
      ->with('Error Communicating with Server');

    $mock = new MockHandler([
      new RequestException('Error Communicating with Server', new Request('GET', 'test')),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);

    $this->assertEmpty($this->revisionQueryHandler->setHttpClient($client)->orchestrate());
  }

  /**
   * Testing when gatsby is up but the server return good response.
   */
  public function testRequestSendingWhenServiceIsUpAndValidResponse() {
    $this->gatsbyHealthMock
      ->expects($this->once())
      ->method('checkGatsbyHealth')
      ->will($this->returnValue(GatsbyOrchestratorGatsbyHealth::GATSBY_SERVICE_UP));

    $this->gatsbySettingsMock
      ->expects($this->once())
      ->method('get')
      ->with('server_url')
      ->willReturn('dummy_address');

    $this
      ->messengerMock
      ->expects($this->never())
      ->method('addError');

    $mock = new MockHandler([
      new Response(200, ['X-Foo' => 'Bar'], json_encode(['foo' => 'bar'])),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);

    $response = $this
      ->revisionQueryHandler
      ->setHttpClient($client)
      ->orchestrate();

    $this->assertEqual('bar', $response->foo);
  }

}
