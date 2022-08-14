"use strict";

const SERVER_URL = "http://mrprostos.keenetic.link/"

function ImportForm() {

    let formData = new FormData();
    formData.append("file", $("input[type=file]")[0].files[0]);

    $.ajax({
        url: SERVER_URL + "?import/import",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (data) {

            let result = JSON.parse(data);


            for (let i = 0; i < result[0].length; i++) {
                let theadResultCell = `<th class="thead-result__cell">
                <div class="select">
                    <select class="select-col">
                        <option value="">Null</option>
                        <option value="sku">Артикул</option>
                        <option value="product_name">Название товара</option>
                        <option value="supplier">Поставщик</option>
                        <option value="price">Цена</option>
                        <option value="cnt">Кол-во</option>
                    </select>
                </div>
            </th>`

                $(".thead__row").append(theadResultCell)
            }

            for (let i = 0; i < result.length; i++) {
                $(".tbody-result-import").append(`<tr class="tbody-result-import__row-${i}"></tr>`) // TODO Исправить потом
                for (let j = 0; j < result[i].length; j++) {
                    $(`.tbody-result-import__row-${i}`).append(`<td class="tbody-result-import__cell">${result[i][j]}</td>`)
                }
            }
        }
    })
}