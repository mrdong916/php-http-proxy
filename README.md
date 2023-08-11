一个使用PHP代理HTTP请求的小工具

## 使用场景
由于平时需要爬取一些数据或者解析一些网站接口，但是网站对于IP请求频繁的又做了限制，因此就使用了NPS进行内网穿透我跟朋友那里的软路由/电视盒子等设备，设备上搭建PHP环境并放置了本工具，用来实现请求分流。

## 建议更改的配置

### PHP配置
在PHP配置文件php.ini文件中加入这句，以便于获取数据
```ini
always_populate_raw_post_data = on
```

需要将上面的配置加入到php.ini中 然后重载php配置

### nginx配置

nginx设置伪静态，可以忽略后面的`.php`
```nginx
location / {
    try_files $uri $uri/ $uri.php$is_args$args;
}
```

## 使用说明

目前仅仅对常用的GET和POST进行测试了，其中使用POST时，Content-Type 为 multipart/form-data 不能正常使用，其他正常。

测试地址：http://121.4.72.85:22882/http.php?target=https%3A%2F%2Fwww.baidu.com

在请求时只需要将真实请求地址进行UrlEncode，然后拼接上你搭建的代理服务地址，其余内容按照正常请求即可

#### 示例说明：

例如我搭建的是 http://121.4.72.85:22882/http.php 我想使用接口请求IP.SB这个网站的接口https://api.ip.sb/ip

那最终得到的地址为： http://121.4.72.85:22882/http.php?target=https%3A%2F%2Fapi.ip.sb%2Fip

如果是POST请求，同样只需要改变请求地址，其他的不变

### 返回信息说明

#### 字段说明

- target_url：实际请求地址

- request_method：请求方法

- request_headers：请求头

- request_body：请求内容

- response_status_code：返回的HTTP状态

- response_headers: 返回头

- response_body: 返回内容

#### 数据示例：

```json
{
    "success": true,
    "hid": 1,
    "target_url": "https:\/\/api.ip.sb\/ip",
    "request_method": "GET",
    "request_headers": {
        "Accept-Language": "zh,zh-CN;q=0.9",
        "Accept-Encoding": "gzip, deflate",
        "Accept": "text\/html,application\/xhtml+xml,application\/xml;q=0.9,image\/avif,image\/webp,image\/apng,*\/*;q=0.8,application\/signed-exchange;v=b3;q=0.7",
        "User-Agent": "Mozilla\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/115.0.0.0 Safari\/537.36",
        "Upgrade-Insecure-Requests": "1",
        "Cache-Control": "no-cache",
        "Pragma": "no-cache",
        "Connection": "keep-alive",
        "X-Forwarded-For": "64.136.232.142"
    },
    "request_body": "",
    "response_status_code": 200,
    "response_headers": {
        "Date": [
            "Fri, 11 Aug 2023 07:45:10 GMT"
        ],
        "Content-Type": [
            "text\/plain"
        ],
        "Transfer-Encoding": [
            "chunked"
        ],
        "Connection": [
            "keep-alive"
        ],
        "vary": [
            "Accept-Encoding"
        ],
        "Cache-Control": [
            "no-cache"
        ],
        "CF-Cache-Status": [
            "DYNAMIC"
        ],
        "Report-To": [
            "{\"endpoints\":[{\"url\":\"https:\\\/\\\/a.nel.cloudflare.com\\\/report\\\/v3?s=pBFR4Nf%2BgPEzV92sSn7XPagCMe89e26SRadFlifdjSkVHlQH0RSZo0V689Elm8YxSB0E8P3w8CYLcLksC4nWif2SlmI1FC8Y7gZcrf3%2FDX0N1EUNV6Imh6ax1g%3D%3D\"}],\"group\":\"cf-nel\",\"max_age\":604800}"
        ],
        "NEL": [
            "{\"success_fraction\":0,\"report_to\":\"cf-nel\",\"max_age\":604800}"
        ],
        "Strict-Transport-Security": [
            "max-age=31536000; includeSubDomains; preload"
        ],
        "Server": [
            "cloudflare"
        ],
        "CF-RAY": [
            "7f4edb87de8a0452-HKG"
        ],
        "alt-svc": [
            "h3=\":443\"; ma=86400"
        ],
        "x-encoded-content-encoding": [
            "gzip"
        ]
    },
    "response_body": "222.137.85.148\n"
}

```

