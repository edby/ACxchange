# 开放接口文档

## 1.接口规范与约定

### 1.请求接口域名统一为 https://exchange.alliancecapitals.com/

### 2.接口返回统一格式

>##### 整体返回格式示例

```php
{
    "status": "1",
    "message": "成功",
    "data": []
}
```
>##### 返回参数

| 参数名称  | 值类型   |  备注说明  |
| :--------: | :-----:  | :----:|
| status    | string   |  请求状态[1:表示成功] [0:表示失败] |
| message   |   string |   成功提示或者失败信息 |
| data    |  array string |  返回数组结果集 可能为空|

## 2.接口列表

> ##### 1.各种货币汇率，以及对应参考 btc usd cny 汇率
> * 请求方法 POST
> * 请求路径 /wallet/market

>##### 请求参数 
> * 当不传参数时候 currency 默认为 btc


| 参数名称 | 是否必填  |值类型| 备注说明 |
| :-------: | :-----:| :----:|:----: |
| currency | 否 |  string   | 参考货币三种 btc usd cny|

>##### 返回参数 data
> * data 是一个三维数组

| 参数名称  | 值类型   |  备注说明  |
| :--------: | :-----:  | :----:|
| percent_change_24h  | string   |  24小时变化 百分比 |
| 24h_volume_btc   |   float |   浮点型数字 24小时交易总量 |
| curr_abb    |  string |  虚拟货币名称|
| price_'.$currency |  string | 请求参考货币对应虚拟货币当前价格|
| flag |  int | 升降 0 降 1 升 |

>##### 返回示例

```php
{
    "status": "0",
    "message": "失败",
    "data": [
       [
          "percent_change_24h":"1%",
          "total_supply":"1.23", 
          "curr_abb":"btc", 
          "price_btc":"0.0001",  
       ],
    ]
}
```
> ##### 2.买卖流程流程 
> * 请求方法 POST
> * 请求路径 /wallet/currencytrade

>##### 请求参数 
> * 传参 amount，price，total，netTotal，fee，feeRate，type，currency，tradeCurr

| 参数名称 | 是否必填  |值类型| 备注说明 |
| :-------: | :-----:| :----:|:----: |
| amount | 是 |  string   | 货币数量|
| price | 是 |  string   | 货币价格 |
| total | 是 |  string   | 货币总量 |
| netTotal | 是 |  string   | 实际货币量 |
| feeRate | 是 |  float   | 手续费费率 |
| fee | 是 |  string   | 手续费 |
| type | 是 |  int   | 交易类型 （10 buy  20 sell） |
| currency | 是 |  string   | 参考货币 （btc usd cny） |
| tradeCurr | 是 |  string   | 交易货币名称 |

>##### 返回参数 data
> * data 


>##### 返回示例

```php
{
    "status": "0",
    "message": "失败",
    "data": [
    ]
}
```
> ##### 3.默认请求 用户最多能该买
> * 请求方法 POST
> * 请求路径 /wallet/tradeTemp

>##### 请求参数 
> * 传参 type，currency，tradeCurr

| 参数名称 | 是否必填  |值类型| 备注说明 |
| :-------: | :-----:| :----:|:----: |
| type | 是 |  int   | 交易类型 （10 buy  20 sell） |
| currency | 是 |  string  | 参考货币 （btc usd cny） |
| tradeCurr | 是 |  string | 交易货币名称 |

>##### 返回参数 data
> * data 二维数组

| 参数名称  | 值类型   |  备注说明  |
| :--------: | :-----:  | :----:|
| amount  | string   |  最多能消费数量 |
| price |  string |   交易价格 |
| total |  string |  交易货币|
| feeRate |  string | 费率|
| fee |  string | 手续费|
| netTotal | string | 得到的货币数量|
| currName | string | tradeCurr 的全称|

> ##### 4.自己订单 包括买卖  my open orders 
> * 请求方法 GET
> * 请求路径 /trade/myOpenOrder

>##### 请求参数 
> * 传参  page 

| 参数名称 | 是否必填  |值类型| 备注说明 |
| :-------: | :-----:| :----:|:----: |
| page | 否 |  int   | 当前页数 |
| tradeCurr | 是 |  string   | 交易的货币 |


>##### 返回参数 data
> * data 二维数组

| 参数名称  | 值类型   |  备注说明  |
| :--------: | :-----:  | :----:|
| current_page  | int   |  当前页 |
| data |  array |   交易数据 |
| first_page_url |  string |  第一页链接|
| from |  int | 结束条数 |
| last_page |  int | 最后一页|
| last_page_url |  string | 最后一页链接|
| next_page_url |  string | 下一页链接|
| path |  string | 请求地址|
| per_page |  int | 每页显示条数|
| prev_page_url |  null | |
| to |  int | 开始条数|
| total |  int | 总条数|


