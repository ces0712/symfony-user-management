# /src/AppBundle/Features/profile.feature

Feature: Manage User profile data via the RESTful API

  In order to allow a user to keep their profile information up to date
  As a client software developer
  I need to be able to let users read and update their profile


  Background:
    Given there are Users with the following details:
      | id | username | email          | password | name  |
      | 1  | peter    | peter@test.com | testpass | peter |
      | 2  | john     | john@test.org  | johnpass | john  |
    And I am successfully logged in with username: "peter", and password: "testpass"
    And I set header "Content-Type" with value "application/json"
  
  Scenario: Can view own profile
    When I send a "GET" request to "/profile/1"
    Then the response code should be 200
     And the response should contain json:
      """
      {
        "id": "1",
        "username": "peter",
        "email": "peter@test.com"
      }
      """

  Scenario: Cannot view a profile with a bad JWT
    When I set header "Authorization" with value "Bearer bad-token-string"
     And I send a "GET" request to "/profile/1"
    Then the response code should be 401
     And the response should contain "Invalid JWT Token"

  Scenario: Cannot view another user's profile
    When I send a "GET" request to "/profile/2"
    Then the response code should be 403

  Scenario: Can replace their own profile
    When I send a "PUT" request to "/profile/1" with body:
      """
      {
        "username": "peter",
        "email": "new_email@test.com",
        "current_password": "testpass"
      }
      """
    Then the response code should be 204
     And I send a "GET" request to "/profile/1"
     And the response should contain json:
      """
      {
        "id": "1",
        "username": "peter",
        "email": "new_email@test.com"
      }
      """

  Scenario: Must supply current password when updating profile information
    When I send a "PUT" request to "/profile/1" with body:
      """
      {
        "email": "new_email@test.com"
      }
      """
    Then the response code should be 400

  Scenario: Cannot replace another user's profile
    When I send a "PUT" request to "/profile/2" with body:
      """
      {
        "username": "peter",
        "email": "new_email@test.com",
        "current_password": "testpass"
      }
      """
    Then the response code should be 403

