
function showTable() {
    $(document).ready(function () {
        $.post("?table/update", {page: 0}, function (msg, status) {
            if (status === "success") {
                let dataTable = JSON.parse(msg)
                for (let i = 0; i < dataTable.length; i++) {
                    let sku = dataTable[i]["sku"]
                    let product_name = dataTable[i]["product_name"]
                    let supplier = dataTable[i]["supplier"]
                    let price = dataTable[i]["price"]
                    let cnt = dataTable[i]["cnt"]

                    $(".result-database-table").append(`<tr class="result-database-table__row-${i}"></tr>`)
                        .append(`<td>${sku}</td>
                        <td>${product_name}</td>
                        <td>${supplier}</td>
                        <td>${price}</td>
                        <td>${cnt}</td>`)
                }

                for (let i = 0; i < dataTable[0].length; i++) {
                    // $(".result-database-table__row").append(`<tr class="result-database-table__row"></tr>`)
                }
                console.log((dataTable))
            }


        })
    })
}
