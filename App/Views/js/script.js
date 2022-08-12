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

function UniqueSelect() {
    $(function () {
        $(".select-col").change(function () {
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

function PageSwitch() {
    $(document).ready(function () {
        $(".pagination-link").click(function () {
           console.log( )

        })
    })

}