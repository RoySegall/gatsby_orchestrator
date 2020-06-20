<?php

namespace Drupal\Tests\gatsby_revisions\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Testing the GatsbyRevisionsQuery plugin.
 *
 * @group gatsby_revisions
 */
class GatsbyRevisionsQueryTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['gatsby_revisions'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->foo = 'bar';
  }

  /**
   * Testing no request will be send when gatsby is down.
   */
  public function testRequestSendingWhenServiceIsDown() {
    $this->pass('TBD');
  }

  /**
   * Testing no request will be send when gatsby is down.
   */
  public function testRequestSendingWhenServiceIsUp() {
    $this->pass('TBD');
  }

}
