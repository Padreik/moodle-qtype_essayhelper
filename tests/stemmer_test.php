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
 * Unit tests for the essay with correction helper stemmer class.
 *
 * @package    qtype
 * @subpackage essayhelper
 * @copyright  2018 Philippe Girard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/essayhelper/stemmer.php');


/**
 * Unit tests for the stemmer class.
 *
 * @copyright  2018 Philippe Girard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_essayhelper_stemmer_test extends basic_testcase {
    public function test_get_stemmer() {
        $stemmer = new qtype_essayhelper_stemmer();

        // Set get_stemmer function accessible
        $get_stemmer = $this->get_protected_function($stemmer, "get_stemmer");

        // Get all available languages
        $languages = PHPUnit\Framework\Assert::readAttribute($stemmer, "languages");

        // Test all languages
        foreach ($languages as $langCode => $lang) {
            $langStemmer = $get_stemmer->invokeArgs($stemmer, array($langCode));
            $this->assertEquals((new \ReflectionClass($langStemmer))->getShortName(), $lang);
        }
    }

    public function test_get_stemmer_non_existing() {
        $stemmer = new qtype_essayhelper_stemmer();

        // Set get_stemmer function accessible
        $get_stemmer = $this->get_protected_function($stemmer, "get_stemmer");

        // Test non existing language
        $langStemmer = $get_stemmer->invokeArgs($stemmer, array("zzz"));
        $this->assertEquals((new \ReflectionClass($langStemmer))->getShortName(), "English");
    }

    public function test_split_words() {
        $stemmer = new qtype_essayhelper_stemmer();
        $split_words = $this->get_protected_function($stemmer, "split_words");

        $this->assertEquals($split_words->invokeArgs($stemmer, array("I love potatoes")), array("I", "love", "potatoes"));
        $this->assertEquals($split_words->invokeArgs($stemmer, array("I+love+potatoes")), array("I", "love", "potatoes"));
        $this->assertEquals($split_words->invokeArgs($stemmer, array("I-love\npotatoes")), array("I", "love", "potatoes"));
        $this->assertEquals($split_words->invokeArgs($stemmer, array("")), array());
        $this->assertEquals($split_words->invokeArgs($stemmer, array("-\n    ' %")), array());
    }

    public function test_make_stem_array() {
        $stemmer = new qtype_essayhelper_stemmer();
        $make_stem_array = $this->get_protected_function($stemmer, "make_stem_array");

        $snowball_stemmer = $this->createMock('\\Wamania\\Snowball\\Stemmer', array('stem'));
        $snowball_stemmer->method('stem')->willReturn('test');

        $snowball_utf8 = new qtype_essayhelper_stemmer_utf8_mock();

        $make_stem_array = $this->get_protected_function($stemmer, 'make_stem_array');

        $stemmed_array = $make_stem_array->invokeArgs($stemmer, array("test asdf", $snowball_stemmer, $snowball_utf8));
        $expected_stemmed_array = array("test" => array("test", "asdf"));

        $this->assertEquals($stemmed_array, $expected_stemmed_array);
    }

    protected function get_protected_function($stemmer, $protectedFunctionName) {
        $reflection = new ReflectionClass($stemmer);
        $protectedFunction = $reflection->getMethod($protectedFunctionName);
        $protectedFunction->setAccessible(true);
        return $protectedFunction;
    }
}

class qtype_essayhelper_stemmer_utf8_mock extends Wamania\Snowball\Utf8 {
    public static function check($str) {
        return true;
    }
}