> * data 二维数组

| 参数名称  | 值类型   |  备注说明  |
| :--------: | :-----:  | :----:|
| net_volume  | string   |  交易数量 |
| price_btc |  string |   交易价格 |
| volume_btc |  string |  初始量|
| residual_num |  string | 剩余量|
| trade_type |  int | 交易类型 【10 买 20 卖】|
| operation |  int | 操作的类型 【10 交易中 20 取消交易】|
| add_time |  string | 发起时间|

>##### 返回示例

```php
{
    "status": "0",
    "message": "失败",
    "data":[
          "current_page":1,
           "data":[
             0=> [
                 'add_time' =>1234566875,
                 'net_volume' =>'1.00000000',
                 'volume_btc' =>'1.00000000',
                 'residual_num' =>'0.00010000',
                 'trade_type' =>10,
                 'price_btc' =>'0.00000002',
                 'operation' =>10,
             ],
          ]       
          "first_page_url": "https://exchange.alliancecapitals.com/trade/myOpenOrder?page=1",
          "from": 1,
          "last_page": 2,
          "last_page_url": "https://exchange.alliancecapitals.com/trade/myOpenOrder?page=2",
          "next_page_url": "https://exchange.alliancecapitals.com/trade/myOpenOrder?page=2",
          "path": "https://exchange.alliancecapitals.com/trade/myOpenOrder",
          "per_page": 1,
          "prev_page_url": null,
          "to": 1,
          "total": 2,
    ],

}
```

> ##### 5.卖订单  Sell orders  和 Buy orders
> * 请求方法 GET
> * 请求路径 /trade/tradeOrder

>##### 请求参数 
> * 传参  page  curr_abb  tradeType

| 参数名称 | 是否必填  |值类型| 备注说明 |
| :-------: | :-----:| :----:|:----: |
| page | 否 |  int   | 当前页数 |
| curr_abb | 否 |  string   | 货币名称 |
| tradeType /否 |  int   | 交易类型 【10 买 20 卖】 |

>##### 返回参数 data
> * data 二维数组

| 参数名称  | 值类型   |  备注说明  |
| :--------: | :-----:  | :----:|
| current_page  | int   |  当前页 |
| data |  array |   交易数据 |
| first_page_url |  string |  第一页链接|
| from |  int | 结束条数 |
| last_page |  int | 最后一页|
| last_page_url |  string | 最后一页链接|
| next_page_url |  string | 下一页链接|
| path |  string | 请求地址|
| per_page |  int | 每页显示条数|
| prev_page_url |  null | |
| to |  int | 开始条数|
| total |  int | 总条数|
| currAbb |  string | 货币名称|
| totalNum |  string | 交易总数 比特币 计算|


> * data 二维数组

| 参数名称  | 值类型   |  备注说明  |
| :--------: | :-----:  | :----:|
| price_btc  | string   |  交易价格 |
| residual_num | string |   交易数量 |
| value |  string |  初始量|


>##### 返回示例

```php
{
    "status": "0",
    "message": "失败",
    "data":[
          "current_page":1,
           "data":[
             0=> [
                 'price_btc' =>'0.00000002',
                  'residual_num' =>'1.00000000',
                  'value' =>'1.00000000',
             ],
          ]       
          "first_page_url": "https://exchange.alliancecapitals.com/trade/myOpenOrder?page=1",
          "from": 1,
          "last_page": 2,
          "last_page_url": "https://exchange.alliancecapitals.com/trade/myOpenOrder?page=2",
          "next_page_url": "https://exchange.alliancecapitals.com/trade/myOpenOrder?page=2",
          "path": "https://exchange.alliancecapitals.com/trade/myOpenOrder",
          "per_page": 1,
          "prev_page_url": null,
          "to": 1,
          "total": 2,
          "currAbb":"btc",
    ],

}
```

> ##### 6.市场订单历史  Market history
> * 请求方法 GET
> * 请求路径 /trade/marketHistory

>##### 请求参数 
> * 传参  page  curr_abb

| 参数名称 | 是否必填  |值类型| 备注说明 |
| :-------: | :-----:| :----:|:----: |
| page | 否 |  int   | 当前页数 |
| curr_abb | 否 |  string   | 货币名称 |

>##### 返回参数 data
> * data 二维数组

