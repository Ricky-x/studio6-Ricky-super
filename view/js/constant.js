const API_URL = 'http://super.test0009.com/';
const TOKEN = "user_id"

let token = localStorage.getItem(TOKEN);

//Ajax封装
var postAjax = function (url, postData, sucessFunction, options) {

    var flag = false;
    var defaultOptions = {
        header: "",
        type: "post",
        async: true,
        dataType: "json",
        contentType: "application/json; charset=utf-8",
        errorFunction: errorFunction
    }
    /**
     * $.extend(target, objec);将一个或多个对象的内容合并到目标对象。
     *  注意事项：
     *    1.如果只为$.extend()指定了一个参数，则意味着参数target被省略。此时，target就是jQuery对象本身。通过这种方式，我们可以为全局对象jQuery添加新的函数。
     *    2.如果多个对象具有相同的属性，则后者会覆盖前者的属性值。
     */
    var currentOptions = $.extend(defaultOptions, options);

    if (defaultOptions.type == 'post') {
        postData = JSON.stringify(postData);
    }

    $.ajax({
        headers: currentOptions.header,
        type: currentOptions.type,
        url: url,
        async: currentOptions.async,
        data: postData,
        dataType: currentOptions.dataType,
        contentType: currentOptions.contentType,
        beforeSend: function (data) {

        },
        // statusCode: {
        //     401: function (data) {
        //         console.log('----401---------');
        //         console.log(data.msg);
        //         console.log('----401---------');
        //         localStorage.setItem(TOKEN, null);
        //         window.location.replace("login.html")
        //     }, 200: function (data) {
        //         console.log('----200---------');
        //         sucessFunction(data);
        //         flag = true;
        //     }, 400: function (data) {
        //         defaultOptions.errorFunction(data.responseJSON);
        //         flag = false;
        //     }
        // },
        success: function (data) {
            switch (data.code) {
                case 200:
                    console.log('----1---------');
                    console.log(data);
                    sucessFunction(data);
                    flag = true;
                    break;
                case 400:
                    defaultOptions.errorFunction(data);
                    console.log('----2---------');
                    break;
                case 401:
                    console.log('----3---------');
                    break;
                default:
                    console.log('----4---------');
                    break;
            }
        },
        complete: function (data) {

        },
        error: function (data) {
        }
    });
    return flag;
}

function errorFunction(data) {
    alert(data.msg)
}

function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg); //获取url中"?"符后的字符串并正则匹配
    var context = "";
    if (r != null) context = decodeURIComponent(r[2]);
    reg = null;
    r = null;
    return context == null || context == "" || context == "undefined" ? "" : context;
}


