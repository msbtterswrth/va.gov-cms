<?php

namespace Drupal\va_gov_backend\Element;

if (!class_exists('\\Drupal\\office_hours\\Element\\FormElementBase')) {
  // Prevent fatal error if alias is not defined (e.g., in unit test context).
  return;
}

use Drupal\office_hours\Element\OfficeHoursDatetime;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a Datetime element for VA office hours with accessible labels.
 *
 * This element extends the contrib OfficeHoursDatetime element to allow
 * injection of a unique, accessible label for the time input, improving
 * screen reader support and overall accessibility. The label is passed
 * via #label and applied directly to the time input.
 *
 * @FormElement("va_gov_office_hours_datetime")
 */
class VaGovOfficeHoursDatetime extends OfficeHoursDatetime {

  /**
   * {@inheritdoc}
   */
  public static function processDatetime(&$element, FormStateInterface $form_state, &$complete_form) {
    $element = parent::processDatetime($element, $form_state, $complete_form);

    // Set accessible, visually hidden label for time input.
    if (isset($element['time']) && !empty($element['#label'])) {
      $element['time']['#title'] = $element['#label'];
    }

    return $element;
  }

}
