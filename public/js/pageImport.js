"use strict"

// Sending the form to the server and displaying the table
function ImportForm() {
    let formData = new FormData();
    formData.append("file", $("input[type=file]")[0].files[0]);
    $(".import__button").attr("class","button block import__button is-primary is-loading  ml-4")

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

            $(".import__button").attr("class","button block import__button  ml-4")
            $(".finish-processing__button").show()
        }
    })


}

// Checking file size
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

// Parsing of column selection by the user
function ChooseSelect() {
    $(".finish-processing__button").attr('class','finish-processing__button button finish-processing__button box is-primary is-loading')
    let dataArr = {
        "data": {
            "sku": {"index": Number, "value": []},
            "product_name": {"index": Number, "value": []},
            "supplier": {"index": Number, "value": []},
            "price": {"index": Number, "value": []},
            "cnt": {"index": Number, "value": []}
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
            if (status === "success") {
                $(".finish-processing__button").attr('class','finish-processing__button button finish-processing__button box')
                if (msg['status'] !== 0) {
                    alert(`Импортировано ${msg['status']} строк`)
                } else {
                    alert('Данные не импортированы')
                }
            }
            console.log(msg, status)
        }
    )
}