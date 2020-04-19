<?php

namespace Drupal\Tests\modal_widget\Functional;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;

/**
 * Tests the Modal widget.
 *
 * @group modal_widget
 */
class ModalWidgetTest extends EntityKernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'modal_widget',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Tests widget.
   */
  public function testWidget() {
    $widgets = \Drupal::service('plugin.manager.field.widget')->getDefinitions();
    $this->assertArrayHasKey('modal_widget', $widgets);
  }

}
