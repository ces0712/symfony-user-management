Feature: Manage Users data via the RESTful API

  In order to offer the User resource via an hypermedia API
  As a client software developer
  I need to be able to retrieve, create, update, and delete JSON encoded User resources


  Background:
    Given there are Users with the following details:
    | id | username | email          | password | name         |
    | 1  | peter    | peter@test.com | testpass | peter O tool |
    | 2  | john     | john@test.org  | testpass | john wane    |
    
    Given there are Groups with the following details:
    | id  | name       | roles                 | username |
    | 1   | grouptest  | ROLE_ADMIN, ROLE_TEST | peter    |
    | 2   | grouptest2 | ROLE_ADMIN, ROLE_TEST |          |

    And I am successfully logged in with username: "peter", and password: "testpass"
    And I set header "Content-Type" with value "application/json"
  
  Scenario: User cannot GET a Collection of User objects
    When I send a "GET" request to "/users"
    Then the response code should be 405
  
  Scenario: User can GET their personal data by their unique ID
    When I send a "GET" request to "/users/1"
    Then the response code should be 200
     And the response header "Content-Type" should be equal to "application/json"
     And the response should contain json:
      """
      {
        "id": "1",
        "email": "peter@test.com",
        "username": "peter"
      }
      """
  
  Scenario: An admin user can DELETE a user
    When I send a "DELETE" request to "/users/2"
    Then the response code should be 204
  
  Scenario: An admin user cannot DELETE a none existent User
    When I send a "DELETE" request to "/users/100"
    Then the response code should be 403
  @t
  Scenario: An admin user can assign users to a group
    When I send a "POST" request to "/users/addgroup" with body:
      """
      {
        "userId": "1",
        "name": "grouptest2"
      }
      """
    Then the response code should be 200
    And the response should contain json:
    """
      {
        "msg": "users assign to a group successfully"
      }
    """
  @t
  Scenario: An admin user can remove users from a group
    When I send a "POST" request to "/users/removegroup" with body:
    """
    {
      "userId": "1",
      "name": "grouptest"
    }
    """
    Then the response code should be 200
     And the response should contain json:
    """
      {
        "msg": "remove user from group successfully"
      }
    """
   
  Scenario: A not admin user cannot DELETE any user
    When I am successfully logged in with username: "john", and password: "testpass"
    Then I send a "DELETE" request to "/users/2"
    And the response code should be 403

