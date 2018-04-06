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


defined('MOODLE_INTERNAL') || die();

/**
 * Stemming class for correction helper question renderer class.
 *
 * @package    qtype
 * @subpackage essayhelper
 * @copyright  2017 Philippe Girard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class qtype_essayhelper_stemmer {
    public function stem($sentence, Stemmer $stemmer) {
        $words = split_words($sentence);
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

    /**
     * @param $sentence
     * @return string[]|false
     */
    protected function split_words($sentence) {
        $words = preg_split('/(\s|\')/', preg_replace('/[^[:alnum:][:space:]]/u', ' ', $sentence));
        return $words;
    }
}