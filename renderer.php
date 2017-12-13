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
 * Essay for correction helper question renderer class.
 *
 * @package    qtype
 * @subpackage essayhelper
 * @copyright  2017 Philippe Girard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * Inspired by:
 * @package    qtype
 * @subpackage essay
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Generates the output for essay for correction helper questions.
 *
 * @copyright  2017 Philippe Girard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * Inspired by:
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_essayhelper_renderer extends qtype_renderer {
    public function formulation_and_controls(question_attempt $qa,
            question_display_options $options) {

        $question = $qa->get_question();
        $responseoutput = $question->get_format_renderer($this->page);

        // Answer field.
        $step = $qa->get_last_step_with_qt_var('answer');

        if (!$step->has_qt_var('answer') && empty($options->readonly)) {
            // Question has never been answered, fill it with response template.
            $step = new question_attempt_step(array('answer'=>$question->responsetemplate));
        }

        if (empty($options->readonly)) {
            $answer = $responseoutput->response_area_input('answer', $qa,
                    $step, $question->responsefieldlines, $options->context);

        } else {
            $answer = $responseoutput->response_area_read_only('answer', $qa,
                    $step, $question->responsefieldlines, $options->context);
        }

        $result = '';
        $result .= html_writer::tag('div', $question->format_questiontext($qa),
                array('class' => 'qtext'));

        $result .= html_writer::start_tag('div', array('class' => 'ablock'));
        $result .= html_writer::tag('div', $answer, array('class' => 'answer'));
        $result .= html_writer::end_tag('div');

        return $result;
    }

    public function manual_comment(question_attempt $qa, question_display_options $options) {
        if ($options->manualcomment != question_display_options::EDITABLE) {
            return '';
        }

        $question = $qa->get_question();
        return html_writer::nonempty_tag('div', $question->format_text(
                $question->graderinfo, $question->graderinfo, $qa, 'qtype_essay',
                'graderinfo', $question->id), array('class' => 'graderinfo'));
    }
}


/**
 * A base class to abstract out the differences between different type of
 * response format.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class qtype_essayhelper_format_renderer_base extends plugin_renderer_base {
    /**
     * Render the students respone when the question is in read-only mode.
     * @param string $name the variable name this input edits.
     * @param question_attempt $qa the question attempt being display.
     * @param question_attempt_step $step the current step.
     * @param int $lines approximate size of input box to display.
     * @param object $context the context teh output belongs to.
     * @return string html to display the response.
     */
    public abstract function response_area_read_only($name, question_attempt $qa,
            question_attempt_step $step, $lines, $context);

    /**
     * Render the students respone when the question is in read-only mode.
     * @param string $name the variable name this input edits.
     * @param question_attempt $qa the question attempt being display.
     * @param question_attempt_step $step the current step.
     * @param int $lines approximate size of input box to display.
     * @param object $context the context teh output belongs to.
     * @return string html to display the response for editing.
     */
    public abstract function response_area_input($name, question_attempt $qa,
            question_attempt_step $step, $lines, $context);

    /**
     * @return string specific class name to add to the input element.
     */
    protected abstract function class_name();
}


/**
 * An essay format renderer for essays where the student should use a plain
 * input box, but with a normal, proportional font.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_essayhelper_format_plain_renderer extends plugin_renderer_base {
    /**
     * @return string the HTML for the textarea.
     */
    protected function textarea($response, $lines, $attributes) {
        $attributes['class'] = $this->class_name() . ' qtype_essay_response';
        $attributes['rows'] = $lines;
        $attributes['cols'] = 60;
        return html_writer::tag('textarea', s($response), $attributes);
    }

    protected function class_name() {
        return 'qtype_essay_plain';
    }

    public function response_area_read_only($name, $qa, $step, $lines, $context) {
        $studentAnswer = $step->get_qt_var($name).'';
        include('php-stemmer/src/Utf8.php');
        include('php-stemmer/src/Stemmer.php');
        include('php-stemmer/src/Stem.php');
        include('php-stemmer/src/French.php');
        $answerWords = $this->stem($studentAnswer);

        $question = $qa->get_question();
        $keywords = array_keys($this->stem($question->keywords));

        $usedKeywords = array_intersect(array_keys($answerWords), $keywords);

        foreach ($usedKeywords as $keyword) {
            $words = $answerWords[$keyword];
            foreach ($words as $word) {
                $studentAnswer = str_replace($word, '<b><u>' . $word . '</u></b>', $studentAnswer);
            }
        }

        if ((has_capability("mod/quiz:grade", $context) || has_capability("mod/quiz:regrade", $context)) &&
            (array_key_exists('mode', $_GET) && $_GET['mode'] == 'grading')) {
            $output = '';
            $output .= html_writer::start_tag('div', array('class' => 'row'));
            $output .= html_writer::start_tag('div', array('class' => 'col-sm-6'));
            $output .= html_writer::start_tag('h5');
            $output .= format_text('Réponse de l\'étudiant', FORMAT_PLAIN);
            $output .= html_writer::end_tag('h5');
            $output .= nl2br($studentAnswer);
            $output .= html_writer::end_tag('div');
            $output .= html_writer::start_tag('div', array('class' => 'col-sm-6'));
            $output .= html_writer::start_tag('h5');
            $output .= format_text('Réponse de l\'enseignant', FORMAT_PLAIN);
            $output .= html_writer::end_tag('h5');
            $output .= format_text($question->officialanswer, FORMAT_PLAIN);
            $output .= html_writer::end_tag('div');
            $output .= html_writer::end_tag('div');
            return $output;
        }
        else {
            return $this->textarea($step->get_qt_var($name), $lines, array('readonly' => 'readonly'));
        }
    }

    public function stem($sentence) {
        $stemmer = new Wamania\Snowball\French();
        $words = preg_split('/(\s|\')/', preg_replace('/[^[:alnum:][:space:]]/u', ' ', $sentence));
        $stems = array();
        foreach ($words as $word) {
            if ($word) {
                if (Wamania\Snowball\Utf8::check($word)) {
                    $stem = $stemmer->stem($word);
                    if (isset($stems[$stem])) {
                        if (!in_array($word, $stems[$stem])) {
                            $stems[$stem][] = $word;
                        }
                    } else {
                        $stems[$stem] = array($word);
                    }
                } else {
                    $stems[] = $word;
                }
            }
        }
        // Remove empty elements in the array
        //$stems = array_filter($stems, function($value) { return $value !== ''; });
        return $stems;
    }

    public function insert_in_array($key, $value, $array) {
        if (isset($array[$key])) {
            if (!in_array($value, $array[$key])) {
                $array[$key][] = $value;
            }
        } else {
            $array[$key] = array($value);
        }
        return $array;
    }

    public function response_area_input($name, $qa, $step, $lines, $context) {
        $inputname = $qa->get_qt_field_name($name);
        return $this->textarea($step->get_qt_var($name), $lines, array('name' => $inputname)) .
                html_writer::empty_tag('input', array('type' => 'hidden',
                    'name' => $inputname . 'format', 'value' => FORMAT_PLAIN));
    }
}


/**
 * An essay format renderer for essays where the student should use a plain
 * input box with a monospaced font. You might use this, for example, for a
 * question where the students should type computer code.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_essayhelper_format_monospaced_renderer extends qtype_essayhelper_format_plain_renderer {
    protected function class_name() {
        return 'qtype_essay_monospaced';
    }
}
