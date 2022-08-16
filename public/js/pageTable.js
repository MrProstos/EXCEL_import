"use strict"

// Show a table with data
function showTable() {
    $(document).ready(function () {
        $.post("?table/update", {page: 0}, function (msg, status) {
            if (status === "success") {
                let data = JSON.parse(msg)
                let dataTable = data["data"]
                console.log(data)
                for (let i = 0; i < dataTable.length; i++) {
                    let sku = dataTable[i]["sku"]
                    let product_name = dataTable[i]["product_name"]
                    let supplier = dataTable[i]["supplier"]
                    let price = dataTable[i]["price"]
                    let cnt = dataTable[i]["cnt"]

                    $(".tbody-result-import__row__hide").clone().attr("class", `tbody-result-import__row-${i}`).appendTo(".result-database-table")

                    let cellTable = $(".tbody-result-import__cell__hide").clone().attr("class", "tbody-result-import__cell")

                    cellTable.clone().text(sku).appendTo(`.tbody-result-import__row-${i}`)
                    cellTable.clone().text(product_name).appendTo(`.tbody-result-import__row-${i}`)
                    cellTable.clone().text(supplier).appendTo(`.tbody-result-import__row-${i}`)
                    cellTable.clone().text(price).appendTo(`.tbody-result-import__row-${i}`)
                    cellTable.clone().text(cnt).appendTo(`.tbody-result-import__row-${i}`)
                }

                for (let i = 0; i < Number(data["nAllRow"][0]); i++) {
                    let nPage = i + 1
                    $(".pagination-list").append(`<li><a onclick="showPage()" class="pagination-link">${nPage}</a></li>`)
                }
            }
        })
    })
}
