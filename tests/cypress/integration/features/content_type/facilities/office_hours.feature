@office_hours
Feature: CMS users can manage office hours for VAMC Facilities
  In order to provide accurate facility information
  As a CMS content editor
  I want to add and edit office hours on VAMC Facility content types

  @critical_path
  Scenario: Add office hours to a new VAMC Facility
    Given I am logged in as a user with the "content_admin" role
    When I am at "/node/add/health_care_local_facility"
    And I fill in "Opening time on Monday" with "08:00"
    And I fill in "Closing time on Monday" with "17:00"
    And I fill in "Opening time on Tuesday" with "08:00"
    And I fill in "Closing time on Tuesday" with "17:00"
    And I fill in "A comment for Monday" with "Open for appointments"
    And I fill in "A comment for Sunday" with "Closed on Sundays"
    And I should see "Opening time"
    And I should see "Closing time"
