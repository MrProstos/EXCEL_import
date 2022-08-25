'use strict';

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
        headers: {'Authorization': '63070b4c04a604.64646160'},
        dataType: 'json',
        data: {
            'method': 'get',
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