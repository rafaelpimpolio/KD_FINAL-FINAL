// ===============================
// Element References
// ===============================
const orderForm     = $("#orderForm");
const dateTimeLocal = $("#dateTimeLocal");
const status        = $("#status");

const inquiryID     = $("#inquiryID");
const employeeID    = $("#employeeID");

const btnSaveRecord = $("#btnSaveRecord");
const tableBody     = $("#tableOrder tbody");

// Hidden variable for edit mode
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

    // Use the raw DOM element to check validity
    const formEl = this;

    if (!formEl.checkValidity()) {
        // Add Bootstrap validation class
        formEl.classList.add("was-validated");

        // Optional: highlight invalid inputs with red border
        $(formEl).find("input, select").each(function() {
            if (!this.checkValidity()) {
                $(this).addClass("is-invalid");
            } else {
                $(this).removeClass("is-invalid");
            }
        });

        return; // Stop submission
    }

    // If valid, remove invalid classes
    $(formEl).find("input, select").removeClass("is-invalid");

    saveOrder();
});

// ===============================
// Save Order (Add / Update)
// ===============================
function saveOrder() {
    const funcName = currentOrderID === "" ? "AddRecord" : "UpdateRecord";

    const postData = {
        func_name: funcName,
        orderID: currentOrderID,
        inquiryID: inquiryID.val().trim(),
        employeeID: employeeID.val().trim(),
        dateTimeLocal: dateTimeLocal.val().trim(),
        status: status.val()
    };

    btnSaveRecord.prop("disabled", true);

    $.ajax({
        type: "POST",
        url: "crud.php",
        data: postData
    }).done(function (response) {
        let res = JSON.parse(response);

        if (res.status === "success") {
            $.alert({ title: "Success", content: res.message });
        } else if (res.status === "error") {
            $.alert({ title: "Error", content: res.message });
        } else {
            $.alert({ title: "Info", content: JSON.stringify(res) });
        }

        resetForm();
        displayOrderList();
    }).fail(function () {
        $.alert({ title: "Error", content: "AJAX request failed." });
    }).always(function () {
        btnSaveRecord.prop("disabled", false);
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
    }).done(function (response) {
        let res = JSON.parse(response);
        tableBody.empty();

        if (res.status === "success" && Array.isArray(res.data)) {
            res.data.forEach(order => {
                const dateValue = order.DateTime ? order.DateTime.replace(" ", "T").slice(0, 16) : "";
                const inquiry  = order.InquiryID ?? "";
                const employee = order.EmployeeID ?? "";

                tableBody.append(`
                    <tr>
                        <td>${order.OrderID}</td>
                        <td>${inquiry}</td>
                        <td>${employee}</td>
                        <td>${order.DateTime}</td>
                        <td>${order.Status}</td>
                        <td>
                            <button class="btnEdit btn btn-warning btn-sm me-1">EDIT</button>
                            <button class="btnDelete btn btn-danger btn-sm">DELETE</button>
                        </td>
                    </tr>
                `);
            });
        } else {
            $.alert({ title: "Info", content: res.message || "No orders found." });
        }
    }).fail(function () {
        $.alert({ title: "Error", content: "Failed to load orders." });
    });
}

// ===============================
// Edit Order
// ===============================
$(document).on("click", ".btnEdit", function () {
    const row = $(this).closest("tr");

    currentOrderID = row.find("td:eq(0)").text().trim();
    inquiryID.val(row.find("td:eq(1)").text().trim());
    employeeID.val(row.find("td:eq(2)").text().trim());

    const dt = row.find("td:eq(3)").text().trim();
    dateTimeLocal.val(dt ? dt.replace(" ", "T").slice(0, 16) : "");

    status.val(row.find("td:eq(4)").text().trim());

    btnSaveRecord.text("SAVE CHANGES");

    // Remove previous validation highlights
    orderForm[0].classList.remove("was-validated");
    $(orderForm).find("input, select").removeClass("is-invalid");
});

// ===============================
// Delete Order
// ===============================
$(document).on("click", ".btnDelete", function () {
    const orderID = $(this).closest("tr").find("td:eq(0)").text().trim();

    $.confirm({
        title: "Delete Order",
        content: "Are you sure you want to delete Order ID: " + orderID + "?",
        buttons: {
            Yes: function () { deleteOrder(orderID); },
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
        data: { func_name: "DeleteRecord", orderID }
    }).done(function (response) {
        const res = JSON.parse(response);

        if (res.status === "success") {
            $.alert({ title: "Deleted", content: res.message });
        } else {
            $.alert({ title: "Error", content: res.message || "Failed to delete." });
        }

        displayOrderList();
    }).fail(function () {
        $.alert({ title: "Error", content: "AJAX request failed." });
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
    $(orderForm).find("input, select").removeClass("is-invalid");
}
