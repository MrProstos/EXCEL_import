'use strict';

//Search using the Sphinx
// TODO Sphinx доделать пейджер
function Search() {
    $(document).ready(function () {
        let search_value = $('.search__input').val();
        console.log(search_value)
        if (search_value !== '') {
            window.location.href = '/search/' + search_value + '/0'
        }
    })
}
