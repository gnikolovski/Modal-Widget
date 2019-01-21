<?php

namespace Drupal\modal_widget\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Modal widget.
 *
 * @FieldWidget(
 *   id = "modal_widget",
 *   label = @Translation("Modal widget"),
 *   field_types = {
 *     "entity_reference"
 *   },
 *   multiple_values = false
 * )
 */
class ModalWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $defaults = parent::defaultSettings();
    $defaults += [
      'width' => '800',
      'height' => '500',
      'override_label' => FALSE,
      'label_singular' => '',
    ];

    return $defaults;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);

    $element['width'] = [
      '#type' => 'number',
      '#title' => $this->t('Modal width'),
      '#default_value' => $this->getSetting('width'),
    ];

    $element['height'] = [
      '#type' => 'number',
      '#title' => $this->t('Modal height'),
      '#default_value' => $this->getSetting('height'),
    ];

    $element['override_label'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Override label'),
      '#default_value' => $this->getSetting('override_label'),
    ];

    $element['label_singular'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Singular label'),
      '#default_value' => $this->getSetting('label_singular'),
      '#states' => [
        'visible' => [
          ':input[name="fields[' . $this->fieldDefinition->getName() . '][settings_edit_form][settings][override_label]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();

    if ($this->getSetting('width')) {
      $summary[] = $this->t('Width: @width', ['@width' => $this->getSetting('width')]);
    }
    else {
      $summary[] = $this->t('Width: not set.');
    }

    if ($this->getSetting('height')) {
      $summary[] = $this->t('Height: @height', ['@height' => $this->getSetting('height')]);
    }
    else {
      $summary[] = $this->t('Height: not set.');
    }

    if ($this->getSetting('override_label')) {
      $summary[] = $this->t('Overriden label is used: %singular', ['%singular' => $this->getSetting('label_singular')]);
    }
    else {
      $summary[] = $this->t('Default label is used.');
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    if (!$items->first()->getValue()) {
      return;
    }

    $entity_type = $items->getSetting('target_type');

    $url = Url::fromRoute('modal_widget.modal', [
      'entity_type' => $entity_type,
      'entity_id' => $items->first()->getValue()['target_id'],
    ]);

    if ($this->getSetting('override_label')) {
      $title = $this->getSetting('label_singular');
    }
    else {
      $title = $this->t('Edit entity');
    }

    return [
      '#type' => 'link',
      '#title' => $title,
      '#url' => $url,
      '#ajax' => [
        'dialogType' => 'modal',
        'dialog' => [
          'width' => $this->getSetting('width'),
          'height' => $this->getSetting('height'),
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    return $field_definition->getFieldStorageDefinition()->getCardinality() == 1;
  }

}
