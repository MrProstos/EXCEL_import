"use strict";

const SERVERURL = "http://mrprostos.keenetic.link/"

function ImportForm() {

    let formData = new FormData();
    formData.append("file", $("input[type=file]")[0].files[0]);

    $.ajax({
        url: SERVERURL + "?import/import",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (data) {

            let result = JSON.parse(data);
            let theadResultCell = $(".thead-result__cell")

            for (let i = 0; i < result[0].length; i++) {
                theadResultCell.clone().appendTo(".thead__row")
            }

            let tbodyResultImportRow = $(".tbody-result-import__row")

            result.forEach(function () {
                tbodyResultImportRow.clone().appendTo(".tbody-result-import")
            })

            let tbodyResultImportCell = $(".tbody-result-import__cell")

            tbodyResultImportCell.each(function (index, value) {

                result[index].forEach(function (n) {
                    $(value).clone().text(n).appendTo(".tbody-result-import__row")
                })
                console.log(result[index], value)
            })


        }
    })
}