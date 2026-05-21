<?php

namespace VivaComSmartCheckout\Vivawallet\VivawalletPhp\Api;

use VivaComSmartCheckout\Vivawallet\VivawalletPhp\Api\Request\TransactionRequest;
use VivaComSmartCheckout\Vivawallet\VivawalletPhp\Application;
use VivaComSmartCheckout\Vivawallet\VivawalletPhp\Http\Authentication\Authentication;
use VivaComSmartCheckout\Vivawallet\VivawalletPhp\Http\Client;
use VivaComSmartCheckout\Vivawallet\VivawalletPhp\Http\Response;
use VivaComSmartCheckout\Vivawallet\VivawalletPhp\Utils\Utils;
class TransactionClient
{
    private $httpClient;
    private $authentication;
    public function __construct(Authentication $authentication, $config = [])
    {
        $this->authentication = $authentication;
        $this->httpClient = new Client($config);
    }
    public function getChargeToken(array $options, ?Authentication $authentication = null) : Response
    {
        $authentication = \is_null($authentication) ? $this->authentication : $authentication;
        $headers = Utils::buildHttpHeaders($options, $authentication->getHeader());
        $payload = TransactionRequest::getChargeToken($options);
        $query = '';
        $url = Utils::buildRequestUrl('api', 'nativeChargeToken', $query, $authentication);
        return $this->httpClient->request('post', $url, ['json' => $payload, 'headers' => $headers]);
    }
    public function getDigitalChargeToken(array $options, ?Authentication $authentication = null) : Response
    {
        $headers = Utils::buildHttpHeaders($options, $authentication->getHeader());
        $payload = TransactionRequest::getDigitalChargeToken($options);
        $query = '';
        $url = Utils::buildRequestUrl('api', 'nativeDigitalChargeToken', $query, $authentication);
        return $this->httpClient->request('post', $url, ['json' => $payload, 'headers' => $headers]);
    }
    public function createRecurringTransaction(array $options = [], ?Authentication $authentication = null) : Response
    {
        $authentication = \is_null($authentication) ? $this->authentication : $authentication;
        $headers = Utils::buildHttpHeaders($options, $authentication->getHeader());
        $payload = TransactionRequest::getCreateRecurringTransaction($options);
        $query = "/{$options['transactionId']}:charge";
        $url = Utils::buildRequestUrl('api', 'acquiring', $query, $authentication);
        return $this->httpClient->request('post', $url, ['json' => $payload, 'headers' => $headers]);
    }
    public function createTransaction(array $options = [], ?Authentication $authentication = null) : Response
    {
        $authentication = \is_null($authentication) ? $this->authentication : $authentication;
        $headers = Utils::buildHttpHeaders($options, $authentication->getHeader());
        $payload = TransactionRequest::getCreateTransaction($options);
        $query = '';
        $url = Utils::buildRequestUrl('api', 'nativeTransactions', $query, $authentication);
        return $this->httpClient->request('post', $url, ['json' => $payload, 'headers' => $headers]);
    }
    public function refundTransaction(array $options = [], ?string $idempotencyKey = null, ?Authentication $authentication = null) : Response
    {
        $authentication = \is_null($authentication) ? $this->authentication : $authentication;
        $headers = Utils::buildHttpHeaders($options, $authentication->getHeader());
        $payload = TransactionRequest::getRefundTransaction($options, $idempotencyKey);
        $query = "/{$options['transactionId']}";
        $url = Utils::buildRequestUrl('api', 'acquiring', $query, $authentication);
        return $this->httpClient->request('delete', $url, ['query' => $payload, 'headers' => $headers]);
    }
    public function retrieveTransactionById(string $transactionId, ?Authentication $authentication = null) : Response
    {
        $authentication = \is_null($authentication) ? $this->authentication : $authentication;
        return $this->retrieveTransaction(['transactionId' => $transactionId], $authentication);
    }
    private function retrieveTransaction(array $options, ?Authentication $authentication = null) : Response
    {
        $authentication = \is_null($authentication) ? $this->authentication : $authentication;
        $headers = Utils::buildHttpHeaders($options, $authentication->getHeader());
        $query = !empty($options['transactionId']) ? "/{$options['transactionId']}" : '';
        $url = Utils::buildRequestUrl('api', 'transactions', $query, $authentication);
        return $this->httpClient->request('get', $url, ['headers' => $headers]);
    }
    public function captureTransaction(array $options = [], ?Authentication $authentication = null) : Response
    {
        $authentication = \is_null($authentication) ? $this->authentication : $authentication;
        $headers = Utils::buildHttpHeaders($options, $authentication->getHeader());
        $payload = TransactionRequest::getCaptureTransaction($options);
        $query = "/{$options['transactionId']}";
        $url = Utils::buildRequestUrl('api', 'nativeTransactions', $query, $authentication);
        return $this->httpClient->request('post', $url, ['json' => $payload, 'headers' => $headers]);
    }
    public function captureAuthorizedTransaction(array $options = [], ?Authentication $authentication = null) : Response
    {
        $authentication = \is_null($authentication) ? $this->authentication : $authentication;
        $headers = Utils::buildHttpHeaders($options, $authentication->getHeader());
        $payload = TransactionRequest::getCaptureAuthorizedTransaction($options);
        $query = "/{$options['transactionId']}:charge";
        $url = Utils::buildRequestUrl('api', 'acquiring', $query, $authentication);
        return $this->httpClient->request('post', $url, ['json' => $payload, 'headers' => $headers]);
    }
    public function voidAuthorizedTransaction(array $options = [], ?string $idempotencyKey = null, ?Authentication $authentication = null) : Response
    {
        $authentication = \is_null($authentication) ? $this->authentication : $authentication;
        $headers = Utils::buildHttpHeaders($options, $authentication->getHeader());
        $payload = TransactionRequest::getVoidAuthorizedTransaction($options, $idempotencyKey);
        $query = "/{$options['transactionId']}";
        $url = Utils::buildRequestUrl('api', 'acquiring', $query, $authentication);
        return $this->httpClient->request('delete', $url, ['query' => $payload, 'headers' => $headers]);
    }
}
