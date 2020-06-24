<?php

namespace Drupal\Tests\gatsby_revisions\Kernel;

use Drupal\Core\Config\Config;
use Drupal\gatsby_orchestrator\GatsbyOrchestratorGatsbyHealth;
use Drupal\gatsby_revisions\Plugin\GatsbyOrchestrate\GatsbyRevisionsQuery;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\gatsby_orchestrator\Kernel\Mocks\LoggerMock;
use Drupal\Tests\gatsby_orchestrator\Kernel\Mocks\MessengerMock;
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
   * @var GatsbyRevisionsQuery
   */
  protected $revisionQueryHandler;

  /**
   * The mocked messenger object.
   *
   * @var MessengerMock
   */
  protected $messengerMock;

  /**
   * The gatsby service mock.
   *
   * @var \PHPUnit\Framework\MockObject\MockObject|GatsbyOrchestratorGatsbyHealth
   */
  public $gatsbyMock;

  /**
   * @var \PHPUnit\Framework\MockObject\MockObject|\Drupal\Core\Config\Config
   */
  public $gatsbySettingsMock;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installEntitySchema('gatsby_revision');
    $event_listener_plugin = $this->container->get('plugin.manager.gatsby_orchestrate');
    $this->revisionQueryHandler = $event_listener_plugin->createInstance('get_revisions');

    $this->gatsbyMock = $this
      ->getMockBuilder(GatsbyOrchestratorGatsbyHealth::class)
      ->disableOriginalConstructor()
      ->getMock();

    $this->gatsbySettingsMock = $this
      ->getMockBuilder(Config::class)
      ->disableOriginalConstructor()
      ->getMock();

    $this->messengerMock = new MessengerMock();
    $this->revisionQueryHandler
      ->setGatsbyHealth($this->gatsbyMock)
      ->setGatsbySettings($this->gatsbySettingsMock)
      ->setMessenger($this->messengerMock);
  }

  /**
   * Testing no request will be send when gatsby is down.
   */
  public function testRequestSendingWhenServiceIsDown() {

    $this->gatsbyMock
      ->expects($this->once())
      ->method('checkGatsbyHealth')
      ->will($this->returnValue(GatsbyOrchestratorGatsbyHealth::GATSBY_SERVICE_DOWN));

    $this->gatsbySettingsMock->expects($this->never())->method('get');

    $this->assertNull($this->revisionQueryHandler->orchestrate());
  }

  /**
   * Testing no request will be send when gatsby is up but the server return good response.
   */
  public function testRequestSendingWhenServiceIsUpAndValidResponse() {
    $this->gatsbyMock
      ->expects($this->once())
      ->method('checkGatsbyHealth')
      ->will($this->returnValue(GatsbyOrchestratorGatsbyHealth::GATSBY_SERVICE_UP));

    $this->gatsbySettingsMock
      ->expects($this->once())
      ->method('get')
      ->with('server_url')
      ->willReturn('a');

    $this->revisionQueryHandler->orchestrate();
  }

  /**
   * Testing no request will be send when gatsby is up but the server returned a bad response.
   */
  public function testRequestSendingWhenServiceIsUpAndErroredResponse() {
    $this->gatsbyMock
      ->expects($this->once())
      ->method('checkGatsbyHealth')
      ->will($this->returnValue(GatsbyOrchestratorGatsbyHealth::GATSBY_SERVICE_UP));

    $this->gatsbySettingsMock
      ->expects($this->once())
      ->method('get')
      ->with('server_url')
      ->willReturn('a');

    $this->revisionQueryHandler->orchestrate();
  }

}
