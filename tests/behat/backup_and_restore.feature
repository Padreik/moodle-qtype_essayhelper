@qtype @qtype_essayhelper
Feature: Test duplicating a quiz containing an Essay with correction helper question
  As a teacher
  In order re-use my courses containing Essay with correction helper questions
  I need to be able to backup and restore them

  Background:
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name      | template   |
      | Test questions   | essayhelper | essay-001 | plain      |
      | Test questions   | essayhelper | essay-002 | monospaced |
    And the following "activities" exist:
      | activity   | name      | course | idnumber |
      | quiz       | Test quiz | C1     | quiz1    |
    And quiz "Test quiz" contains the following questions:
      | essay-001 | 1 |
      | essay-002 | 1 |
    And I log in as "admin"
    And I am on "Course 1" course homepage

  @javascript
  Scenario: Backup and restore a course containing 2 Essay with correction helper questions
    When I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Schema | Course name | Course 2 |
    And I navigate to "Question bank" node in "Course administration"
    And I should see "essay-001"
    And I should see "essay-002"
    And I click on "Edit" "link" in the "essay-001" "table_row"
    Then the following fields match these values:
      | Question name              | essay-001                                               |
      | Question text              | Please write a story about a frog.                      |
      | General feedback           | I hope your story had a beginning, a middle and an end. |
      | Response format            | Plain text                                              |
      | Require text               | Require the student to enter text                       |
    And I press "Cancel"
    And I click on "Edit" "link" in the "essay-002" "table_row"
    Then the following fields match these values:
      | Question name              | essay-002                                               |
      | Question text              | Please write a story about a frog.                      |
      | General feedback           | I hope your story had a beginning, a middle and an end. |
      | Response format            | Plain text, monospaced font                             |
      | Require text               | Require the student to enter text                       |
