<?php

namespace Drupal\Tests\gatsby_orchestrator\Kernel;

use Drupal\gatsby_orchestrator\GatsbyOrchestratorGatsbyHealth;
use Drupal\KernelTests\KernelTestBase;
use GuzzleHttp\Handler\MockHandler;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

/**
 * Test description.
 *
 * @group gatsby_orchestrator
 */
class GatsbyOrchestratorGatsbyHealthTest extends KernelTestBase {

  use MockTraits;

  /**
   * {@inheritdoc}
   */
  public static $modules = ['gatsby_orchestrator'];

  /**
   * The gatsby health.
   *
   * @var \Drupal\gatsby_orchestrator\GatsbyOrchestratorGatsbyHealth
   */
  protected $gatsbyHealth;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $mock = new MockHandler([
      new RequestException('Error Communicating with Server', new Request('GET', 'test')),
      new Response(200, ['X-Foo' => 'Bar'], 'Hello, World'),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);

    $this->gatsbyHealth = $this->container->get('gatsby_orchestrator.gatsby_health');
    $this->gatsbyHealth
      ->setHttpClient($client)
      ->setGatsbySettings($this->setGatsbySettingsMock())
      ->setMessenger($this->setMessengerMock());

    $this->messengerMock->expects($this->atLeast(2))->method('addError');

    $this->gatsbySettingsMock->expects($this->at(0))->method('get')->with('server_url')->willReturn(NULL);
    $this->gatsbySettingsMock->expects($this->at(1))->method('get')->with('server_url')->willReturn('http://google.com');
    $this->gatsbySettingsMock->expects($this->at(2))->method('get')->with('server_url')->willReturn('http://google.com');
  }

  /**
   * Testing the scenarios of getting the health of the gatsby service.
   */
  public function testHealthOfTheGatsbyServer() {
    $this->messengerMock->expects($this->at(0))->method('addError')->with('It seems that the address of the gatsby server has not been set.');
    $this->messengerMock->expects($this->at(1))->method('addError')->with('Error Communicating with Server');

    // Checking handling with a null address.
    $results = $this->gatsbyHealth->checkGatsbyHealth();
    $this->assertEquals(GatsbyOrchestratorGatsbyHealth::GATSBY_SERVICE_DOWN, $results);

    // Checking handling error.
    $results = $this->gatsbyHealth->checkGatsbyHealth();
    $this->assertEquals(GatsbyOrchestratorGatsbyHealth::GATSBY_SERVICE_DOWN, $results);

    // Checking handling success.
    $results = $this->gatsbyHealth->checkGatsbyHealth();
    $this->assertEquals(GatsbyOrchestratorGatsbyHealth::GATSBY_SERVICE_UP, $results);
  }

}
