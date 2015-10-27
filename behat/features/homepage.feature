Feature: Homepage
  In order to be able to log in.
  As an anonymous user
  We need to be able to have access to the dashboard

  @javascript
  Scenario: Visit the homepage
    Given I visit the homepage
    When  I go to log in page
    And   I log in as registered user
    Then  I should see the dashboard as registered user
#    And   I should see "Gizra" in the "" element
#     And  I go to "account/details/"
