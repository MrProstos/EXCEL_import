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
                $(".thead-result__cell__hide").clone().attr("class", "thead-result__cell").appendTo(".thead__row")
            }

            for (let i = 0; i < result.length; i++) {
                $(".tbody-result-import__row__hide").clone().attr("class", `tbody-result-import__row-${i}`).appendTo(".tbody-result-import")
                for (let j = 0; j < result[0].length; j++) {
                    $(".tbody-result-import__cell__hide").clone().attr("class", "tbody-result-import__cell").text(result[i][j]).appendTo(`.tbody-result-import__row-${i}`)
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
        console.log(indexSelect, $(valueSelect).val())
        if ($(valueSelect).val() !== "") {
            dataArr["data"][$(valueSelect).val()]["index"] = indexSelect


        }
    });

    for (let index in dataArr["data"]) {

        $(".tbody-result-import").children().each(function (indexRow, valueRow) {

            $(valueRow).children().each(function (indexCell, valueCell) {

                if (indexCell === dataArr["data"][index]["index"] - 1) {

                    if ($(valueCell).text() !== "") {
                        
                        dataArr["data"][index]["value"].push($(valueCell).text())
                    }

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