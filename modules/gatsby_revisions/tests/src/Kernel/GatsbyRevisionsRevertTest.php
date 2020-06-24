<?php

namespace Drupal\Tests\gatsby_revisions\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\gatsby_orchestrator\Kernel\MockTraits;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

/**
 * Testing the GatsbyRevisionsRevert plugin.
 *
 * @group gatsby_revisions
 */
class GatsbyRevisionsRevertTest extends KernelTestBase {

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
   * The revision revert handler.
   *
   * @var \Drupal\gatsby_revisions\Plugin\GatsbyOrchestrate\GatsbyRevisionsRevert
   */
  protected $revisionRevertHandler;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installEntitySchema('gatsby_revision');
    $event_listener_plugin = $this->container->get('plugin.manager.gatsby_orchestrate');
    $this->revisionRevertHandler = $event_listener_plugin->createInstance('revert_revision');

    $this->revisionRevertHandler
      ->setGatsbyHealth($this->setGatsbyHealthMock())
      ->setGatsbySettings($this->setGatsbySettingsMock())
      ->setMessenger($this->setMessengerMock());
  }

  /**
   * Testing no request will be send when gatsby is down.
   */
  public function testRequestSendingWhenServiceIsDown() {
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

    $response = $this
      ->revisionRevertHandler
      ->setHttpClient($client)
      ->orchestrate();

    $this->assertEmpty($response);
  }

  /**
   * Testing no request will be send when gatsby is down.
   */
  public function testRequestSendingWhenServiceIsUp() {
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
      ->revisionRevertHandler
      ->setHttpClient($client)
      ->orchestrate();

    $this->assertEqual('bar', $response->foo);
  }

}
