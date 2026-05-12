<?php

namespace Drupal\va_gov_backend\Element;

if (!class_exists('\\Drupal\\office_hours\\Element\\FormElementBase')) {
  // Prevent fatal error if alias is not defined (e.g., in unit test context).
  return;
}

use Drupal\Core\Form\FormStateInterface;
use Drupal\office_hours\Element\OfficeHoursBaseSlot;

/**
 * Provides a VA.gov accessible office hours slot element.
 *
 * @FormElement("va_gov_office_hours_slot")
 */
class VaGovOfficeHoursSlot extends OfficeHoursBaseSlot {

  /**
   * {@inheritdoc}
   */
  public static function processOfficeHoursSlot(array &$element, FormStateInterface $form_state, array &$complete_form): array {
    $element = parent::processOfficeHoursSlot($element, $form_state, $complete_form);

    $value = $element['#value'];
    $value = is_object($value) ? $value->getValue() : $value;
    $day = $value['day'];
    $day_delta = $element['#day_delta'];
    $value['day_delta'] = $day_delta;

    $pattern = 'long';
    $label = static::formatWeekday($pattern, $value, $day_delta);

    $element['day'] = [
      '#type' => 'hidden',
      '#title' => $label,
      '#prefix' => $day_delta
        ? "<div class='office-hours-more-label'>" . htmlspecialchars($label) . "</div>"
        : "<div class='office-hours-label'>" . htmlspecialchars($label) . "</div>",
      '#default_value' => $day,
      '#wrapper_attributes' => ['header' => !$day_delta],
    ];

    // Set accessible, visually hidden labels for time subfields only.
    if (isset($element['starthours'])) {
      $element['starthours']['#type'] = 'va_gov_office_hours_datetime';
      $element['starthours']['#label'] = t('Opening time on %day', ['%day' => $label]);
    }
    if (isset($element['endhours'])) {
      $element['endhours']['#type'] = 'va_gov_office_hours_datetime';
      $element['endhours']['#label'] = t('Closing time on %day', ['%day' => $label]);
    }
    if (isset($element['comment'])) {
      $element['comment']['#title'] = t('A comment for %day', ['%day' => $label]);
    }

    return $element;
  }

}
