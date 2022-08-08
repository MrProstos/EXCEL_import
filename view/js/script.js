"use strict";

function Validate() {
    $(document).ready(function () {
        $(".form").validate({
            rules : {
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
};