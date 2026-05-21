<?php

namespace VivaComSmartCheckout\Vivawallet\VivawalletPhp\Utils;

use VivaComSmartCheckout\Vivawallet\VivawalletPhp\Application;
use VivaComSmartCheckout\Vivawallet\VivawalletPhp\Http\Authentication\Authentication;
class Utils
{
    private const CORRELATION_ID_HEADER = 'X-Viva-CorrelationId';
    public static function getCustomUserAgent() : string
    {
        $elements = self::getUserAgentElements();
        \array_walk($elements, function (&$value, $key) {
            $value = "{$key}/{$value}";
        });
        $userAgent = !empty($_SERVER['HTTP_USER_AGENT']) ? \htmlspecialchars(\strip_tags($_SERVER['HTTP_USER_AGENT']), \ENT_QUOTES) : '';
        return $userAgent . ' ' . \implode(' ', $elements);
    }
    private static function getUserAgentElements() : array
    {
        $applicationInfo = Application::getInformation();
        $abbreviationKey = $applicationInfo['vivaWallet']['abbreviation'] . $applicationInfo['cms']['abbreviation'];
        $userAgentElements = [($applicationInfo['vivaWallet']['abbreviation'] ?? '') . 'SDK' => Application::SDK_VERSION, $applicationInfo['cms']['abbreviation'] => $applicationInfo['cms']['version'], $abbreviationKey => $applicationInfo['vivaWallet']['version']];
        if (!empty($applicationInfo['ecommercePlatform']['abbreviation'])) {
            $abbreviationKey = $applicationInfo['ecommercePlatform']['abbreviation'];
            $userAgentElements[$abbreviationKey] = $applicationInfo['ecommercePlatform']['version'];
        }
        $userAgentElements['IP'] = self::getIpAddress();
        return $userAgentElements;
    }
    public static function getIpAddress() : string
    {
        foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'] as $key) {
            if (\array_key_exists($key, $_SERVER) === \true) {
                $address = \htmlspecialchars(\strip_tags($_SERVER[$key]), \ENT_QUOTES);
                $ips = \explode(',', $address);
                foreach ($ips as $ip) {
                    $ip = \trim($ip);
                    if (\filter_var($ip, \FILTER_VALIDATE_IP, \FILTER_FLAG_NO_PRIV_RANGE | \FILTER_FLAG_NO_RES_RANGE) !== \false) {
                        return $ip;
                    }
                }
            }
        }
        return '';
    }
    public static function isHttpStatusCodeValid($status) : bool
    {
        return $status >= 200 && $status < 300;
    }
    public static function getCorrelationIdFromHeaders(array $headers) : ?string
    {
        if (empty($headers)) {
            return null;
        }
        $normalized = [];
        foreach ($headers as $k => $v) {
            $normalized[\strtolower($k)] = $v;
        }
        $key = \strtolower(self::CORRELATION_ID_HEADER);
        if (!\array_key_exists($key, $normalized)) {
            return null;
        }
        $val = $normalized[$key];
        return \is_array($val) ? $val[0] ?? null : $val;
    }
    public static function buildHttpHeaders(array $options, string $authHeader) : array
    {
        $headers = ['Accept' => 'application/json', 'User-Agent' => self::getCustomUserAgent(), 'Authorization' => $authHeader];
        return !empty($options['headers']) ? self::addHeaders($headers, $options) : $headers;
    }
    public static function addHeaders(array $headers, array $options) : array
    {
        // Currently we only allow correlation id if present
        if (!empty($options['headers']['correlationId'])) {
            // Insert if not already set
            if (empty($headers[self::CORRELATION_ID_HEADER])) {
                $headers[self::CORRELATION_ID_HEADER] = (string) $options['headers']['correlationId'];
            }
        }
        return $headers;
    }
    public static function buildRequestUrl(string $base, string $endpoint, string $query, ?Authentication $authentication) : string
    {
        $baseUrl = Application::BASE_URLS[$authentication->getEnvironment()][$base];
        $path = Application::ENDPOINTS[$endpoint] . $query;
        return $baseUrl . $path;
    }
    public static function newCorrelationId() : string
    {
        $date = new \DateTime('now', new \DateTimeZone('UTC'));
        $year = $date->format('y');
        $dayOfYearFormatted = \sprintf('%03d', (int) $date->format('z') + 1);
        $hash = \strtoupper(\dechex(\crc32(\uniqid('', \true))));
        return "{$year}-{$dayOfYearFormatted}-{$hash}";
    }
}
