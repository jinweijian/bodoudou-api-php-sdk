<?php

namespace Bodoudou\SDK;

use Firebase\JWT\JWT;
use Symfony\Component\HttpClient\HttpClient;
use Throwable;

class BodoudouApi
{
    private $accessKey;

    private $secretKey;

    private $options;

    private $client;

    public function __construct(string $accessKey, string $secretKey, array $options = [])
    {
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
        $this->options = $options;

        if (empty($this->options['endpoint'])) {
            $this->options['endpoint'] = "https://www.bodoudou.com/";
        }
    }

    public function enableAccount(): array {
        return $this->request('POST', '/api-open/account/enable');
    }

    public function disableAccount(): array {
        return $this->request('POST', '/api-open/account/disable');
    }

    public function getAccount(): array {
        return $this->request('GET', '/api-open/account/get');
    }

    private function request(string $method, string $uri, array $options = []): array {
        if (!$this->client) {
            $this->client = HttpClient::create([
                'http_version' => '1.1',
                'base_uri' => $this->options['endpoint'],
                'timeout' => 15,
            ]);
        }

        $token = JWT::encode([
            'iss' => 'bodoudou open api',
            'exp' => time() + 600,
        ], $this->secretKey, 'HS256', $this->accessKey);

        if (!isset($options['headers'])) {
            $options['headers'] = [];
        }
        $options['headers'][] = 'Authorization: Bearer ' . $token;

        try {
            $response =  $this->client->request($method, $uri, $options);
            $status = $response->getStatusCode();
            $content = $response->toArray(false);
        } catch (Throwable $e) {
            throw new SDKException($e->getMessage(), 'HTTP_ERROR', '');
        }

        if ($status >= 300 || $status < 200) {
            throw new SDKException($content['message'] ?? '', $content['code'] ?? 'UNKNOWN', $content['traceId'] ?? '');
        }

        return $content;
    }
}