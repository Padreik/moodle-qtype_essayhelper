<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Test helpers for the essay with correction helper question type.
 *
 * @package    qtype_essayhelper
 * @copyright  2018 Philippe Girard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * Inspired by:
 * @package    qtype_essay
 * @copyright  2013 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Test helper class for the essay with correction helper question type.
 *
 * @copyright  2018 Philippe girard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * Inspired by:
 * @copyright  2013 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_essayhelper_test_helper extends question_test_helper {
    public function get_test_questions() {
        return array('plain', 'monospaced', 'responsetemplate');
    }

    /**
     * Helper method to reduce duplication.
     * @return qtype_essayhelper_question
     */
    protected function initialise_essayhelper_question() {
        question_bank::load_question_definition_classes('essay');
        $q = new qtype_essay_question();
        test_question_maker::initialise_a_question($q);
        $q->name = 'Essay question';
        $q->questiontext = 'Please write a story about a frog.';
        $q->generalfeedback = 'I hope your story had a beginning, a middle and an end.';
        $q->responseformat = 'plain';
        $q->responserequired = 1;
        $q->responsefieldlines = 10;
        $q->officialanswer = 'Once upon a time, a frog.';
        $q->keywords = 'frog';
        $q->graderinfo = '';
        $q->graderinfoformat = FORMAT_HTML;
        $q->qtype = question_bank::get_qtype('essay');

        return $q;
    }

    /**
     * Makes an essay question using plain text input.
     * @return qtype_essayhelper_question
     */
    public function make_essayhelper_question_plain() {
        $q = $this->initialise_essay_question();
        return $q;
    }

    public function get_essayhelper_question_form_data_plain() {
        $fromform = new stdClass();

        $fromform->name = 'Essay question';
        $fromform->questiontext = array('text' => 'Please write a story about a frog.', 'format' => FORMAT_HTML);
        $fromform->defaultmark = 1.0;
        $fromform->generalfeedback = array('text' => 'I hope your story had a beginning, a middle and an end.', 'format' => FORMAT_HTML);
        $fromform->responseformat = 'plain';
        $fromform->responserequired = 1;
        $fromform->responsefieldlines = 10;
        $fromform->graderinfo = array('text' => '', 'format' => FORMAT_HTML);
        $fromform->responsetemplate = '';
        $fromform->officialanswer = 'Once upon a time, a frog.';
        $fromform->keywords = 'frog';

        return $fromform;
    }

    /**
     * Makes an essay question using monospaced input.
     * @return qtype_essayhelper_question
     */
    public function make_essayhelper_question_monospaced() {
        $q = $this->initialise_essayhelper_question();
        $q->responseformat = 'monospaced';
        return $q;
    }

    public function get_essayhelper_question_form_data_monospaced() {
        $fromform = new stdClass();

        $fromform->name = 'Essay question';
        $fromform->questiontext = array('text' => 'Please write a story about a frog.', 'format' => FORMAT_HTML);
        $fromform->defaultmark = 1.0;
        $fromform->generalfeedback = array('text' => 'I hope your story had a beginning, a middle and an end.', 'format' => FORMAT_HTML);
        $fromform->responseformat = 'monospaced';
        $fromform->responserequired = 1;
        $fromform->responsefieldlines = 10;
        $fromform->graderinfo = array('text' => '', 'format' => FORMAT_HTML);
        $fromform->responsetemplate = '';
        $fromform->officialanswer = 'Once upon a time, a frog.';
        $fromform->keywords = 'frog';

        return $fromform;
    }

    /**
     * Makes an essay with correction helper question with a response template
     * @return qtype_essayhelper_question
     */
    public function make_essayhelper_question_responsetemplate() {
        $q = $this->initialise_essayhelper_question();
        $q->responsetemplate = 'Once upon a time';
        return $q;
    }
}
