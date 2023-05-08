<?php

use Bodoudou\SDK\BodoudouApi;
use Bodoudou\SDK\SDKException;

require dirname(__DIR__). '/vendor/autoload.php';

$accessKey = "test_access_key";
$secretKey = "test_secret_key";
$options = [
//    'endpoint' => 'https://127.0.0.1:8080/', // 测试环境接口地址， 生产环境不需要传此参数
    'endpoint' => 'https://bdd-test.edusoho.cn', // 测试环境接口地址， 生产环境不需要传此参数
];

$api = new BodoudouApi($accessKey, $secretKey, $options);

//inspectAccountExample($api);
enableAccountExample($api);
//disableAccountExample($api);
//inspectAccountExample($api);


gameJoinExample($api);

/**
 * 启用账号示例
 * @param BodoudouApi $api
 * @return void
 */
function enableAccountExample(BodoudouApi $api) {
    try {
        $result = $api->enableAccount();
        var_dump("enableAccount", $result);
    } catch (SDKException $e) {
        echo "\nError Code: {$e->getErrorCode()}";
        echo "\nError Message: {$e->getMessage()}";
        echo "\nTrace Id: {$e->getTraceId()}";
    }
}

/**
 * 禁用账号示例
 * @param BodoudouApi $api
 * @return void
 */
function disableAccountExample(BodoudouApi $api) {
    try {
        $result = $api->disableAccount();
        var_dump("disableAccount", $result);
    } catch (SDKException $e) {
        echo "\nError Code: {$e->getErrorCode()}";
        echo "\nError Message: {$e->getMessage()}";
        echo "\nTrace Id: {$e->getTraceId()}";
    }
}

/**
 * 查看账号状态示例
 * @param BodoudouApi $api
 * @return void
 */
function inspectAccountExample(BodoudouApi $api) {
    try {
        $result = $api->inspectAccount();
        var_dump("inspectAccount", $result);
    } catch (SDKException $e) {
        echo "\nError Code: {$e->getErrorCode()}";
        echo "\nError Message: {$e->getMessage()}";
        echo "\nTrace Id: {$e->getTraceId()}";
    }
}

/**
 * 游戏创建加入过程示例
 * @param BodoudouApi $api
 * @return void
 */
function gameJoinExample(BodoudouApi $api) {
    try {
        $params = json_decode(file_get_contents(__DIR__.'/paper-example.json'), true);
        $paper = $api->createPaper($params);

        echo "========== paper :\n";
        var_dump($paper);

        $room = $api->createRoom([
            'paperId' => $paper['id'],
            'joinUrl' => 'https://www.baidu.com/join',
            'webhookUrl' => 'https://www.baidu.com/webook',
        ]);

        echo "========== room :\n";
        var_dump($room);

        $teacherUser = ['id' => '1', 'name' => '测试老师'];
        $roomTeacherJoinUrl = $api->makeJoinUrl($room['id'], 'teacher', $teacherUser);

        echo "========== teacher join url :\n";
        var_dump($roomTeacherJoinUrl);


        $studentUser = ['id' => '2', 'name' => '测试学生'];
        $roomStudentJoinUrl = $api->makeJoinUrl($room['id'], 'student', $studentUser);

        echo "========== student join url :\n";
        var_dump($roomStudentJoinUrl);

    } catch (SDKException $e) {
        echo "\nError Code: {$e->getErrorCode()}";
        echo "\nError Message: {$e->getMessage()}";
        echo "\nTrace Id: {$e->getTraceId()}";
    }
}