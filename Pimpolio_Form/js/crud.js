$('#customerForm').on('submit', function (e) {
    e.preventDefault();

    const form = this;

    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }

    let formData = new FormData(form);
    formData.append("func_name", "CreateCustomerAccount");

    $.ajax({
        type: "POST",
        url: "crud.php",
        data: formData,
        contentType: false,
        processData: false
    })
    .done(function (msg) {
        let message;
        try { message = JSON.parse(msg); } catch { message = msg; }

        $.alert({
            title: 'Account Created',
            content: message,
            type: 'green',
            theme: 'modern',
            buttons: {
                ok: {
                    text: 'Proceed to Login',
                    action: function () {
                        window.location.href = "../depota.html?tab=login";
                    }
                }
            }
        });

        form.reset();
        form.classList.remove('was-validated');
    })
    .fail(function (xhr) {
        $.alert({
            title: 'Error',
            content: xhr.responseText,
            type: 'red'
        });
    });
});
<<<<<<< HEAD

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

function deleteCustomer(id) {
    $.post(
        "crud.php",
        {
            func_name: "DeleteCustomer",
            customerID: id
        },
        function (res) {
            let response;

            try {
                response = JSON.parse(res);
            } catch (e) {
                alert("Invalid server response");
                return;
            }

            if (response.success) {
                $.alert("Customer deleted successfully");
                location.reload();
            } else {
                $.alert(response.error || "Delete failed");
            }
        }
    );
}


=======
>>>>>>> d8b50dd3bf4c2b3cca1e1d17df4fcfec34ebc008
