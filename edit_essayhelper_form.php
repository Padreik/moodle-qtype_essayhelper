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
 * Defines the editing form for the essay (for correction helper) question type.
 *
 * @package    qtype
 * @subpackage essayhelper
 * @copyright  2017 Philippe Girard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * Inspired by:
 * @package    qtype
 * @subpackage essay
 * @copyright  2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Essay helper question type editing form.
 *
 * @copyright  2017 Philippe Girard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * Inspired by:
 * @copyright  2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_essayhelper_edit_form extends question_edit_form {

    protected function definition_inner($mform) {
        $qtype = question_bank::get_qtype('essayhelper');

        $mform->addElement('header', 'essayhelper', get_string('essayhelperheader', 'qtype_essayhelper'));
        $mform->setExpanded('essayhelper');
        $mform->addElement('textarea', 'officialanswer', get_string('officialanswer', 'qtype_essayhelper'),
            array('rows' => 10, 'cols' => 100));
        $mform->addElement('textarea', 'keywords', get_string('keywords', 'qtype_essayhelper'),
            array('rows' => 10, 'cols' => 60));
        $mform->addHelpButton('keywords', 'keywords', 'qtype_essayhelper');

        $mform->addElement('header', 'responseoptions', get_string('responseoptions', 'qtype_essay'));
        $mform->setExpanded('responseoptions');

        $mform->addElement('select', 'responseformat',
            get_string('responseformat', 'qtype_essay'), $qtype->response_formats());
        $mform->setDefault('responseformat', 'editor');

        $mform->addElement('select', 'responserequired',
                get_string('responserequired', 'qtype_essay'), $qtype->response_required_options());
        $mform->setDefault('responserequired', 1);

        $mform->addElement('select', 'responsefieldlines',
                get_string('responsefieldlines', 'qtype_essay'), $qtype->response_sizes());
        $mform->setDefault('responsefieldlines', 15);

        $mform->addElement('header', 'responsetemplateheader', get_string('responsetemplateheader', 'qtype_essay'));
        $mform->addElement('textarea', 'responsetemplate', get_string('responsetemplate', 'qtype_essay'),
                array('rows' => 10, 'cols' => 100));
        $mform->addHelpButton('responsetemplate', 'responsetemplate', 'qtype_essay');

        $mform->addElement('header', 'graderinfoheader', get_string('graderinfoheader', 'qtype_essay'));
        $mform->setExpanded('graderinfoheader');
        $mform->addElement('editor', 'graderinfo', get_string('graderinfo', 'qtype_essay'),
                array('rows' => 10), $this->editoroptions);
    }

    protected function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);

        if (empty($question->options)) {
            return $question;
        }

        $question->responseformat = $question->options->responseformat;
        $question->responserequired = $question->options->responserequired;
        $question->responsefieldlines = $question->options->responsefieldlines;

        $draftid = file_get_submitted_draft_itemid('graderinfo');
        $question->graderinfo = array();
        $question->graderinfo['text'] = file_prepare_draft_area(
            $draftid,           // Draftid
            $this->context->id, // context
            'qtype_essayhelper',      // component
            'graderinfo',       // filarea
            !empty($question->id) ? (int) $question->id : null, // itemid
            $this->fileoptions, // options
            $question->options->graderinfo // text.
        );
        $question->graderinfo['format'] = $question->options->graderinfoformat;
        $question->graderinfo['itemid'] = $draftid;

        $question->responsetemplate = $question->options->responsetemplate;
        $question->officialanswer = $question->options->officialanswer;
        $question->keywords = $question->options->keywords;

        return $question;
    }

    public function qtype() {
        return 'essayhelper';
    }
}
