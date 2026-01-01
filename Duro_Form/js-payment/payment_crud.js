// ================== DISPLAY PAYMENT LIST ==================
function DisplayPaymentList() {
    $.ajax({
        type: "POST",
        url: "crud.php",
        data: { func_name: "DisplayPayment" }
    })
    .done(function(msg) {
        let list;
        try { list = JSON.parse(msg); } catch(e){ list = []; }

        $("#paymentTable > tbody").empty();

        list.forEach(p => {
            let row = "<tr>";
            row += "<td>" + p.payment_id + "</td>";
            row += "<td>" + p.transaction_id + "</td>";
            row += "<td>" + p.payment_reference + "</td>";
            row += "<td>" + p.transaction_status + "</td>";
            row += "<td>" + p.method_payment + "</td>";
            row += "<td>" + p.payment_date + "</td>";
            row += "<td>" + p.amount + "</td>";
            row += "<td>" + p.balance + "</td>";
            row += "<td>";
            row += "<button class='btnEdit btn btn-warning btn-sm'>EDIT</button> ";
            row += "<button class='btnDelete btn btn-danger btn-sm'>DELETE</button>";
            row += "</td>";
            row += "</tr>";
            $("#paymentTable > tbody").append(row);
        });
    });
}

// ================== SAVE / UPDATE PAYMENT ==================
$('#paymentForm').on('submit', function(e) {
    e.preventDefault();
    const form = this;

    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }

    let formData = new FormData(this);
    if ($("#btnSavePayment").text() === "SAVE CHANGES")
        formData.append("func_name", "UpdatePayment");
    else
        formData.append("func_name", "AddPayment");

    $.ajax({
        type: "POST",
        url: "crud.php",
        data: formData,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
        let message;
        try { message = JSON.parse(msg); } catch (e) { message = msg; }

        $.alert({
            title: 'Manage Payment',
            content: message,
            type: 'green',
            theme: 'modern',
            icon: 'fa fa-check',
            boxWidth: '30%',
            useBootstrap: false,
            buttons: { ok: { text: 'OK', btnClass: 'btn-green' } }
        });

        $("#paymentForm")[0].reset();
        $("#btnSavePayment").text("CONFIRM");
        $("#paymentID").val("");
        DisplayPaymentList();
    })
    .fail(function(xhr, status, err) {
        $.alert({
            title: 'Error',
            content: 'AJAX error: ' + err,
            type: 'red',
            theme: 'modern',
            icon: 'fa fa-times',
            boxWidth: '30%',
            useBootstrap: false,
            buttons: { ok: { text: 'OK', btnClass: 'btn-red' } }
        });
        console.error(xhr.responseText);
    });
});

// ================== EDIT PAYMENT ==================
$(document).on("click", ".btnEdit", function(e) {
    e.preventDefault();
    let row = $(this).closest("tr");

    $("#paymentID").val(row.find("td:eq(0)").text().trim());
    $("#transactionID").val(row.find("td:eq(1)").text().trim());
    $("#paymentReference").val(row.find("td:eq(2)").text().trim());
    $("#transactionStatus").val(row.find("td:eq(3)").text().trim());
    $("#methodPayment").val(row.find("td:eq(4)").text().trim());
    $("#paymentDate").val(row.find("td:eq(5)").text().trim());
    $("#amount").val(row.find("td:eq(6)").text().trim());
    $("#balance").val(row.find("td:eq(7)").text().trim());

    $("#btnSavePayment").text("SAVE CHANGES");

    $.alert({
        title: 'Edit Mode',
        content: 'You can now edit this payment and click SAVE CHANGES.',
        type: 'blue',
        theme: 'modern',
        icon: 'fa fa-edit',
        boxWidth: '30%',
        useBootstrap: false,
        buttons: { ok: { text: 'OK', btnClass: 'btn-blue' } }
    });
});

// ================== DELETE PAYMENT ==================
$(document).on("click", ".btnDelete", function(e) {
    e.preventDefault();
    let row = $(this).closest("tr");
    let id = row.find("td:eq(0)").text().trim();
    let ref = row.find("td:eq(2)").text().trim();

    $.confirm({
        title: 'Confirm Delete',
        content: 'Do you want to delete payment <b>' + ref + '</b>?',
        type: 'red',
        theme: 'modern',
        icon: 'fa fa-trash',
        boxWidth: '35%',
        useBootstrap: false,
        buttons: {
            Yes: {
                text: 'Yes, Delete',
                btnClass: 'btn-red',
                action: function() {
                    $.ajax({
                        type: "POST",
                        url: "crud.php",
                        data: { func_name: "DeletePayment", paymentID: id }
                    })
                    .done(function(msg) {
                        let message;
                        try { message = JSON.parse(msg); } catch(e){ message = msg; }

                        $.alert({
                            title: 'Deleted',
                            content: message,
                            type: 'green',
                            theme: 'modern',
                            icon: 'fa fa-check',
                            boxWidth: '30%',
                            useBootstrap: false,
                            buttons: { ok: { text: 'OK', btnClass: 'btn-green' } }
                        });

                        DisplayPaymentList();
                    });
                }
            },
            No: {
                text: 'Cancel',
                btnClass: 'btn-secondary'
            }
        }
    });
});

// ================== INITIALIZE ==================
$(document).ready(function () {
    DisplayPaymentList();
});
