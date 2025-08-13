<?php
/**
 * Copyright (c) 2025 AltaPay
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

namespace Altapay\Response\Embeds;

use Altapay\Response\AbstractResponse;

class Authentication extends AbstractResponse
{
    /**
     * @var "CHALLENGE"|"FRICTIONLESS"|"UNKNOWN"
     */
    public $Flow;

    /**
     * @var "MERCHANT"|"ISSUER"|"UNKNOWN"
     */
    public $Liability;

    /**
     * @var "AUTHENTICATED"|"ACCEPTED"|"NOT_AUTHENTICATED"|"FAILED"|"REJECTED"|"INFORMATIONAL"|"UNKNOWN"
     */
    public $Result;

    /**
     * Version in the format X.X.X (e.g. 1.0.0, 2.1.0, 2.2.3).
     *
     * @var string
     */
    public $Version;

    /**
     * @var "3DSECURE"|"UNKNOWN"
     */
    public $Type;
}
