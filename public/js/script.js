"use strict";

function ValidateSignIn() {
    $(document).ready(function () {
        $(".sign-in__form").validate({
            rules: {
                email: {
                    required: true,
                    email: true
                },
                password: {
                    required: true,
                    minlength: 8
                },
            },
            messages: {
                email: {
                    required: "это поле обязательно для заполнения",
                    email: "email некорректный"
                },
                password: {
                    required: "это поле обязательно для заполнения",
                    minlength: "минимальная длинна пароля 8 символов"
                }
            }
        });
    })
}

function ValidateSignUp() {
    $(document).ready(function () {
        $(".sign-up__form").validate({
            rules: {
                email: {
                    required: true,
                    email: true
                },
                username: {
                    required: true,
                    minlength: 3
                },
                password: {
                    required: true,
                    minlength: 8
                },
                password_confirm: {
                    required: true,
                    minlength: 8,
                    equalTo: ".password__input"
                }
            },
            messages: {
                email: {
                    required: "это поле обязательно для заполнения",
                    email: "email некорректный"
                },
                username: {
                    required: "это поле обязательно для заполнения",
                    minlength: "минимальная длинна логина 3 символа"
                },
                password: {
                    required: "это поле обязательно для заполнения",
                    minlength: "минимальная длинна пароля 8 символов"
                },
                password_confirm: {
                    required: "это поле обязательно для заполнения",
                    equalTo: "пароли не совпадают"
                }
            }
        });
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

function showTable() {
    $(document).ready(function () {
        $.post("?table/update", function (msg, status) {
            console.log(msg,status)
            // let dataTable = JSON.parse(msg)

        })
    })
}
