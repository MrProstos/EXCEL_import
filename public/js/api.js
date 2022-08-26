'use strict';

const TOKEN = '63070b4c04a604.64646160'

function getToken() {
    Cookies.get('hash')
    $.get('/api/token/' + Cookies.get('hash'), function (data, status) {
        if (status === 'success') {
            let response = JSON.parse(data)
            $('.token').text('Ваш токен авторизации -> ' + response['token'])
        }
    })
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
            },{
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