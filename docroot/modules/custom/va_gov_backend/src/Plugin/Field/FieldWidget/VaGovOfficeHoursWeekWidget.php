<?php

declare(strict_types=1);

namespace Drupal\va_gov_backend\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\office_hours\Plugin\Field\FieldWidget\OfficeHoursWeekWidget;

/**
 * Field widget for VA.gov office hours with accessible time input labels.
 *
 * This widget extends the contrib OfficeHoursWeekWidget to use a custom
 * table element (va_gov_office_hours_table) and custom slot/datetime elements,
 * ensuring that each time input has a unique, accessible label for screen
 * readers. This improves accessibility and usability for all users.
 *
 * @FieldWidget(
 *   id = "va_gov_office_hours_week_widget",
 *   label = @Translation("Office hours (accessible week)"),
 *   description = @Translation("Accessible widget for weekdays with unique time input labels."),
 *   field_types = {
 *     "office_hours",
 *   },
 *   multiple_values = TRUE,
 * )
 */
class VaGovOfficeHoursWeekWidget extends OfficeHoursWeekWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    // Call the parent to get the original table/slot rendering.
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    if (isset($element['value'])) {
      $element['value']['#type'] = 'va_gov_office_hours_table';
    }
    return $element;
  }

}
