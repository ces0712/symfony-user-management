Feature: Handle user groups via the RESTful API

  In order to allow a user to manage groups
  As a client software developer
  I need to be able to handle groups table

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
    
    Scenario: An admin user can create a new group with valid data
      When I send a "POST" request to "/group" with body:
        """
        {
          "name": "grouptest3"
        }
        """
      Then the response code should be 201
      And the response should contain "The group has been created"
    
    Scenario: An admin can delete groups when they no longer have members
      When I send a "DELETE" request to "/group/grouptest2/delete"
      Then the response code should be 200
      And the response should contain "The group has been deleted"
    
    Scenario: An admin can not delete groups when they have members
      When I send a "DELETE" request to "/group/grouptest/delete"
      Then the response code should be 400
      And the response should contain "error this group has a member"

    Scenario: A not admin users can not delete any groups
      When I am successfully logged in with username: "max", and password: "testpass"
      Then I send a "DELETE" request to "/group/grouptest2/delete"
      And the response code should be 403


