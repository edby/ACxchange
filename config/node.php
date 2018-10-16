<?php

/**
 *
 * Created by PhpStorm.
 * User: ZRothschild
 * Date: 2018/1/25
 * Time: 9:36
 * WHM
 * 链接： https://139.5.203.106:2087
 *用户名： root
 *密码： *t5W!t^GXJ7dy
 *数据库
 *用户名 ： zhaoqiaowang   acxechange
 *数据库密码   my06lucky12dog.
 *Cpanel
 *链接： https://139.5.203.106:2083/
 *用户名： acxchange
 *密码： alliance888
 *禅道
 *链接：http://43.225.198.82:81/zentao/
 *用户名: zhaoqiaowang
 *密码: Mm123456
 *
 * 短信地址   http://con.monyun.cn:9960/frame/#console/console.html
 * 短信用户名   acxchange
 * 短信密码   vdTQiUI2izY9zzX2
 *
 *ac正式服ip 103.117.145.42  网址  https://www.acxchange.io/
 * 用户 root 密码  ihGPEAOt4e
 *
 * 路径 /home/acxchange/public_html/exchange
 *
 * 后台  bd.acxchange.io
 * 用户 zhao 密码123445678
 *
 * 数据库
 *用户名 ： zhaoqiaowang   acxechange
 *密码   my06lucky12dog.
 *
 *
 *ac测试式服ip 148.66.58.218  网址  https://testacx.acxchange.io/
 * 用户 root 密码  n4RMw543BM
 *
 * 路径  /home/acxchange/public_html/testexchange
 *
 * 后台  https://testbd.acxchange.io/
 * 用户 zhao 密码123445678
 *
 *数据库
 *用户名 ：staging_nova
 *密码： 1230.1230.xf
 *
 * 短信平台
 * http://www.fastoo.cn/recordSend
 * 账号:17603012101
 * 密码:ROnEmRevathKau1
 *
 */

return [
    /*
    |---------------------------
    | AC 交易平台服务器
    |---------------------------
    |
    */
//    'ac' => [
//        'domain' => '139.5.202.26',
//        'user' =>  'root',
//        'password' => 'kwmBc%zkvhZa3ct8',
//    ],

    /*
    |---------------------------
    | AC 交易平台服务器
    |---------------------------
    |
    */
    'ac' => [
        'domain' => '139.5.203.106',
        'user' =>  'root',
        'password' => '*t5W!t^GXJ7dy',
    ],

    /*
    |---------------------------
    | BTC 节点
    |---------------------------
    |
    */
    'btc' => [
        'domain' => '110.173.59.106',
        'user' =>  'bitcoinrpc',
        'password' => 'B!tco!n',
        'port' => 8332,
        'url' => "http://bitcoinrpc:B!tco!n@110.173.59.106:8332",
    ],

    /*
    |---------------------------
    | ZEC 节点
    |---------------------------
    |
    */
    'zec' => [
        'domain' => '110.173.59.106',
        'user' =>  'zcashuser',
        'password' => 'zcash000',
        'port' => 8888,
        'url' => "http://zcashuser:zcash000@110.173.59.106:8888",
    ],

    /*
    |---------------------------
    | pivx 节点
    |---------------------------
    |
    */
    'pivx' => [
        'domain' => '103.71.252.10',
        'user' =>  'p!vxus3r',
        'password' => 'q!vxco!n',
        'port' => 1688,
        'url' => "http://p!vxus3r:q!vxco!n@103.71.252.10:1688",
    ],

    /*
    |---------------------------
    | ltc 节点
    |---------------------------
    |
    */
    'ltc' => [
        'domain' => '103.71.252.10',
        'user' =>  'litecoinrpc',
        'password' => 'l!teco!n',
        'port' => 8331,
        'url' => "http://litecoinrpc:l!teco!n@103.71.252.10:8331",
    ],

    /*
    |---------------------------
    | dash 节点
    |---------------------------
    |
    */
    'dash' => [
        'domain' => '137.59.22.122',
        'user' =>  'd@shus3r',
        'password' => 'd@shco!n',
        'port' => 8331,
        'url' => "http://d@shus3r:d@shco!n@137.59.22.122:1888",
    ],

    /*
    |---------------------------
    | bch 节点
    |---------------------------
    |
    */
    'bch' => [
        'domain' => '137.59.22.122',
        'user' =>  'bitcoincashrpc',
        'password' => 'B!tco!ncash',
        'port' => 8334,
        'url' => "http://bitcoincashrpc:B!tco!ncash@137.59.22.122:8334",
    ],

    /*
    |---------------------------
    | rpz 节点
    |---------------------------
    |
    */
    'rpz' => [
        'domain' => '148.66.60.170',
        'user' =>  'rrpcuser00',
        'password' => 'Nathan000',
        'port' => 21435,
        'url' => "http://rrpcuser00:Nathan000@148.66.60.170:21435",
    ],



    /*
    |---------------------------
    | rpz 节点
    |---------------------------
    |
    */
    'xvg' => [
        'domain' => '148.66.60.170',
        'user' =>  'V3RgeU53R',
        'password' => 'V3RgeCo!n',
        'port' => 20102,
        'url' => "http://V3RgeU53R:V3RgeCo!n@148.66.60.170:20102",
    ],

//    Verge (XVG) 节点
];