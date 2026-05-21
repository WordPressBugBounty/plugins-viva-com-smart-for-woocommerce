<?php

namespace VivaComSmartCheckout\Vivawallet\VivawalletPhp\Api\Request;

use VivaComSmartCheckout\Vivawallet\VivawalletPhp\Application;
class TransactionRequest
{
    public static function getChargeToken(array $options) : array
    {
        return \array_filter(['amount' => $options['amount'], 'holderName' => $options['holderName'] ?? null, 'number' => $options['number'] ?? null, 'expirationMonth' => $options['expirationMonth'] ?? null, 'expirationYear' => $options['expirationYear'] ?? null, 'cvc' => $options['cvc'] ?? null, 'sessionRedirectUrl' => $options['sessionRedirectUrl'] ?? null, 'installments' => $options['installments'] ?? null, 'token' => $options['token'] ?? null], function ($value) {
            return !\is_null($value);
        });
    }
    public static function getDigitalChargeToken(array $options) : array
    {
        return ['sourceCode' => $options['sourceCode'], 'providerId' => $options['providerId'], 'validationUrl' => $options['validationUrl']];
    }
    public static function getCreateRecurringTransaction(array $options) : array
    {
        return \array_filter(['amount' => $options['amount'], 'currencyCode' => Application::getCurrencyCode($options['currencyCode'], \false) ?? null, 'sourceCode' => $options['sourceCode'] ?? null, 'merchantTrns' => $options['messages']['merchant'] ?? null, 'customerTrns' => $options['messages']['customer'] ?? null], function ($value) {
            return !\is_null($value);
        });
    }
    public static function getCreateTransaction(array $options = []) : array
    {
        return \array_filter(['amount' => $options['amount'], 'currencyCode' => Application::getCurrencyCode($options['currencyCode'], \false), 'chargeToken' => $options['chargeToken'], 'preauth' => $options['preauth'] ?? null, 'tipAmount' => $options['tipAmount'] ?? null, 'sourceCode' => $options['sourceCode'] ?? null, 'installments' => $options['installments'] ?? null, 'merchantTrns' => $options['messages']['merchant'] ?? null, 'customerTrns' => $options['messages']['customer'] ?? null, 'allowsRecurring' => $options['allowsRecurring'] ?? null, 'paymentMethodId' => $options['paymentMethodId'] ?? null, 'customer' => ['email' => $options['customer']['email'] ?? null, 'phone' => $options['customer']['phone'] ?? null, 'fullName' => $options['customer']['fullName'] ?? null, 'requestLang' => $options['customer']['requestLang'] ?? null, 'countryCode' => $options['customer']['countryCode'] ?? null]], function ($value) {
            return !\is_null($value);
        });
    }
    public static function getRefundTransaction(array $options, ?string $idempotencyKey = null) : array
    {
        return \array_filter(['amount' => $options['amount'], 'currencyCode' => Application::getCurrencyCode($options['currencyCode'], \false), 'sourceCode' => $options['sourceCode'], 'idempotencyKey' => $idempotencyKey], function ($v) {
            return !\is_null($v);
        });
    }
    public static function getCaptureTransaction(array $options) : array
    {
        return ['amount' => $options['amount'], 'currencyCode' => Application::getCurrencyCode($options['currencyCode'], \false), 'sourceCode' => $options['sourceCode']];
    }
    public static function getCaptureAuthorizedTransaction(array $options = []) : array
    {
        return \array_filter(['amount' => $options['amount'], 'currencyCode' => Application::getCurrencyCode($options['currencyCode'], \false) ?? null, 'sourceCode' => $options['sourceCode'] ?? null, 'merchantTrns' => $options['messages']['merchant'] ?? null, 'customerTrns' => $options['messages']['customer'] ?? null], function ($value) {
            return !\is_null($value);
        });
    }
    public static function getVoidAuthorizedTransaction(array $options, ?string $idempotencyKey = null) : array
    {
        return \array_filter(['amount' => $options['amount'], 'currencyCode' => Application::getCurrencyCode($options['currencyCode'], \false), 'sourceCode' => $options['sourceCode'], 'idempotencyKey' => $idempotencyKey], function ($v) {
            return !\is_null($v);
        });
    }
}