| 参数名称  | 值类型   |  备注说明  |
| :--------: | :-----:  | :----:|
| current_page  | int   |  当前页 |
| data |  array |   交易数据 |
| first_page_url |  string |  第一页链接|
| from |  int | 结束条数 |
| last_page |  int | 最后一页|
| last_page_url |  string | 最后一页链接|
| next_page_url |  string | 下一页链接|
| path |  string | 请求地址|
| per_page |  int | 每页显示条数|
| prev_page_url |  null | |
| to |  int | 开始条数|
| total |  int | 总条数|
| currAbb |  string | 货币名称|


> * data 二维数组

| 参数名称  | 值类型   |  备注说明  |
| :--------: | :-----:  | :----:|
| add_time  | string |  交易时间 为时间戳 |
| trade_type | int | 交易类型 【10 买 20 卖】 |
| price_btc |  string |  价格|
| initial_mun |  string |  消费量 |
| value |  string | 总价格|


> ##### 7.用户各种币的资产  balance 
> * 请求方法 POST
> * 请求路径 /wallet/assets

>##### 请求参数 
> * 传参  无


>##### 返回参数 data
> * data 二维数组

| 参数名称  | 值类型   |  备注说明  |
| :--------: | :-----:  | :----:|
| curr_abb  | string |  货币名称 |
| in_trade |  string |  在交易的金额|
| balance |  string |  消费量 |
| curr_img |  string |  货币图片路径 |


```php
{
    "status": "1",
    "message": "successful",
    "data": [
        ['curr_abb':'btc','in_trade':'0.000001','balance':'0.0001','curr_img':'public/images/btc.png']
    ]
}
```

> ##### 8.获取用户开通的货币列表
> * 请求方法 POST
> * 请求路径 /trade/currList

>##### 请求参数 
> * 传参  无


>##### 返回参数 data
> * data 二维数组

| 参数名称  | 值类型   |  备注说明  |
| :--------: | :-----:  | :----:|
| curr_abb  | string |  货币名称 |


```php
{
    "status": "1",
    "message": "successful",
    "data": [
        ['curr_abb':'btc'],
        ['curr_abb':'zec'],
    ]
}
```

> ##### 9.获取用户开通的货币列表
> * 请求方法  POST
> * 请求路径 /trade/deposit

>##### 请求参数 
> * 传参  currAbb 货币名称

| 参数名称  | 值类型   |  备注说明  |
| :--------: | :-----:  | :----:|
| currAbb  | string |  货币名称 |


>##### 返回参数 data
> * data 二维数组

| 参数名称  | 值类型   |  备注说明  |
| :--------: | :-----:  | :----:|
| balance  | string |  货币金额 |
| address  | string |  货币地址 |
| qcode  | string |  货币二维码 |
| currName  | string |  货币全称 |
| currAbb  | string |  货币简称 |


```php
{
    "status": "1",
    "message": "successful",
    "data": [
        ['balance':'btc','address':'12EpNaVzhqbudGaDbncpsaujgLYEQLrcJRpsaujgLYEQ1LrcJR','qcode':'/public/storage/aa.png
        '],
    ]
}
```

> ##### 10.获取order 页面的 openOrder 
> * 请求方法  GET
> * 请求路径 /order/openOrder

>##### 请求参数 
> * 传参  page 页码 默认为第一页

| 参数名称 | 是否必填  |值类型| 备注说明 |
| :-------: | :-----:| :----:|:----: |
| page | 否 |  int   | 当前页数 |


>##### 返回参数 data
> * data 二维数组

| 参数名称  | 值类型   |  备注说明  |
| :--------: | :-----:  | :----:|
| add_time  | string |  时间戳  |
| price_btc  | string |  订单价格 |
| net_volume  | string |  交易货币数量 本币量 |
| volume_btc  | string |  交易货币数量 比特币计算 |
| residual_volume  | string |  交易货币剩余量 比特币计算 |
| curr_abb  | string |  货币简称 |
| trade_type  | int |  10 买 10 卖 |
| id  | int |  记录id|



```php
{
    "status": "0",
    "message": "失败",
    "data":[
          "current_page":1,
           "data":[
             0=> [
             'add_time' =>'0.00000002',
             'price_btc' =>'0.00000002',
             'net_volume' =>'0.00000002',
             'volume_btc' =>'0.00000002',
             'residual_volume' =>'0.00000002',
             'curr_abb' =>'0.00000002',
             'trade_type' =>'0.00000002',
             ],
          ]       
          "first_page_url": "https://exchange.alliancecapitals.com/trade/openOrder?page=1",
          "from": 1,
          "last_page": 2,
          "last_page_url": "https://exchange.alliancecapitals.com/trade/openOrder?page=2",
          "next_page_url": "https://exchange.alliancecapitals.com/trade/openOrder?page=2",
          "path": "https://exchange.alliancecapitals.com/trade/openOrder",
          "per_page": 1,
          "prev_page_url": null,
          "to": 1,
          "total": 2,
    ],

}
```



