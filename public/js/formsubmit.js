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
        success : function(data) {
            let result = JSON.parse(data);
            $(".tbody-result-import__row").forEach(function (value) {

            })
        }
    })
}