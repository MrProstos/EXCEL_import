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

