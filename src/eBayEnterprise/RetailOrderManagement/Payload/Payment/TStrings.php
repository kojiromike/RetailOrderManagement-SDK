<?php
/**
 * Copyright (c) 2013-2014 eBay Enterprise, Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright   Copyright (c) 2013-2014 eBay Enterprise, Inc. (http://www.ebayenterprise.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace eBayEnterprise\RetailOrderManagement\Payload\Payment;

trait TStrings
{
    /**
     * Trim any white space and return the resulting string truncating to $maxLength.
     *
     * Return null if the result is an empty string or not a string
     *
     * @param string $string
     * @param int $maxLength
     * @return string or null
     */
    protected function cleanString($string, $maxLength)
    {
        $value = null;
        if (is_string($string)) {
            $trimmed = substr(trim($string), 0, $maxLength);
            $value = empty($trimmed) ? null : $trimmed;
        }
        return $value;
    }

    /**
     * Convert "true", "false", "1" or "0" to boolean
     * Everything else returns null
     *
     * @param $string
     * @return bool|null
     */
    protected function convertStringToBoolean($string)
    {
        if (!is_string($string)) {
            return null;
        }
        $string = strtolower($string);
        return (($string === 'true') || ($string === '1'));
    }

    /**
     * Normalize any whitespace characters, tab, new line, etc., with a single
     * space character. Does not collapse whitespace.
     *
     * @param string
     * @return string
     */
    protected function normalizeWhitespace($string)
    {
        return preg_replace('#\s#', ' ', $string);
    }

    /**
     * Check for the value to be a valid string for use as an id. Does not
     * validated that the string is unique. Returns null if the string is not
     * valid.
     *
     * @param string
     * @return string|null
     */
    protected function cleanId($string)
    {
        return preg_match('#^[a-z_][\w.-]*$#i', $string) ? $string : null;
    }
}
