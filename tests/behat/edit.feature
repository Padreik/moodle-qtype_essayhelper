@qtype @qtype_essayhelper
Feature: Test editing an Essay with correction helper question
  As a teacher
  In order to be able to update my Essay with correction helper question
  I need to edit them

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | T1        | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name      | template   |
      | Test questions   | essayhelper | essay-001 | plain      |
      | Test questions   | essayhelper | essay-002 | monospaced |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank" node in "Course administration"

  Scenario: Edit an Essay with correction helper question
    When I click on "Edit" "link" in the "essay-001" "table_row"
    And I set the following fields to these values:
      | Question name | |
    And I press "id_submitbutton"
    Then I should see "You must supply a value here."
    When I set the following fields to these values:
      | Question name   | Edited essay-001 name |
      | Response format | Plain text, monospaced font |
    And I press "id_submitbutton"
    Then I should see "Edited essay-001 name"