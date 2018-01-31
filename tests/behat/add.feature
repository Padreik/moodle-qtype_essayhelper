@qtype @qtype_essayhelper
Feature: Test creating an Essay question
  As a teacher
  In order to test my students
  I need to be able to create an Essay with correction helper question

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher1 | T1        | Teacher1 | teacher1@moodle.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank" node in "Course administration"

  Scenario: Create an Essay with correction helper question with Response format set to 'Plain text'
    When I add a "Essay with correction helper" question filling the form with:
      | Question name            | essay-001                      |
      | Question text            | Write an essay with 500 words. |
      | General feedback         | This is general feedback       |
      | Response format          | Plain text                     |
    Then I should see "essay-001"

  Scenario: Create an Essay with correction helper question with Response format set to 'Plain text, monospaced font'
    When I add a "Essay with correction helper" question filling the form with:
      | Question name            | essay-002                      |
      | Question text            | Write an essay with 500 words. |
      | General feedback         | This is general feedback       |
      | Response format          | Plain text, monospaced font    |
    Then I should see "essay-002"
