@qtype @qtype_essayhelper
Feature: Validate Essay with correction helper special features
  As a teacher
  In order to be helped correcting essays
  I need to see teacher answer and keywords while correcting while students don't

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher1 | T1        | Teacher1 | teacher1@moodle.com |
      | student1 | S1        | Student1 | student1@moodle.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name      | template |
      | Test questions   | essayhelper | essay-001 | plain    |
    And the following "activities" exist:
      | activity   | name   | course | idnumber |
      | quiz       | Quiz 1 | C1     | quiz1    |
    And quiz "Quiz 1" contains the following questions:
      | question   | page |
      | essay-001  | 1    |

  @javascript
  Scenario: A student submit an Essay with correction helper answer and don't see the correction helper.
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Quiz 1"
    And I press "Attempt quiz now"
    Then I should not see "Teacher answer" on quiz page "1"
    And I follow "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Confirmation" "dialogue"
    Then I should see "Finished"
    Then I should not see "Teacher answer" on quiz page "1"

  @javascript
  Scenario: A teacher should see the correction helper while manually grading.
    # Create answer
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Quiz 1"
    And I press "Attempt quiz now"
    And I set the field with xpath "//textarea[contains(@class, 'qtype_essayhelper_response')]" to "I think it's a frog"
    And I follow "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Confirmation" "dialogue"
    And I log out

    # Go to manuel correction module
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Quiz 1"
    And I navigate to "Results > Manual grading" in current page administration
    And I should see "Manual grading"
    And I click on "grade all" "link" in the "essay-001" "table_row"
    Then I should see "Teacher answer"

    # Test Keyword
    Then "//b/u[contains(text(), 'frog')]" "xpath_element" should exist