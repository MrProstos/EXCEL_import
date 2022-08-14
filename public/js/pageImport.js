"use strict"

function ImportForm() {

    let formData = new FormData();
    formData.append("file", $("input[type=file]")[0].files[0]);

    $.ajax({
        url: "?import/import",
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

function SizeFile() {
    const MAX_FILE_SIZE = 30 * 1024 * 1024; // 30MB

    $(document).ready(function () {
        $(".file__input").change(function () {
            if (this.files[0].size > MAX_FILE_SIZE) {
                console.log("Файл больше 30 MB!");
                alert("Файл больше 30 MB!");
                return
            }
            $(".file-name").text(this.files[0]["name"])
        });
    });
}



function ChooseSelect() {

    let dataArr = {
        "data": {
            "sku": {"index": null, "value": []},
            "product_name": {"index": null, "value": []},
            "supplier": {"index": null, "value": []},
            "price": {"index": null, "value": []},
            "cnt": {"index": null, "value": []}
        }
    }

    $(".select-col option:selected").each(function (indexSelect, valueSelect) {
        if ($(valueSelect).val() !== "") {
            dataArr["data"][$(valueSelect).val()]["index"] = indexSelect
            console.log(indexSelect, $(valueSelect).val())
        }
    });

    for (let index in dataArr["data"]) {
        $(".tbody-result-import").children().each(function (indexRow, valueRow) {
            $(valueRow).children().each(function (indexCell, valueCell) {
                if (indexCell === dataArr["data"][index]["index"]) {
                    dataArr["data"][index]["value"].push($(valueCell).text())
                    // console.log($(valueCell).text())
                }
            })
        })
    }
    console.log(dataArr)

    $.post("?import/insertTable", dataArr, function (msg, status) {
            console.log(msg, status)
        }
    )
}