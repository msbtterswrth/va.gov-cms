<?php

namespace tests\phpunit\va_gov_vamc\functional;

use Drupal\node\Entity\Node;
use Tests\Support\Classes\VaGovExistingSiteBase;

/**
 * Test for archiving a facility service and its related system service.
 *
 * @group va_gov_vamc
 * @group functional
 * @group disabled
 */
class ArchiveFacilityServiceFunctionalTest extends VaGovExistingSiteBase {

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();
    // This test does not use the browser, so disable HTML output to avoid
    // HTTP requests (e.g. fetching druplicon.png) that can fail with DNS
    // resolution errors in CI environments.
    $this->htmlOutputEnabled = FALSE;
  }

  /**
   * Minimal test to check system is archived when last facility is archived.
   */
  public function testSystemIsArchivedWithLastFacility() {
    // Create referenced system node.
    $system_service = $this->createNode([
      'type' => 'regional_health_care_service_des',
      'title' => 'System Service',
      'moderation_state' => 'published',
      'status' => 1,
    ]);
    $system_service->save();

    // Create single facility node referencing the system.
    $facility_service = $this->createNode([
      'type' => 'health_care_local_health_service',
      'title' => 'Facility Service',
      'field_regional_health_service' => [
        ['target_id' => $system_service->id()],
      ],
      'moderation_state' => 'published',
      'status' => 1,
    ]);
    $facility_service->save();

    // Archive the facility service.
    $facility_service->set('moderation_state', 'archived');
    $facility_service->set('status', 0);
    $facility_service->save();

    // Reload both nodes.
    $reloaded_system = Node::load($system_service->id());
    $reloaded_facility = Node::load($facility_service->id());

    // Assert facility is archived.
    $this->assertTrue(
      $reloaded_facility->get('moderation_state')->value === 'archived',
      'Facility service is archived.'
    );
    // Assert system is also archived.
    $this->assertTrue(
      $reloaded_system->get('moderation_state')->value === 'archived',
      'System service is also archived.'
    );
  }

  /**
   * Test that system is not archived if more than one facility is published.
   */
  public function testSystemNotArchivedWithMultipleFacilities() {
    // Create referenced system node.
    $system_service = $this->createNode([
      'type' => 'regional_health_care_service_des',
      'title' => 'System Service',
      'moderation_state' => 'published',
      'status' => 1,
    ]);
    $system_service->save();

    // Create two facility nodes referencing the system.
    $facility1 = $this->createNode([
      'type' => 'health_care_local_health_service',
      'title' => 'Facility 1',
      'field_regional_health_service' => [
        ['target_id' => $system_service->id()],
      ],
      'moderation_state' => 'published',
      'status' => 1,
    ]);
    $facility1->save();
    $facility2 = $this->createNode([
      'type' => 'health_care_local_health_service',
      'title' => 'Facility 2',
      'field_regional_health_service' => [
        ['target_id' => $system_service->id()],
      ],
      'moderation_state' => 'published',
      'status' => 1,
    ]);
    $facility2->save();

    // Archive the first facility.
    $facility1->set('moderation_state', 'archived');
    $facility1->set('status', 0);
    $facility1->save();

    $this->assertTrue(
      $facility1->get('moderation_state')->value === 'archived',
      'First facility service is archived.'
    );
    $this->assertTrue(
      $facility2->get('moderation_state')->value === 'published',
      'Second facility service is still published.'
    );
    $this->assertTrue(
      $system_service->get('moderation_state')->value === 'published',
      'System service is still published.'
    );
  }

}
