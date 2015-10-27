Feature: Homepage
  In order to be able to log in.
  As an anonymous user
  We need to be able to have access to the dashboard

  @javascript
  Scenario: Visit the homepage
    Given I visit the homepage
    When  I login
    Then  I should see the dashboard as registered user
