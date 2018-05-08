Feature: Content
  In order to test some basic Behat functionality
  As a website user
  I need to be able to see that the Drupal and Drush drivers are working

# TODO:
# Add tests for:
#   - Creating district
#   - Creating district landing page
#   - Creating district voting page
#   - Creating district ballot
#   - Creating district aid users
#
# Used examples from https://github.com/pantheon-systems/example-drops-8-composer/blob/master/tests/features/content.feature

  @api
  Scenario: Create users
    Given users:
    | name     | mail            | status |
    | Joe User | joe@example.com | 1      |
    And I am logged in as a user with the "administrator" role
    When I visit "admin/people"
    Then I should see the link "Joe User"
