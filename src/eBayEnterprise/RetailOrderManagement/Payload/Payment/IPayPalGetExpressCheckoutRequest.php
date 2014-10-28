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

/**
 * Interface IPayPalGetExpressCheckoutRequest
 * @package eBayEnterprise\RetailOrderManagement\Payload\Payment
 */
interface IPayPalGetExpressCheckoutRequest extends IPayPalGetExpressCheckout
{
    const ROOT_NODE = 'PayPalGetExpressCheckoutRequest';

    /**
     * The timestamped token value that was returned by PayPalSetExpressCheckoutReply and
     * passed on PayPalGetExpressCheckoutRequest.
     * Character length and limitations: 20 single-byte characters
     *
     * @return string
     */
    public function getToken();

    /**
     * @param string
     * @return self
     */
    public function setToken($token);

    /**
     * The 3-character ISO 4217 code that represents
     * the type of currency being used for a transaction.
     *
     * @link http://www.iso.org/iso/home/standards/currency_codes.htm
     * @return string
     */
    public function getCurrencyCode();

    /**
     * @param string
     * @return self
     */
    public function setCurrencyCode($code);
}
