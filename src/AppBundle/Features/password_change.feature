# /src/AppBundle/Features/password_change.feature

Feature: Handle password changing via the RESTful API

  In order to provide a more secure system
  As a client software developer
  I need to be able to let users change their current API password


  Background:
    Given there are Users with the following details:
      | id | username | email          | password | name  |
      | 1  | peter    | peter@test.com | testpass | peter | 
      | 2  | john     | john@test.org  | johnpass | john  |
     And I set header "Content-Type" with value "application/json"


  Scenario: Can change password with valid credentials
    When I am successfully logged in with username: "peter", and password: "testpass"
     And I send a "POST" request to "/password/1/change" with body:
      """
      {
        "current_password": "testpass",
        "plainPassword": {
          "first": "new password",
          "second": "new password"
        }
      }
      """
    Then the response code should be 200
     And the response should contain "The password has been changed"

  Scenario: Cannot hit the change password endpoint if not logged in (missing token)
    When I send a "POST" request to "/password/1/change" with body:
      """
      {
        "current_password": "testpass",
        "plainPassword": {
          "first": "new password",
          "second": "new password"
        }
      }
      """
    Then the response code should be 401

  Scenario: Cannot change the password for a different user
    When I am successfully logged in with username: "peter", and password: "testpass"
     And I send a "POST" request to "/password/2/change" with body:
      """
      {
        "current_password": "testpass",
        "plainPassword": {
          "first": "new password",
          "second": "new password"
        }
      }
      """
    Then the response code should be 403
  