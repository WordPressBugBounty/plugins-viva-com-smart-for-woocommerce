<?php

namespace VivaComSmartCheckout\Vivawallet\VivawalletPhp\Api;

use VivaComSmartCheckout\Vivawallet\VivawalletPhp\Api\Request\OrderRequest;
use VivaComSmartCheckout\Vivawallet\VivawalletPhp\Application;
use VivaComSmartCheckout\Vivawallet\VivawalletPhp\Http\Authentication\Authentication;
use VivaComSmartCheckout\Vivawallet\VivawalletPhp\Http\Client;
use VivaComSmartCheckout\Vivawallet\VivawalletPhp\Http\Response;
use VivaComSmartCheckout\Vivawallet\VivawalletPhp\Utils\Utils;
/**
 * Api connection for Order
 *
 * @package VivaWallet\Api
 */
class OrderClient
{
    private $httpClient;
    private $authentication;
    /**
     * Order constructor.
     *
     * @param Authentication $authentication authentication for api connection
     */
    public function __construct(Authentication $authentication, $config = [])
    {
        $this->authentication = $authentication;
        $this->httpClient = new Client($config);
    }
    /**
     * Create a payment order.
     *
     * @param array $options array containing options for creating order. Default empty array. Optional.
     * @param Authentication|null $authentication
     *
     * @return Response
     */
    public function createOrder(array $options = [], ?Authentication $authentication = null) : Response
    {
        $authentication = \is_null($authentication) ? $this->authentication : $authentication;
        $headers = Utils::buildHttpHeaders($options, $authentication->getHeader());
        $payload = OrderRequest::getCreateOrder($options);
        $query = '';
        $url = Utils::buildRequestUrl('api', 'pluginOrders', $query, $authentication);
        return $this->httpClient->request('post', $url, ['json' => $payload, 'headers' => $headers]);
    }
    /**
     * Retrieve payment Order.
     *
     * @param array $options options
     * @param Authentication|null $authentication
     *
     * @return Response
     */
    public function retrieveOrder(array $options, ?Authentication $authentication = null) : Response
    {
        $authentication = \is_null($authentication) ? $this->authentication : $authentication;
        $headers = Utils::buildHttpHeaders($options, $authentication->getHeader());
        $query = !empty($options['orderCode']) ? "/{$options['orderCode']}" : '';
        $url = Utils::buildRequestUrl('api', 'order', $query, $authentication);
        return $this->httpClient->request('get', $url, ['headers' => $headers]);
    }
}
