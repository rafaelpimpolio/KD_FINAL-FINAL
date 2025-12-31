// ================== DISPLAY CUSTOMER LIST ==================

function DisplayCustomerList() {
    $.ajax({
        type: "POST",
        url: "crud.php",
        data: { func_name: "DisplayCustomer" }
    })
    .done(function(msg) {
        let list;
        try { list = JSON.parse(msg); } catch(e){ list = []; }

        $("#customerTable > tbody").empty();

        list.forEach(c => {
            let row = "<tr>";
            row += "<td>" + c.customer_id + "</td>";
            row += "<td>" + c.first_name + "</td>";
            row += "<td>" + c.last_name + "</td>";
            row += "<td>" + c.phone_number + "</td>";
            row += "<td>" + c.email + "</td>";
            row += "<td>" + c.barangay + "</td>";
            row += "<td>" + c.city_municipality + "</td>";
            row += "<td>" + c.province + "</td>";
            row += "<td>" + c.postal_code + "</td>";
            row += "<td>";
            row += "<button class='btnEdit btn btn-warning btn-sm'>EDIT</button> ";
            row += "<button class='btnDelete btn btn-danger btn-sm'>DELETE</button>";
            row += "</td>";
            row += "</tr>";
            $("#customerTable > tbody").append(row);
        });
    });
}

// ================== SAVE / UPDATE CUSTOMER ==================
$('#customerForm').on('submit', function(e) {
    e.preventDefault();
    const form = this;

    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }

    let formData = new FormData(this);
    if ($("#btnSaveCustomer").text() === "SAVE CHANGES")
        formData.append("func_name", "UpdateCustomer");
    else
        formData.append("func_name", "AddCustomer");

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
            title: 'Manage Record',
            content: message,
            type: 'green',
            theme: 'modern',
            icon: 'fa fa-check',
            boxWidth: '30%',
            useBootstrap: false,
            buttons: { ok: { text: 'OK', btnClass: 'btn-green' } }
        });

        $("#customerForm")[0].reset();
        $("#btnSaveCustomer").text("CONFIRM");
        $("#customerID").val("");
        DisplayCustomerList();
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

// ================== EDIT CUSTOMER ==================
$(document).on("click", ".btnEdit", function(e) {
    e.preventDefault();
    let row = $(this).closest("tr");

    $("#customerID").val(row.find("td:eq(0)").text().trim());
    $("#firstName").val(row.find("td:eq(1)").text().trim());
    $("#lastName").val(row.find("td:eq(2)").text().trim());
    $("#phone").val(row.find("td:eq(3)").text().trim());
    $("#email").val(row.find("td:eq(4)").text().trim());
    $("#barangay").val(row.find("td:eq(5)").text().trim());
    $("#city").val(row.find("td:eq(6)").text().trim());
    $("#province").val(row.find("td:eq(7)").text().trim());
    $("#postalCode").val(row.find("td:eq(8)").text().trim());

    $("#btnSaveCustomer").text("SAVE CHANGES");

    $.alert({
        title: 'Edit Mode',
        content: 'You can now edit this customer and click SAVE CHANGES.',
        type: 'blue',
        theme: 'modern',
        icon: 'fa fa-edit',
        boxWidth: '30%',
        useBootstrap: false,
        buttons: { ok: { text: 'OK', btnClass: 'btn-blue' } }
    });
});

// ================== DELETE CUSTOMER ==================
$(document).on("click", ".btnDelete", function(e) {
    e.preventDefault();
    let row = $(this).closest("tr");
    let id = row.find("td:eq(0)").text().trim();
    let name = row.find("td:eq(1)").text().trim() + " " + row.find("td:eq(2)").text().trim();

    $.confirm({
        title: 'Confirm Delete',
        content: 'Do you want to delete <b>' + name + '</b>?',
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
                        data: { func_name: "DeleteCustomer", customerID: id }
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

                        DisplayCustomerList();
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

$(document).ready(function () {
    DisplayCustomerList();
});

