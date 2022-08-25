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
        url: '/api/v1/',
        method: 'POST',
        headers: {'Authorization': '63070b4c04a604.64646160'},
        dataType: 'json',
        data: {
            'method': 'add',
            'params': {
                'sku': '000006890',
                'product_name': 'Кран-балка 007',
                'supplier': 'GTK.',
                'price': '122',
                'cnt': '12245.88'
            }},
        success: function (data) {
            console.log(data)
        },
        error: function (jqXHD, error) {
            console.log(jqXHD, error)
        }
    })
}