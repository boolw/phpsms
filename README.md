# Laravel Sms

一个基于`Laravel`框架的功能强大的手机号合法性验证解决方案。

### 1. 关于phpsms
`phpsms` 是基于[toplan/laravel-sms](https://github.com/toplan/laravel-sms)开发的适用于`Laravel`框架的手机号验证解决方案。
`phpsms`主要是在[toplan/laravel-sms](https://github.com/toplan/laravel-sms)基础上升级了阿里大于协议，安装步骤参考[toplan/laravel-sms](https://github.com/toplan/laravel-sms)

安装完成[toplan/laravel-sms](https://github.com/toplan/laravel-sms)后
# 安装

在项目根目录下运行如下composer命令:
```php
//推荐
composer require boolw/phpsms:~1.0

//安装开发中版本
composer require boolw/phpsms:dev-master

在config/phpsms.php中设置
```php
'scheme' => [
    'AlidayuSms'
];

'agents' => [
    /*
     * -----------------------------------
     * AlidayuSms
     * 阿里大鱼短信
     * -----------------------------------
     * website:http://www.alidayu.com
     * support template sms.
     */
    'AlidayuSms' => [
        //请求地址
        'sendUrl'           => 'http://dysmsapi.aliyuncs.com/',
        //淘宝开放平台中，对应阿里大鱼短信应用的App Key
        'appKey'            => 'you appkey',
        //淘宝开放平台中，对应阿里大鱼短信应用的App Secret
        'secretKey'         => 'you secretKey',
        //短信签名，传入的短信签名必须是在阿里大鱼“管理中心-短信签名管理”中的可用签名
        'smsFreeSignName'   => '阿里云短信测试专用',
        //被叫号显(用于语音通知)，传入的显示号码必须是阿里大鱼“管理中心-号码管理”中申请或购买的号码
        'calledShowNum'     => null,
        'agentClass'=>"Boolw\\PhpSms\\AlidayuSmsAgent",//实现类
    ],
]
```

# License

MIT
