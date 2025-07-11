<?php
/**
 * Copyright (c) 2016 Martin Aarhof
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Altapay\Types;

class LanguageTypes implements TypeInterface
{
    /**
     * Allowed languages
     * nb, nn will be converted to no.
     * ee will be converted to et
     *
     * @var list<string>
     */
    private static $languages = [
        'br', 'ca', 'cs', 'cy', 'da', 'de', 'el', 'en', 'es', 'fi', 'fr', 'hr', 'hu', 'is', 'ja',
        'lt', 'lv', 'nl', 'no', 'nb', 'nn', 'pl', 'sv','th', 'tr', 'zh',
        'et', 'ee', 'it', 'pt', 'ro', 'ru', 'sk', 'sl', 'eu'
    ];

    /**
     * Get allowed values
     *
     * @return list<string>
     */
    public static function getAllowed()
    {
        return self::$languages;
    }

    /**
     * Is the requested value allowed
     *
     * @param string $value
     *
     * @return bool
     */
    public static function isAllowed($value)
    {
        return in_array($value, self::$languages);
    }
}
