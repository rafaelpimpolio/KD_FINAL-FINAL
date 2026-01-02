// ===============================
// Element References (MATCH GUI)
// ===============================
const orderForm     = $("#orderForm");
const dateTimeLocal = $("#dateTimeLocal");
const status        = $("#status");

const inquiryID     = $("#inquiryID");
const employeeID    = $("#employeeID");

const btnSaveRecord = $("#btnSaveRecord");
const tableBody     = $("#tableOrder tbody");

// hidden variable for edit mode
let currentOrderID = "";

// ===============================
// Load records on page load
// ===============================
$(document).ready(function () {
    displayOrderList();
});

// ===============================
// Form Submit (Add / Update)
// ===============================
orderForm.on("submit", function (e) {
    e.preventDefault();

    if (!this.checkValidity()) {
        this.classList.add("was-validated");
        return;
    }

    saveOrder();
});

// ===============================
// Save Order (Add / Update)
// ===============================
function saveOrder() {

    let funcName = currentOrderID === ""
        ? "AddRecord"
        : "UpdateRecord";

    let postData = {
        func_name: funcName,
        orderID: currentOrderID,
        inquiryID: inquiryID.val(),
        employeeID: employeeID.val(),
        dateTimeLocal: dateTimeLocal.val(),
        status: status.val()
    };

    $.ajax({
        type: "POST",
        url: "crud.php",
        data: postData
    }).done(function (msg) {
        let message = JSON.parse(msg);
        $.alert({ title: "Order", content: message });

        resetForm();
        displayOrderList();
    });
}

// ===============================
// Display Orders
// ===============================
function displayOrderList() {
    $.ajax({
        type: "POST",
        url: "crud.php",
        data: { func_name: "DisplayRecord" }
    }).done(function (msg) {

        let orders = JSON.parse(msg);
        tableBody.empty();

        orders.forEach(order => {
            tableBody.append(`
                <tr>
                    <td>${order.OrderID}</td>
                    <td>${order.InquiryID ?? ""}</td>
                    <td>${order.EmployeeID ?? ""}</td>
                    <td>${order.DateTime}</td>
                    <td>${order.Status}</td>
                    <td>
                        <button class="btnEdit btn btn-warning btn-sm me-1">EDIT</button>
                        <button class="btnDelete btn btn-danger btn-sm">DELETE</button>
                    </td>
                </tr>
            `);
        });
    });
}

// ===============================
// Edit Order
// ===============================
$(document).on("click", ".btnEdit", function () {

    let row = $(this).closest("tr");

    currentOrderID = row.find("td:eq(0)").text().trim();
    inquiryID.val(row.find("td:eq(1)").text().trim());
    employeeID.val(row.find("td:eq(2)").text().trim());
    dateTimeLocal.val(row.find("td:eq(3)").text().trim());
    status.val(row.find("td:eq(4)").text().trim());

    btnSaveRecord.text("SAVE CHANGES");
});

// ===============================
// Delete Order
// ===============================
$(document).on("click", ".btnDelete", function () {

    let orderID = $(this).closest("tr").find("td:eq(0)").text().trim();

    $.confirm({
        title: "Delete Order",
        content: "Are you sure you want to delete Order ID: " + orderID + "?",
        buttons: {
            Yes: function () {
                deleteOrder(orderID);
            },
            No: function () {}
        }
    });
});

// ===============================
// Delete Order Function
// ===============================
function deleteOrder(orderID) {

    $.ajax({
        type: "POST",
        url: "crud.php",
        data: {
            func_name: "DeleteRecord",
            orderID: orderID
        }
    }).done(function (msg) {
        let message = JSON.parse(msg);
        $.alert({ title: "Order Deleted", content: message });
        displayOrderList();
    });
}

// ===============================
// Reset Form
// ===============================
function resetForm() {

    currentOrderID = "";
    inquiryID.val("");
    employeeID.val("");
    dateTimeLocal.val("");
    status.val("");

    btnSaveRecord.text("SAVE RECORD");
    orderForm[0].classList.remove("was-validated");
}
