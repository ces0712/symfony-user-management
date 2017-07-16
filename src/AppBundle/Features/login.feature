# /src/AppBundle/Features/login.feature

Feature: Handle user login via the RESTful API

  In order to allow secure access to the system
  As a client software developer
  I need to be able to let users log in and out


  Background:
    Given there are Users with the following details:
      | id | username | email          | password | name  |
      | 1  | peter    | peter@test.com | testpass | peter |
      | 2  | john     | john@test.org  | johnpass | john  |
     And I set header "Content-Type" with value "application/json"

  Scenario: Cannot GET Login
    When I send a "GET" request to "/login"
    Then the response code should be 405

  Scenario: User cannot Login with bad credentials
    When I send a "POST" request to "/login" with body:
      """
      {
        "username": "jimmy",
        "password": "wrongPass"
      }
      """
    Then the response code should be 401


  Scenario: User can Login with good credentials
    When I send a "POST" request to "/login" with body:
      """
      {
        "username": "peter",
        "password": "testpass"
      }
      """
    Then the response code should be 200
     And the response should contain "token"