'use strict';

const TOKEN = 'test'

//Get a token
function getToken() {
    Cookies.get('hash')
    $.get('/api/token/' + Cookies.get('hash'), function (data, status) {
        if (status === 'success') {
            let response = JSON.parse(data)
            $('.token').text('Ваш токен авторизации -> ' + response['token'])
        }
    })
}

//Generate data for the API
function generateData(method, num) {
    let randStr = function () {
        let chars = 'abdehkmnpswxzABDEFGHKMNPQRSTWXZ123456789';
        let str = '';
        for (let i = 0; i < 8; i++) {
            let pos = Math.floor(Math.random() * chars.length);
            str += chars.substring(pos, pos + 1);
        }
        return str;
    }

    let data = []

    for (let i = 0; i < num; i++) {
        switch (method) {
            case 'get':
            case 'delete':
                data.push({'sku': randStr(),})
                break
            case 'add':
            case 'update':
            case 'replace':
                data.push({
                    'sku': randStr(),
                    'product_name': randStr(),
                    'supplier': randStr(),
                    'price': Math.floor(Math.random() * num * 10),
                    'cnt': Math.floor(Math.random() * num * 10)
                })
        }
    }
    return data
}

//Send data
function randomDataSend() {
    let method = ['add', 'update', 'get', 'replace', 'delete']
    for (let index = 0; index < method.length; index++) {
        $.ajax({
            url: '/api/clients/',
            method: 'POST',
            headers: {'Authorization': TOKEN},
            dataType: 'json',
            async: false,
            data: {
                'method': method[index],
                'params': generateData(method[index], 20)
            },
            success: function (data, status) {
                console.log('Метод: ' + method[index], status, data)
            },
            error: function (jqXHD) {
                console.log(jqXHD)
            },
        })
    }
}

function Add() {
    $.ajax({
        url: '/api/clients/',
        method: 'POST',
        headers: {'Authorization': TOKEN},
        dataType: 'json',
        data: {
            'method': 'add',
            'params': [{
                'sku': '000006890',
                'product_name': 'Кофта',
                'supplier': 'МояОдежда',
                'price': 122,
                'cnt': 74
            }, {
                'sku': '000007777',
                'product_name': 'Майка',
                'supplier': 'МояОдежда',
                'price': 187,
                'cnt': 122
            }, {
                'sku': '000006666',
                'product_name': 'Штаны',
                'supplier': 'МояОдежда',
                'price': 300,
                'cnt': 12
            }]
        },
        success: function (data) {
            console.log(data)
        },
        error: function (jqXHD) {
            console.log(jqXHD)
        },
    })
}

function Update() {
    $.ajax({
        url: '/api/clients/',
        method: 'POST',
        headers: {'Authorization': TOKEN},
        dataType: 'json',
        data: {
            'method': 'update',
            'params': [{
                'sku': '000006890',
                'product_name': 'Кофта',
                'supplier': 'UPDATE',
                'price': 122,
                'cnt': 74
            }, {
                'sku': '000007777',
                'product_name': 'Кофта',
                'supplier': 'UPDATE',
                'price': 122,
                'cnt': 74
            }, {
                'sku': '000006666',
                'product_name': 'Кофта',
                'supplier': 'UPDATE',
                'price': 122,
                'cnt': 74
            }, {
                'sku': 'WWW',
                'product_name': 'Куртка',
                'supplier': 'UPDATE',
                'price': 122,
                'cnt': 74
            }]
        },
        success: function (data) {
            console.log(data)
        },
        error: function (jqXHD) {
            console.log(jqXHD)
        },
    })
}

function Get() {
    $.ajax({
        url: '/api/clients/',
        method: 'POST',
        headers: {'Authorization': TOKEN},
        dataType: 'json',
        data: {
            'method': 'get',
            'params': [{
                'sku': '000006890',
            }, {
                'sku': '000007777',
            }, {
                'sku': '000006666',
            }, {
                'sku': 'WWW',
            }]
        },
        success: function (data) {
            console.log(data)
        },
        error: function (jqXHD) {
            console.log(jqXHD)
        },
    })
}

function Delete() {
    $.ajax({
        url: '/api/clients/',
        method: 'POST',
        headers: {'Authorization': TOKEN},
        dataType: 'json',
        data: {
            'method': 'delete',
            'params': [{
                'sku': '000006890',
            }, {
                'sku': '000007777',
            }, {
                'sku': '000006666',
            }]
        },
        success: function (data) {
            console.log(data)
        },
        error: function (jqXHD) {
            console.log(jqXHD)
        },
    })
}

function Replace() {
    $.ajax({
        url: '/api/clients/',
        method: 'POST',
        headers: {'Authorization': TOKEN},
        dataType: 'json',
        data: {
            'method': 'replace',
            'params': [{
                'sku': '000006890',
                'product_name': 'Кофта',
                'supplier': 'UPDATE',
                'price': 122,
                'cnt': 74
            }, {
                'sku': '000007777',
                'product_name': 'Кофта',
                'supplier': 'UPDATE',
                'price': 122,
                'cnt': 74
            }, {
                'sku': '000006666',
                'product_name': 'Кофта',
                'supplier': 'UPDATE',
                'price': 122,
                'cnt': 74
            }, {
                'sku': 'WWW',
                'product_name': 'Куртка',
                'supplier': 'UPDATE',
                'price': 122,
                'cnt': 74
            }]
        },
        success: function (data) {
            console.log(data)
        },
        error: function (jqXHD) {
            console.log(jqXHD)
        },
    })
}