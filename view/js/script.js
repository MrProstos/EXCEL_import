"use strict";

function Validate() {
    $(document).ready(function () {
        $(".form").validate({
            rules: {
                username: {
                    minlength: 3
                },
                password: {
                    minlength: 8
                },
                password_confirm: {
                    minlength: 8,
                    equalTo: ".password__input"
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
            }
        });
    });
}