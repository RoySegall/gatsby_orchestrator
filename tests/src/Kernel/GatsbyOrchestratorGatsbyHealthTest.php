<?php

namespace Drupal\Tests\gatsby_orchestrator\Kernel;

use Drupal\gatsby_orchestrator\GatsbyOrchestratorGatsbyHealth;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\gatsby_orchestrator\Kernel\Mocks\MessengerMock;
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
   * The messenger service.
   *
   * @var \Drupal\Tests\gatsby_orchestrator\Kernel\Mocks\MessengerMock
   */
  protected $messenger;

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
    $this->messenger = new MessengerMock();

    $this->gatsbyHealth = $this->container->get('gatsby_orchestrator.gatsby_health');
    $this->gatsbyHealth
      ->setHttpClient($client)
      ->setMessenger($this->messenger);
  }

  /**
   * Test callback.
   */
  public function testSomething() {
    // Send a request without an address and make sure we an error is tracked.
    $this->gatsbyHealth->checkGatsbyHealth();

    $this->assertEquals(
      "It seems that the address of the gatsby server has not been set.",
      $this->messenger->errors[0]
    );

    // Set a dummy address and see how we handle mocked results.
    $results = $this
      ->gatsbyHealth
      ->setAddress('http://google.com')
      ->checkGatsbyHealth();

    $this->assertEquals(GatsbyOrchestratorGatsbyHealth::GATSBY_SERVICE_DOWN, $results);
    $this->assertEquals("Error Communicating with Server", $this->messenger->errors[1]);

    // Wait for a successful request.
    $results = $this->gatsbyHealth->checkGatsbyHealth();
    $this->assertEquals(GatsbyOrchestratorGatsbyHealth::GATSBY_SERVICE_UP, $results);
  }

}
