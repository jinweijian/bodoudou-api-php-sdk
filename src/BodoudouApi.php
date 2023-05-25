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
            $this->options['endpoint'] = "https://www.bodoudou.com";
        }
    }

    public function enableAccount(): void
    {
        $this->request('POST', '/api-open/account/enable');
    }

    public function disableAccount(): void
    {
        $this->request('POST', '/api-open/account/disable');
    }

    /**
     * @return array status: none, ok, disabled
     */
    public function inspectAccount(): array
    {
        return $this->request('GET', '/api-open/account/inspect');
    }

    public function createPaper($params): array
    {
        return $this->request('POST', '/api-open/paper/create', ['json' => $params]);
    }

    public function createRoom($params): array
    {
        return $this->request('POST', '/api-open/room/create', ['json' => $params]);
    }

    public function getExam(string $id): array
    {
        return $this->request('GET', '/api-open/exam/get', [
            'query' => [
                'id' => $id,
            ]
        ]);
    }

    public function getExamUserOverviewReport(string $id): array
    {
        return $this->request('GET', '/api-open/exam/getExamUserOverviewReport', [
            'query' => [
                'id' => $id,
            ]
        ]);
    }

    public function validateWebhookToken(string $token): void
    {
        try {
            if (class_exists('\Firebase\JWT\Key')) {
                $decoded = JWT::decode($token, new \Firebase\JWT\Key($this->secretKey, 'HS256'));

            } else {
                // 兼容 Firebase/JWT 4.x 版本
                $decoded = JWT::decode($token, $this->secretKey, array('HS256'));
            }

            if (empty($decoded->iss) || $decoded->iss !== 'bodoudou webhook') {
                throw new SDKException("Webhook iss invalid", "", "");
            }

        } catch (\Exception $e) {
            throw new SDKException("Webhook token invalid: {$e->getMessage()}", "", "");
        }
    }

    public function createItemPreviewId(array $params): string
    {
        $preview = $this->request('POST', '/api-open/paper/createItemPreviewId', ['json' => $params]);
        return $preview['id'];
    }

    public function makeItemPreviewUrl(string $previewId): string {
        $payload = [
            'iss' => 'bodoudou sdk item preview api',
            'exp' => time() + 600,
            'id' => $previewId,
        ];

        $token = JWT::encode($payload, $this->secretKey, 'HS256', $this->accessKey);

        return "{$this->options['endpoint'] }/sdk/item/preview?token={$token}";
    }

    public function makeJoinUrl($roomId, $role, $user): string
    {
        $payload = [
            'iss' => 'bodoudou sdk room join api',
            'exp' => time() + 600,
            'oid' => $roomId,
            'role' => $role,
            'uid' => (string) $user['id'],
            'name' => $user['name'],
            'avatar' => empty($user['avatar']) ? '' : $user['avatar'],
        ];

        $token = JWT::encode($payload, $this->secretKey, 'HS256', $this->accessKey);

        return "{$this->options['endpoint'] }/sdk/room/join?token={$token}";
    }

    public function makeViewExamReportUrl($examId): string {
        $payload = [
            'iss' => 'bodoudou sdk exam api',
            'exp' => time() + 86400,
            'id' => $examId,
        ];

        $token = JWT::encode($payload, $this->secretKey, 'HS256', $this->accessKey);

        return "{$this->options['endpoint'] }/sdk/exam/showReport?token={$token}";
    }

    public function makeDownloadExamReportUrl($examId): string {
        $payload = [
            'iss' => 'bodoudou sdk exam api',
            'exp' => time() + 86400,
            'id' => $examId,
        ];

        $token = JWT::encode($payload, $this->secretKey, 'HS256', $this->accessKey);

        return "{$this->options['endpoint'] }/sdk/exam/downloadReport?token={$token}";
    }


    private function request(string $method, string $uri, array $options = []): array
    {
        if (!$this->client) {
            $this->client = HttpClient::create([
                'http_version' => '1.1',
                'base_uri' => $this->options['endpoint'],
                'timeout' => 15,
            ]);
        }

        $token = JWT::encode([
            'iss' => 'bodoudou open api',
            'exp' => time() + 300,
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