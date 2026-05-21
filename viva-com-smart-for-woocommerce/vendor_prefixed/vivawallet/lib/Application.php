<?php

namespace VivaComSmartCheckout\Vivawallet\VivawalletPhp;

class Application
{
    public const VIVAWALLET_ABBREVIATION = 'VW';
    public const SDK_VERSION = '2.3.1';
    public const SOURCE_NAME_FORMAT = 'Viva Wallet For %s - %s';
    public const PARAM_CANCEL_ORDER = 'cancel';
    public const BASE_URLS = ['demo' => ['accounts' => 'https://demo-accounts.vivapayments.com', 'api' => 'https://demo-api.vivapayments.com', 'default' => 'https://demo.vivapayments.com'], 'live' => ['accounts' => 'https://accounts.vivapayments.com', 'api' => 'https://api.vivapayments.com', 'default' => 'https://www.vivapayments.com']];
    public const ENDPOINTS = ['accountsToken' => '/connect/token', 'sources' => '/plugins/v1/sources', 'webhook' => '/plugins/v1/webhooks', 'webhookToken' => '/plugins/v1/webhooks/token', 'order' => '/plugins/v1/orders', 'pluginOrders' => '/checkout/v2/orders', 'merchants' => '/plugins/v1/merchants', 'orders' => '/api/orders', 'transactions' => '/checkout/v2/transactions', 'smartCheckout' => '/web/checkout', 'acquiring' => '/acquiring/v1/transactions', 'diagnostics' => '/diagnostics/v1/log', 'nativeTransactions' => '/nativecheckout/v2/transactions', 'nativeChargeToken' => '/nativecheckout/v2/chargetokens', 'nativeDigitalChargeToken' => '/nativecheckout/v2/chargetokens:digitize'];
    public const EVENTS = [
        // 101–114 General
        'Request' => 101,
        'Response' => 102,
        'GeneralInfo' => 103,
        'GeneralError' => 104,
        'GeneralException' => 105,
        'ApplicationException' => 106,
        'QueueError' => 107,
        // 115–130 Shopify (core)
        'ShopifyNotify' => 115,
        'ShopifyNotifyError' => 116,
        'ShopifyMutation' => 117,
        'ShopifyMutationError' => 118,
        'ShopifyAdminApiGetShop' => 119,
        'ShopifyAdminApiGetShopError' => 120,
        'ShopifyAccessToken' => 121,
        'ShopifyAccessTokenError' => 122,
        // 131–150 Webhooks / Compatibility / DB ops
        'GetWebhookCallback' => 131,
        'PostWebhookCallback' => 132,
        'CompatibilityError' => 133,
        'GeneralDatabaseOperation' => 134,
        'DatabaseInsert' => 135,
        'DatabaseUpdate' => 136,
        'DatabaseDelete' => 137,
        'DatabaseOperationError' => 138,
        'DatabaseOperationException' => 139,
        // 151–220 Viva
        'VivaRequest' => 151,
        'VivaResponse' => 152,
        'VivaError' => 153,
        'VivaToken' => 154,
        'VivaTokenError' => 155,
        'GetVivaToken' => 156,
        'GetVivaTokenError' => 157,
        'GetVivaTokenException' => 158,
        'VivaAccounts' => 160,
        'VivaAccountsError' => 161,
        'GetVivaTransaction' => 162,
        'GetVivaTransactionError' => 163,
        'GetVivaTransactionException' => 164,
        'GetVivaOrderRequest' => 165,
        'GetVivaOrderResponse' => 166,
        'GetVivaOrderError' => 167,
        'GetVivaOrderException' => 168,
        'GetVivaSourceRequest' => 169,
        'GetVivaSourceResponse' => 170,
        'GetVivaSourceError' => 171,
        'GetVivaSourceException' => 172,
        'GetVivaSourcesRequest' => 173,
        'GetVivaSourcesResponse' => 174,
        'GetVivaSourcesError' => 175,
        'GetVivaSourcesException' => 176,
        'CreateVivaSourceRequest' => 177,
        'CreateVivaSourceResponse' => 178,
        'CreateVivaSourceError' => 179,
        'CreateVivaSourceException' => 180,
        'CreateOrderRequest' => 181,
        'CreateOrderResponse' => 182,
        'CreateOrderError' => 183,
        'CreateOrderException' => 184,
        'CreateVivaTransactionRequest' => 185,
        'CreateVivaTransactionResponse' => 186,
        'CreateVivaTransactionError' => 187,
        'CreateVivaTransactionException' => 188,
        'FinalizePayment' => 189,
        'FinalizePaymentError' => 190,
        'FinalizePaymentException' => 191,
        'GetVivaMerchantInfoRequest' => 192,
        'GetVivaMerchantInfoResponse' => 193,
        'GetVivaMerchantInfoError' => 194,
        'GetVivaMerchantInfoException' => 195,
        'ProcessSubscription' => 196,
        'ProcessSubscriptionError' => 197,
        'ProcessSubscriptionException' => 198,
        // 221–250 Azure
        'AzureRequest' => 221,
        'AzureResponse' => 222,
        'AzureException' => 223,
        'AzureKeyVault' => 224,
        'AzureKeyVaultError' => 225,
        // 251–260 Redis
        'RedisError' => 251,
        'RedisException' => 252,
        // 261–300+ Payments infra (Refund/Capture/Void/Config/Installments/Plugin/Store)
        'RefundRequest' => 261,
        'RefundResponse' => 262,
        'RefundError' => 263,
        'RefundException' => 264,
        'CaptureRequest' => 265,
        'CaptureResponse' => 266,
        'CaptureError' => 267,
        'CaptureException' => 268,
        'VoidRequest' => 269,
        'VoidResponse' => 270,
        'VoidError' => 271,
        'VoidException' => 272,
        'StoreConfiguration' => 273,
        'StoreConfigurationError' => 274,
        'Installments' => 275,
        'InstallmentsError' => 276,
        'InstallmentsException' => 277,
        'PluginInstall' => 278,
        'PluginInstallError' => 279,
        'PluginUpdate' => 280,
        'PluginUpdateError' => 281,
    ];
    public const CURRENCIES = ['AED' => '784', 'AFN' => '971', 'ALL' => '008', 'AMD' => '051', 'ANG' => '532', 'AOA' => '973', 'ARS' => '032', 'AUD' => '036', 'AWG' => '533', 'AZN' => '944', 'BAM' => '977', 'BBD' => '052', 'BDT' => '050', 'BGN' => '975', 'BHD' => '048', 'BIF' => '108', 'BMD' => '060', 'BND' => '096', 'BOB' => '068', 'BOV' => '984', 'BRL' => '986', 'BSD' => '044', 'BTN' => '064', 'BWP' => '072', 'BYN' => '933', 'BZD' => '084', 'CAD' => '124', 'CDF' => '976', 'CHE' => '947', 'CHF' => '756', 'CHW' => '948', 'CLF' => '990', 'CLP' => '152', 'CNY' => '156', 'COP' => '170', 'COU' => '970', 'CRC' => '188', 'CUC' => '931', 'CUP' => '192', 'CVE' => '132', 'CZK' => '203', 'DJF' => '262', 'DKK' => '208', 'DOP' => '214', 'DZD' => '012', 'EGP' => '818', 'ERN' => '232', 'ETB' => '230', 'EUR' => '978', 'FJD' => '242', 'FKP' => '238', 'GBP' => '826', 'GEL' => '981', 'GHS' => '936', 'GIP' => '292', 'GMD' => '270', 'GNF' => '324', 'GTQ' => '320', 'GYD' => '328', 'HKD' => '344', 'HNL' => '340', 'HRK' => '191', 'HTG' => '332', 'HUF' => '348', 'IDR' => '360', 'ILS' => '376', 'INR' => '356', 'IQD' => '368', 'IRR' => '364', 'ISK' => '352', 'JMD' => '388', 'JOD' => '400', 'JPY' => '392', 'KES' => '404', 'KGS' => '417', 'KHR' => '116', 'KMF' => '174', 'KPW' => '408', 'KRW' => '410', 'KWD' => '414', 'KYD' => '136', 'KZT' => '398', 'LAK' => '418', 'LBP' => '422', 'LKR' => '144', 'LRD' => '430', 'LSL' => '426', 'LYD' => '434', 'MAD' => '504', 'MDL' => '498', 'MGA' => '969', 'MKD' => '807', 'MMK' => '104', 'MNT' => '496', 'MOP' => '446', 'MRU' => '929', 'MUR' => '480', 'MVR' => '462', 'MWK' => '454', 'MXN' => '484', 'MXV' => '979', 'MYR' => '458', 'MZN' => '943', 'NAD' => '516', 'NGN' => '566', 'NIO' => '558', 'NOK' => '578', 'NPR' => '524', 'NZD' => '554', 'OMR' => '512', 'PAB' => '590', 'PEN' => '604', 'PGK' => '598', 'PHP' => '608', 'PKR' => '586', 'PLN' => '985', 'PYG' => '600', 'QAR' => '634', 'RON' => '946', 'RSD' => '941', 'RUB' => '643', 'RWF' => '646', 'SAR' => '682', 'SBD' => '090', 'SCR' => '690', 'SDG' => '938', 'SEK' => '752', 'SGD' => '702', 'SHP' => '654', 'SLL' => '694', 'SOS' => '706', 'SRD' => '968', 'SSP' => '728', 'STN' => '930', 'SVC' => '222', 'SYP' => '760', 'SZL' => '748', 'THB' => '764', 'TJS' => '972', 'TMT' => '934', 'TND' => '788', 'TOP' => '776', 'TRY' => '949', 'TTD' => '780', 'TWD' => '901', 'TZS' => '834', 'UAH' => '980', 'UGX' => '800', 'USD' => '840', 'USN' => '997', 'UYI' => '940', 'UYU' => '858', 'UYW' => '927', 'UZS' => '860', 'VED' => '926', 'VES' => '928', 'VND' => '704', 'VUV' => '548', 'WST' => '882', 'XAF' => '950', 'XAG' => '961', 'XAU' => '959', 'XBA' => '955', 'XBB' => '956', 'XBC' => '957', 'XBD' => '958', 'XCD' => '951', 'XDR' => '960', 'XOF' => '952', 'XPD' => '964', 'XPF' => '953', 'XPT' => '962', 'XSU' => '994', 'XTS' => '963', 'XUA' => '965', 'XXX' => '999', 'YER' => '886', 'ZAR' => '710', 'ZMW' => '967', 'ZWL' => '932'];
    public const SUPPORTED_LANGUAGES = ['bg' => 'bg-BG', 'hr' => 'hr-HR', 'cs' => 'cs-CZ', 'da' => 'da-DK', 'nl' => 'nl-NL', 'en' => 'en-GB', 'fi' => 'fi-FI', 'fr' => 'fr-FR', 'de' => 'de-DE', 'el' => 'el-GR', 'hu' => 'hu-HU', 'it' => 'it-IT', 'pl' => 'pl-PL', 'pt' => 'pt-PT', 'ro' => 'ro-RO', 'es' => 'es-ES'];
    public const CHANNEL_IDS = ['prestashop' => 'A45C1059-C048-471A-95AE-8F5FF92C16F0', 'shopify' => '4C09857A-C6E7-4B32-B589-202F323C301E', 'shopware' => '7BCA8E85-0413-4FDC-861B-DD17374EED20', 'woocommerce' => 'F173274B-3EFD-4AC1-BCFA-A795D2266777'];
    // Related to webhooks and transaction retrieval
    public const TRANSACTION_TYPE_IDS = ['Card capture' => 0, 'Card pre-auth' => 1, 'Card refund 1 - including MobilePay Online' => 4, 'Card charge - including MobilePay Online' => 5, 'Card charge (installments)' => 6, 'Card void' => 7, 'Card Original Credit (refund, betting MCC only) 1' => 8, 'Viva Wallet charge' => 9, 'Viva Wallet refund' => 11, 'Card refund claimed' => 13, 'Dias' => 15, 'Cash' => 16, 'Cash refund' => 17, 'Card refund (installments) 1' => 18, 'Card payout' => 19, 'Alipay charge' => 20, 'Alipay refund' => 21, 'Card manual cash disbursement' => 22, 'iDEAL charge' => 23, 'iDEAL refund' => 24, 'P24 charge' => 25, 'P24 refund' => 26, 'BLIK charge' => 27, 'BLIK refund' => 28, 'PayU charge' => 29, 'PayU refund' => 30, 'Card withdrawal' => 31, 'Sofort refund' => 37, 'EPS charge' => 38, 'EPS refund' => 39, 'WeChat Pay charge' => 40, 'WeChat Pay refund' => 41, 'BitPay charge' => 42, 'PayPal charge' => 48, 'PayPal refund' => 49, 'Trustly charge' => 50, 'Trustly refund' => 51, 'Klarna charge' => 52, 'Klarna refund' => 53, 'MB WAY charge' => 54, 'MB WAY refund' => 55, 'Multibanco charge' => 56, 'Multibanco refund' => 57, 'Payconiq charge' => 58, 'Payconiq refund' => 59, 'IRIS charge' => 60, 'IRIS refund' => 61, 'Pay By Bank charge' => 62, 'Pay By Bank refund' => 63, 'BANCOMAT Pay charge' => 64, 'BANCOMAT Pay refund' => 65, 'tbi bank charge' => 66, 'tbi bank refund' => 67, 'Pay on Delivery charge' => 68, 'Card Verification' => 69, 'Swish charge' => 70, 'Swish refund' => 71, 'Bluecode charge' => 74, 'Bluecode refund' => 75, 'Satispay charge' => 78, 'Satispay refund' => 79, 'Klarna pre-auth' => 80, 'Klarna capture' => 81];
    // Related to payment result
    public const STATUS_ID = [
        'F' => 'SUCCESSFUL',
        'A' => 'PENDING',
        'C' => 'CAPTURED',
        'E' => 'UNSUCCESSFUL',
        'R' => 'REFUNDED',
        // FULL OR PARTIAL
        'X' => 'CANCELLED',
        // BY MERCHANT
        'M' => 'DISPUTED',
        'MA' => 'DISPUTE AWAITING',
        'MI' => 'DISPUTE IN PROGRESS',
        'ML' => 'DISPUTE LOST',
        // A disputed transaction has been refunded
        'MW' => 'DISPUTE WON',
        'MS' => 'SUSPECTED DISPUTE',
    ];
    private static $information = [];
    public static function getCurrencyCode(string $code, bool $isNumericCode)
    {
        $currencyList = $isNumericCode === \false ? self::CURRENCIES : \array_flip(self::CURRENCIES);
        return $currencyList[$code] ?? null;
    }
    public static function getInformation() : array
    {
        return self::$information;
    }
    public static function setInformation(array $information) : void
    {
        self::$information['cms']['name'] = $information['cms']['name'];
        self::$information['cms']['abbreviation'] = $information['cms']['abbreviation'];
        self::$information['cms']['version'] = $information['cms']['version'] ?? null;
        self::$information['vivaWallet']['abbreviation'] = $information['vivaWallet']['abbreviation'] ?? self::VIVAWALLET_ABBREVIATION;
        self::$information['vivaWallet']['version'] = $information['vivaWallet']['version'];
        if (isset($information['ecommercePlatform']['abbreviation'])) {
            self::$information['ecommercePlatform']['abbreviation'] = $information['ecommercePlatform']['abbreviation'];
        }
        if (isset($information['ecommercePlatform']['version'])) {
            self::$information['ecommercePlatform']['version'] = $information['ecommercePlatform']['version'];
        }
    }
    public static function getSmartCheckoutUrl(array $params, string $environment) : string
    {
        $baseUrl = self::BASE_URLS[$environment]['default'];
        $endpoint = self::ENDPOINTS['smartCheckout'];
        $filteredParams = \array_filter($params, function ($param) {
            return !empty($param);
        });
        return \implode([$baseUrl, $endpoint, '?', \http_build_query($filteredParams)]);
    }
}
