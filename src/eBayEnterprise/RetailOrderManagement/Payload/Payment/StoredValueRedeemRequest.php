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

use eBayEnterprise\RetailOrderManagement\Payload\Exception;
use eBayEnterprise\RetailOrderManagement\Payload\ISchemaValidator;
use eBayEnterprise\RetailOrderManagement\Payload\IValidatorIterator;
use eBayEnterprise\RetailOrderManagement\Payload\TPayload;

/**
 * Class StoredValueRedeemRequest
 * @package eBayEnterprise\RetailOrderManagement\Payload\Payment
 */
class StoredValueRedeemRequest implements IStoredValueRedeemRequest
{
    use TPayload, TPaymentContext;
    /** @var string */
    protected $requestId;
    /** @var string */
    protected $pin;
    /** @var float */
    protected $amount;
    /** @var string */
    protected $currencyCode;
    protected $responseCode;

    public function __construct(IValidatorIterator $validators, ISchemaValidator $schemaValidator)
    {
        $this->extractionPaths = [
            'orderId' => 'string(x:PaymentContext/x:OrderId)',
            'cardNumber' => 'string(x:PaymentContext/x:PaymentAccountUniqueId)',
            'amount' => 'number(x:Amount)',
            'currencyCode' => 'string(x:Amount/@currencyCode)',
            'requestId' => 'string(@requestId)',
        ];
        $this->booleanExtractionPaths = [
            'panIsToken' => 'string(x:PaymentContext/x:PaymentAccountUniqueId/@isToken)',
        ];
        $this->optionalExtractionPaths = [
            'pin' => 'x:Pin',
        ];

        $this->validators = $validators;
        $this->schemaValidator = $schemaValidator;
    }

    /**
     * The result of the request transaction.
     *
     * xsd note: possible values: Fail, Success, Timeout
     * @return string
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * @param string
     * @return self
     */
    public function setResponseCode($code)
    {
        $this->responseCode = $code;
        return $this;
    }

    /**
     * @param string
     * @return self
     */
    public function setCurrencyCodeRedeemed($code)
    {
        $this->currencyCode = $code;
        return $this;
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
        . $this->serializeAmounts('');
    }

    /**
     * Build the Pin node
     *
     * @return string
     */
    protected function serializePin()
    {
        $pin = $this->getPin();
        if ($pin === '') {
            return '';
        }
        return "<Pin>{$this->getPin()}</Pin>";
    }

    public function getPin()
    {
        return $this->pin;
    }

    public function setPin($pin)
    {
        $this->pin = $this->cleanString($pin, 8);
        return $this;
    }

    /**
     * Build the Amount node
     * @param string $amountType either 'AmountRedeemed' or 'BalanceAmount'
     * @return string
     */
    protected function serializeAmounts($amountType)
    {
        return sprintf('<Amount currencyCode="%s">%1.02F</Amount>', $this->getCurrencyCode(), $this->getAmount());
    }

    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode($code)
    {
        $value = null;

        $cleaned = $this->cleanString($code, 3);
        if ($cleaned !== null) {
            if (!strlen($cleaned) < 3) {
                $value = $cleaned;
            }
        }
        $this->currencyCode = $value;

        return $this;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount)
    {
        if (is_float($amount)) {
            $this->amount = round($amount, 2, PHP_ROUND_HALF_UP);
        } else {
            $this->amount = null;
        }
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
     * The XML namespace for the payload.
     *
     * @return string
     */
    protected function getXmlNamespace()
    {
        return static::XML_NS;
    }

    /**
     * RequestId is used to globally identify a request message and is used
     * for duplicate request protection.
     *
     * xsd restrictions: 1-40 characters
     * @return string
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    public function setRequestId($requestId)
    {
        $this->requestId = $this->cleanString($requestId, 40);
        return $this;
    }

    /**
     * Serialize Root Attributes
     */
    protected function serializeRootAttrs()
    {
        return sprintf(
            '%s="%s" %s="%s"',
            'xmlns',
            static::XML_NS,
            'requestId',
            $this->getRequestId()
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
}
