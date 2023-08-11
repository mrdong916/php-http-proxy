<?php
/**
 * 转发HTTP请求
 * 
 */
 
require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

header('Access-Control-Allow-Origin:*');

// 获取目标URL
$targetUrl = $_GET['target'];
$hid = 1;
// 对目标URL进行非空判断
if (empty($targetUrl)) {
    http_response_code(200);
    echo json_encode([
        'success' => false, 
        'hid' => $hid,
        'msg' => 'param target not found',
    ]);
    exit;
}

// 获取目标URL
$targetUrl = urldecode($targetUrl);

// 解析目标 URL 获取 Host
$parsedUrl = parse_url($targetUrl);
$targetHost = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';

// 获取收到的请求方法
$requestMethod = $_SERVER['REQUEST_METHOD'];

// 获取收到的请求头
$requestHeaders = [];

foreach (getallheaders() as $name => $value) {
    // 排除一些不需要转发的请求头，如Host、Connection等
    if ($name !== "Host"  && $name !== "Content-Length" && !empty($value)) {
         $requestHeaders[$name] = $value;
    }
}

// 加入随机IP
$requestHeaders['X-Forwarded-For'] = rand_IP();

// 创建 Guzzle HTTP Client 实例
$client = new Client();

try {
    // 处理POST请求内容
    $requestBody = null;
    $requestForm = null;
    $response = null;
    
    // 获取收到的请求体
    $requestBody = file_get_contents('php://input');
    
    if('POST' == $requestMethod && empty($requestBody)){
        // 如果默认获取POST请求内容为空则更换为Form的方式获取并请求
        $requestForm = createMultipartFormData($_POST);
        // 创建并发送请求
        $response = $client->request($requestMethod, $targetUrl, [
            'headers' => $requestHeaders,
            'multipart' => $requestForm,
            'allow_redirects' => false
        ]);
    }else{
        // 创建并发送请求
        $response = $client->request($requestMethod, $targetUrl, [
            'headers' => $requestHeaders,
            'body' => $requestBody,
            'allow_redirects' => false
        ]);
    }
    
    // 设置响应状态码
    http_response_code(200);

      // 构造请求详情信息
    $requestInfo = [
        'success' => true,
        'hid' => $hid,
        'target_url' => $targetUrl,
        'request_method' => $requestMethod,
        'request_headers' => $requestHeaders,
        'request_body' => $requestBody,
        'response_status_code' => $response->getStatusCode(),
        'response_headers' => $response->getHeaders(),
        'response_body' => $response->getBody()->getContents()
    ];

    // 输出请求详情信息
    header('Content-Type: application/json');
    echo json_encode($requestInfo, JSON_PRETTY_PRINT);
    // 输出响应体
    // echo $response->getBody();
} catch (RequestException $e) {
    // 处理请求异常
    if ($e->hasResponse()) {
        $response = $e->getResponse();
        $statusCode = $response->getStatusCode();

        // 输出异常响应状态码和响应体
        http_response_code($statusCode);
        echo $response->getBody();
    } else {
        // 输出异常消息
        echo "Request Exception: " . $e->getMessage();
    }
}

function rand_IP(){
    $ip2id = round(rand(600000, 2550000) / 10000);
    $ip3id = round(rand(600000, 2550000) / 10000);
    $ip4id = round(rand(600000, 2550000) / 10000);
    $arr_1 = array("218","218","66","66","218","218","60","60","202","204","66","66","66","59","61","60","222","221","66","59","60","60","66","218","218","62","63","64","66","66","122","211");
    $randarr= mt_rand(0,count($arr_1)-1);
    $ip1id = $arr_1[$randarr];
    return $ip1id.".".$ip2id.".".$ip3id.".".$ip4id;
}

// 函数：创建 Multipart Form Data
function createMultipartFormData($formData){
    $multipart = [];

    foreach ($formData as $name => $value) {
        $multipart[] = [
            'name' => (string)$name,
            'contents' =>  (string)$value
        ];
    }

    return $multipart;
}
?>