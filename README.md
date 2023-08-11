一个使用PHP代理HTTP请求的小工具

## 使用场景
由于平时需要爬取一些数据或者解析一些网站接口，但是网站对于IP请求频繁的又做了限制，因此就使用了NPS进行内网穿透我跟朋友那里的软路由/电视盒子等设备，设备上搭建PHP环境并放置了本工具，用来实现请求分流。

## 建议更改的配置

### PHP配置
在PHP配置文件php.ini文件中加入这句，以便于获取数据
```
always_populate_raw_post_data = on
```

需要将上面的配置加入到php.ini中 然后重载php配置

### nginx配置

nginx设置伪静态
```
location / {
    try_files $uri $uri/ $uri.php$is_args$args;
}
```