"use strict";

function ValidateSignIn() {
    $(document).ready(function () {
        $(".form").validate({
            rules: {
                email: {
                    email: true
                },
                password: {
                    minlength: 8
                },
            },
            messages: {
                email: {
                    email:"email некорректный"
                },
                password: {
                    minlength: "минимальная длинна пароля 8 символов"
                }
            }
        });
    })
}

function ValidateSignUp() {
    $(document).ready(function () {
        $(".form").validate({
            rules: {
                email : {
                    email: true
                },
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
            },
            messages: {
                email: {
                    email:"email некорректный"
                },
                username: {
                  minlength: "минимальная длинна логина 3 символа"
                },
                password: {
                    minlength: "минимальная длинна пароля 8 символов"
                },
                password_confirm: {
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
            }
        });
    });
}

function UniqueSelect() {
    $(function () {
        $(".select-col").change(function() {
            let used = new Set;
            $(".select-col").each(function () {
                let reset = false;
                $("option", this).each(function () {
                    let hide = used.has($(this).text());
                    if (hide && $(this).is(':selected')) reset = true;
                    $(this).prop("hidden", hide);
                });
                if (reset) $("option:not([hidden]):first", this).prop("selected", true);
                used.add($("option:selected", this).text());
            });
        }).trigger("change");
    });
}