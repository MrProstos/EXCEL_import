'use strict';

//Search using the Sphinx
function Search() {
    $('.search__input').keyup(function () {
        let search_value = $(this).val();

        $.ajax({
            url: '/search/',
            method: 'POST',
            dataType: 'json',
            data: {'search_word': search_value},
            success: function (data) {

                if (data !== []) {
                    $('.result-database-table').empty()
                    data.forEach(function (value) {
                        // TODO потом удалить html
                        // TODO Переделать если надо с отправкой get запроса
                        $('.result-database-table').append(`<tr><td>${value['sku']}</td>
                                                            <td>${value['product_name']}</td>
                                                            <td>${value['supplier']}</td>
                                                            <td>${value['price']}</td>
                                                            <td>${value['cnt']}</td></tr>`)
                    })
                }
            },
        })
    });
}

//Redirect back
function Reset() {
    window.location.href = '/table/0';
}