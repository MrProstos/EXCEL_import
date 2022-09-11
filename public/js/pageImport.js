'use strict'

// Sending the form to the server and displaying the table
function ImportForm()
{
    $('.new-thead-result__cell').remove()
    $('.new-tbody-result-import__row').remove()

    let formData = new FormData();
    formData.append('file', $('input[type=file]')[0].files[0]);
    $('.import__button').attr('class', 'button block import__button is-primary is-loading  ml-4')

    $.ajax({
        url: '/import/import/',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (data) {
            let result = JSON.parse(data);
            let button = $('.import__button')

            if (result === 2) {
                button.attr('class', 'button block import__button  ml-4')
                alert('Размер файла превышает допустимый предел')
                return
            }
            if (result === 1) {
                button.attr('class', 'button block import__button  ml-4')
                alert('Неверный формат файла')
                return
            }

            for (let i = 0; i < result[0].length; i++) {
                $('.thead-result__cell__hide').clone().attr('class', 'thead-result__cell new-thead-result__cell').appendTo('.thead__row')
            }

            for (let i = 0; i < result.length; i++) {
                $('.tbody-result-import__row__hide').clone().attr('class', `tbody-result-import__row-${i} new-tbody-result-import__row`).appendTo('.tbody-result-import')

                for (let j = 0; j < result[0].length; j++) {
                    $('.tbody-result-import__cell__hide').clone().attr('class', 'tbody-result-import__cell').text(result[i][j]).appendTo(`.tbody-result-import__row-${i}`)
                }
            }
            $('.finish-processing__button').show()
            button.attr('class', 'button block import__button  ml-4')
            $('.import__table-container').show()
        },
    })
}

// Checking file size
function NameFile()
{
    $(document).ready(function () {
        $('.file__input').change(function () {
            $('.file-name').text(this.files[0]['name'])
        });
    });
}

// Parsing of column selection by the user
function ChooseSelect()
{
    $('.finish-processing__button').attr('class', 'finish-processing__button button finish-processing__button box is-primary is-loading')

    let indexArr = {'sku': Number, 'product_name': Number, 'supplier': Number, 'price': Number, 'cnt': Number}
    let dataMsg = [];

    $('.select-col option:selected').each(function (indexSelect, valueSelect) {
        if ($(valueSelect).val() !== '') {
            indexArr[$(valueSelect).val()] = indexSelect - 1
        }
    });

    $('.tbody-result-import').children().each(function (indexRow, valueRow) {
        let sku, product_name, supplier, price, cnt

        $(valueRow).children().each(function (indexCell, valueCell) {
            if ($(valueCell).text() === undefined || $(valueCell).text() === '') {
                return
            }
            if (indexCell === indexArr['sku']) {
                sku = $(valueCell).text()
            }
            if (indexCell === indexArr['product_name']) {
                product_name = $(valueCell).text()
            }
            if (indexCell === indexArr['supplier']) {
                supplier = $(valueCell).text()
            }
            if (indexCell === indexArr['price']) {
                price = $(valueCell).text()
            }
            if (indexCell === indexArr['cnt']) {
                cnt = $(valueCell).text()
            }
        })

        if (sku !== undefined || product_name !== undefined || supplier !== undefined || price !== undefined || cnt !== undefined) {
            dataMsg.push({'sku': sku, 'product_name': product_name, 'supplier': supplier, 'price': price, 'cnt': cnt})
        }
    })

    console.log(dataMsg)
    $.ajax({
        url: '/import/insertTable/',
        type: 'POST',
        dataType: 'json',
        data: {data: dataMsg},
        success: function (data) {
            $('.finish-processing__button').attr('class', 'finish-processing__button button finish-processing__button box')
            if (data['status'] !== 0) {
                alert('Импортировано строк - ' + data['status'])
            } else {
                alert('Данные не импортированы')
            }
        },
        error: function (jqXHD) {
            console.log(jqXHD)
        },
    })
}