> ##### 11.获取order 页面的 orderHistory 
> * 请求方法  GET
> * 请求路径 /order/orderHistory

>##### 请求参数 
> * 传参  page 页码 默认为第一页

| 参数名称 | 是否必填  |值类型| 备注说明 |
| :-------: | :-----:| :----:|:----: |
| page | 否 |  int   | 当前页数 |


>##### 返回参数 data
> * data 二维数组

| 参数名称  | 值类型   |  备注说明  |
| :--------: | :-----:  | :----:|
| fee_money  | string |  手续费  |
| price_btc  | string |  订单价格 |
| last_time  | string |  完成时间 |
| initial_volume  | string |  已经交易货币数量 比特币计算 |
| curr_abb  | string |  货币名称 |
| trade_type  | int |  10 买 10 卖 |


```php
{
    "status": "0",
    "message": "失败",
    "data":[
          "current_page":1,
           "data":[
             0=> [
             'last_time' =>'0.00000002',
             'price_btc' =>'0.00000002',
             'initial_volume' =>'0.00000002',
             'curr_abb' =>'0.00000002',
             'trade_type' =>'0.00000002',
             ],
          ]       
          "first_page_url": "https://exchange.alliancecapitals.com/trade/orderHistory?page=1",
          "from": 1,
          "last_page": 2,
          "last_page_url": "https://exchange.alliancecapitals.com/trade/orderHistory?page=2",
          "next_page_url": "https://exchange.alliancecapitals.com/trade/orderHistory?page=2",
          "path": "https://exchange.alliancecapitals.com/trade/orderHistory",
          "per_page": 1,
          "prev_page_url": null,
          "to": 1,
          "total": 2,
    ],

}
```


> ##### 12.K 线图数据接口 
> * 请求方法  POST
> * 请求路径 /trade/charts

>##### 请求参数 
> * 传参  currAbb datumType  limit

| 参数名称 | 是否必填  |值类型| 备注说明 |
| :-------: | :-----:| :----:|:----: |
| currAbb | 是|  string  | 货币简称 |
| datumType | 是|  int  |  请求时间 间隔 |
| limit | 否 |  int   | 请求条数  默认为20条 |


>##### 返回参数 data
> * data 二维数组

| 参数名称  | 值类型   |  备注说明  |
| :--------: | :-----:  | :----:|
| open  | string |  开盘价  |
| low  | string |  最低价 |
| high  | string |  最高价 |
| close  | string |  收盘价 |
| average  | string |  平均价 |
| volume  | string |  成交量 |
| datum_time  /int  | 基准时间 时间戳 | 
| late_time  /int  | 最后请求时间 时间戳| 


```php
{
    "status": "1",
    "message": "successful",
    "data":[
         'open' =>'0.00000002',
         'low' =>'0.00000002',
         'high' =>'0.00000002',
         'close' =>'0.00000002',
         'average' =>'0.00000002',
         'volume' =>'0.00000002',
         'datum_time' =>'1523791515',
         'late_time' =>'1523791515',
    ],

}
```


> ##### 13.获取货币货币  24 小时的 最低价格 最高价格 上升幅度
> * 请求方法  POST
> * 请求路径 /trade/tranSummary

>##### 请求参数 
> * 传参  currAbb

| 参数名称 | 是否必填  |值类型| 备注说明 |
| :-------: | :-----:| :----:|:----: |
| currAbb | 是|  string  | 货币简称 |



>##### 返回参数 data
> * data 二维数组

| 参数名称  | 值类型   |  备注说明  |
| :--------: | :-----:  | :----:|
| currAbb  | string |  货币简称  |
| low  | string |  最低价 |
| high  | string |  最高价 |
| volume  | string |  成交量 |
| currName  | string |  货币交易全称 |
| change  | string |  改变量 |
| lastPrice  | string |  当前价格 |
| feeRate  | string |  手续费 |
| status  | string | 状态 up 上 down 下 |



```php
{
    "status": "1",
    "message": "successful",
    "data":[
         'low' =>'0.00000001',
         'high' =>'0.00000002',
         'volume' =>'0.00000002',
         'lastPrice' =>'0.0001',
         'change' =>'24.21',
         'currAbb' =>'bch',
         'currName' =>'Bitcoin Cash',
         'feeRate' => 0.001,
         'status' => 'up',
    ],

}
```