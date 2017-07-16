# /src/AppBundle/Features/register.feature

Feature: Handle user registration via the RESTful API

  In order to allow an admin user create new users
  As a client software developer
  I need to be able to handle new users


  Background:
    Given there are Users with the following details:
      | id | username | email          | password | name  |
      | 1  | peter    | peter@test.com | testpass | peter |
      | 2  | max      | max@test.com   | testpass | max   |
    Given there are Groups with the following details:
      | id  | name       | roles                 | username |
      | 1   | grouptest  | ROLE_ADMIN, ROLE_TEST | peter    |
      | 2   | grouptest2 | ROLE_ADMIN, ROLE_TEST |          |
    And I am successfully logged in with username: "peter", and password: "testpass"
    And I set header "Content-Type" with value "application/json"
    
  Scenario: An admin with valid data can add users
    When I send a "POST" request to "/register" with body:
      """
      {
        "email": "cesar@gmail.com",
        "username": "cesar",
        "name": "cesar",
        "plainPassword": {
          "first": "abc123",
          "second": "abc123"
        }
      }
      """
    Then the response code should be 201
    And the response should contain "The user has been created successfully"
  
  Scenario: An admin cannot add users with an existing username
    When I send a "POST" request to "/register" with body:
      """
      {
        "email": "peter@some-different-domain.com",
        "username": "peter",
        "plainPassword": {
          "first": "abc123",
          "second": "abc123"
        }
      }
      """
    Then the response code should be 400
     And the response should contain "The username is already used"
  
  Scenario: An admin cannot add users with an existing email address
    When I send a "POST" request to "/register" with body:
      """
      {
        "email": "peter@test.com",
        "username": "different_peter",
        "plainPassword": {
          "first": "abc123",
          "second": "abc123"
        }
      }
      """
    Then the response code should be 400
     And the response should contain "The email is already used"
  
  Scenario: An admin cannot add users with an mismatched password
    When I send a "POST" request to "/register" with body:
      """
      {
        "email": "gary@test.co.uk",
        "username": "garold",
        "plainPassword": {
          "first": "gaz123",
          "second": "gaz456"
        }
      }
      """
    Then the response code should be 400
    And the response should contain "The entered passwords don't match"
  
  Scenario: An not admin user cannot add users
    When I am successfully logged in with username: "max", and password: "testpass"
    Then I send a "POST" request to "/register" with body:
      """
      {
        "email": "cesar@gmail.com",
        "username": "cesar",
        "name": "cesar",
        "plainPassword": {
          "first": "abc123",
          "second": "abc123"
        }
      }
      """
    And the response code should be 403