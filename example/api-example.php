<?php

use Bodoudou\SDK\BodoudouApi;
use Bodoudou\SDK\SDKException;

require dirname(__DIR__). '/vendor/autoload.php';

$accessKey = "test_access_key";
$secretKey = "test_secret_key";
$options = [
    'endpoint' => 'https://127.0.0.1:8080/', // 测试环境接口地址， 生产环境不需要传此参数
];

$api = new BodoudouApi($accessKey, $secretKey, $options);

try {
    $result = $api->enableAccount();
    var_dump($result);
} catch (SDKException $e) {
    echo "\nError Code: {$e->getErrorCode()}";
    echo "\nError Message: {$e->getMessage()}";
    echo "\nTrace Id: {$e->getTraceId()}";
}
