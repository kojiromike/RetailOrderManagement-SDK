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

use eBayEnterprise\RetailOrderManagement\Payload\IValidatorIterator;
use eBayEnterprise\RetailOrderManagement\Payload\ISchemaValidator;
use eBayEnterprise\RetailOrderManagement\Payload\Exception;
use eBayEnterprise\RetailOrderManagement\Payload\TPayload;

/**
 * StoredValueBalanceRequest
 * @package eBayEnterprise\RetailOrderManagement\Payload\Payment
 *
 */
class StoredValueRedeemVoidRequest implements IStoredValueRedeemVoidRequest
{
    use TPayload, TPaymentContext;

    protected $amount;
    protected $pin;
    protected $currencyCode;
    protected $requestId;
    /**
     * @param IValidatorIterator $validators Payload object validators
     * @param ISchemaValidator $schemaValidator Serialized object schema validator
     */
    public function __construct(IValidatorIterator $validators, ISchemaValidator $schemaValidator)
    {
        $this->extractionPaths = [
            'orderId' => 'string(x:PaymentContext/x:OrderId)',
            'cardNumber' => 'string(x:PaymentContext/x:PaymentAccountUniqueId)',
            'amount' => 'number(x:Amount)',
            'currencyCode' => 'string(x:Amount/@currencyCode)',
            'requestId' => ' string(@requestId)',
        ];
        $this->optionalExtractionPaths = [
            'pin' => 'x:Pin',
        ];
        $this->booleanExtractionPaths = [
            'panIsToken' => 'string(x:PaymentContext/x:PaymentAccountUniqueId/@isToken)'
        ];
        $this->validators = $validators;
        $this->schemaValidator = $schemaValidator;
    }

    /**
     * The amount to void.
     *
     * xsd note: 1-8 characters, exclude if empty
     *           pattern (\d{1,8})?
     * return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param string $amount
     * @return self
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * The personal identification number or code associated with a giftcard
     * account unique id
     *
     * xsd note: 1-8 characters, exclude if empty
     *           pattern (\d{1,8})?
     * return string
     */
    public function getPin()
    {
        return $this->pin;
    }

    /**
     * @param string $pin
     * @return self
     */
    public function setPin($pin)
    {
        $this->pin = $pin;
        return $this;
    }

    /**
     * The 3-character ISO 4217 code that represents
     * the type of currency being used for a transaction.
     *
     * @link http://www.iso.org/iso/home/standards/currency_codes.htm
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    /**
     * @param string $code
     * @return self
     */
    public function setCurrencyCode($code)
    {
        $this->currencyCode = $code;
        return $this;
    }

    /**
     * Identifier for this request.
     * On serialization, a request id will be generated if not already set.
     *
     * xsd notes: required, 1-40 characters
     * @return string
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * @param string $requestId
     * @return self
     */
    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;
        return $this;
    }

    /**
     * Name, value pairs of root attributes
     *
     * @return array
     */
    protected function getRootAttributes()
    {
        return [
            'xmlns' => $this->getXmlNamespace(),
            'requestId' => $this->getRequestId(),
        ];
    }

    /**
     * Serialize the various parts of the payload into XML strings and
     * simply concatenate them together.
     * @return string
     */
    protected function serializeContents()
    {
        return $this->serializePaymentContext()
            . $this->serializePin()
            . sprintf(
                '<Amount currencyCode="%s">%s</Amount>',
                $this->getCurrencyCode(),
                $this->getAmount()
            );
    }

    /**
     * Return the XML representation of the PIN if it exists;
     * otherwise, return the empty string.
     * @return string
     */
    protected function serializePin()
    {
        return $this->pin ? sprintf('<Pin>%s</Pin>', $this->getPin()) : '';
    }

    /**
     * Create an XML string representing the PaymentContext nodes
     * @return string
     */
    protected function serializePaymentContext()
    {
        $template = '<PaymentContext>'
            . '<OrderId>%s</OrderId>'
            . '<PaymentAccountUniqueId isToken="%s">%s</PaymentAccountUniqueId>'
            . '</PaymentContext>';
        return sprintf(
            $template,
            $this->getOrderId(),
            $this->getPanIsToken() ? 'true' : 'false',
            $this->getCardNumber()
        );
    }

    /**
     * Return the schema file path.
     * @return string
     */
    protected function getSchemaFile()
    {
        return __DIR__ . '/schema/' . static::XSD;
    }

    /**
     * Return the name of the xml root node.
     *
     * @return string
     */
    protected function getRootNodeName()
    {
        return static::ROOT_NODE;
    }

    /**
     * The XML namespace for the payload.
     *
     * @return string
     */
    protected function getXmlNamespace()
    {
        return static::XML_NS;
    }
}
