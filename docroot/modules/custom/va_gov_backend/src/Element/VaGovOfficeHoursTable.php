<?php

namespace Drupal\va_gov_backend\Element;

if (!class_exists('\\Drupal\\office_hours\\Element\\FormElementBase')) {
  // Prevent fatal error if alias is not defined (e.g., in unit test context).
  return;
}

use Drupal\Core\Form\FormStateInterface;
use Drupal\office_hours\Element\OfficeHoursTable;
use Drupal\office_hours\OfficeHoursDateHelper;
use Drupal\office_hours\Plugin\Field\FieldType\OfficeHoursItem;

/**
 * Provides an accessible office hours table element for VA.gov.
 *
 * This custom FormElement builds an office hours table widget with improved
 * accessibility over the default contrib implementation. It ensures:
 *   - Each row is rendered using the custom va_gov_office_hours_slot element,
 *     not the contrib slot.
 *   - All time input fields have unique, visually hidden labels for screen
 *     readers (e.g., "Opening time on Monday").
 *   - Each day (Monday–Sunday) is rendered as a visible row header (th) for
 *     proper table semantics.
 *   - The "Comment" column header is present and accessible.
 *   - No reliance on fragile array indexes; context is passed explicitly to
 *     child elements.
 *   - All accessibility improvements are applied directly in the table and
 *     slot elements.
 *
 * This class intentionally does NOT call the parent processTable(), as the
 * parent always uses the contrib slot element, which does not provide the
 * required accessible labeling. Instead, it builds the table rows and cells
 * directly, ensuring robust and maintainable accessibility.
 *
 * @FormElement("va_gov_office_hours_table")
 */
class VaGovOfficeHoursTable extends OfficeHoursTable {

  /**
   * Builds the office hours table with custom slot elements for accessibility.
   *
   * This method intentionally does NOT call the parent processTable() because
   * the parent always uses the contrib slot element, which does not provide
   * the required accessible labeling. By building the table rows and cells
   * directly, we ensure that each row uses the custom slot element
   * (va_gov_office_hours_slot) and that all accessibility improvements are
   * applied. All required table properties and children are set explicitly.
   *
   * @param array &$element
   *   The form element array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   * @param array &$complete_form
   *   The complete form array.
   *
   * @return array
   *   The modified element array with custom slot children.
   */
  public static function processTable(&$element, FormStateInterface $form_state, &$complete_form): array {
    $field_settings = $element['#field_settings'];
    $cardinality = $field_settings['cardinality_per_day'];
    $indexed_items = $element['#value'];

    // Build multi element widget. Copy the description, etc. into the table.
    $header = OfficeHoursItem::getPropertyLabels('data', $field_settings);
    $element['#header'] = static::updatePropertyLabels('data', $header);

    // Build elements, sorted by first_day_of_week.
    $elements = [];
    $element_type = 'va_gov_office_hours_slot';
    $days = OfficeHoursDateHelper::weekDaysOrdered(range(0, 6));
    foreach ($days as $day) {
      $day_index = $day;
      for ($day_delta = 0; $day_delta < $cardinality; $day_delta++) {
        $value = $indexed_items[$day][$day_delta]
          ?? OfficeHoursItem::format(['day' => $day]);
        $elements[] = [
          '#type' => $element_type,
          '#default_value' => $value,
          '#day_index' => $day_index,
          '#day_delta' => $day_delta,
          '#field_settings' => $field_settings,
          '#title' => '',
          '#title_display' => 'invisible',
          '#description' => '',
        ];
      }
    }
    return $element + $elements;
  }

  /**
   * Updates the property labels for the office hours table header.
   *
   * This method ensures that the 'from' and 'to' column headers use the
   * accessible, translated labels 'Opening time' and 'Closing time',
   * with a fallback to the default translation context if needed.
   *
   * @param string $parent
   *   The parent key for the property labels array (usually 'data').
   * @param array $properties
   *   The property labels array to update.
   *
   * @return array
   *   The updated property labels array with accessible column headers.
   */
  public static function updatePropertyLabels($parent, $properties) {

    // Special translation with fallback.
    $from = t('Opening time', [], ['context' => 'A point in time']);
    $to = t('Closing time', [], ['context' => 'A point in time']);
    $properties['from'][$parent] = ($from == 'Opening time') ? t('Opening time') : $from;
    $properties['to'][$parent] = ($to == 'Closing time') ? t('Closing time') : $to;

    return $properties;
  }

}
