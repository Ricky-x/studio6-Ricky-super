<?php

// sendSuccess
// 第一个参数：返回错误信息
// 第2个参数：返回数据
// 第3个参数：返回状态码。 成功的状态码：200.  失败的状态码：400

// 返回给 ajax 统一的数据格式
function sendSuccess($msg = '', $data = [], $code = 200)
{
    $result = [
        'code' => $code,
        'msg'  => $msg,
        'time' => time(),
        'data' => $data,
    ];
    // 把数组转换成json
    return json_encode($result, JSON_UNESCAPED_UNICODE);
}


