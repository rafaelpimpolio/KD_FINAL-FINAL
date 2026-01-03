$(document).ready(function () {

    // ================== LOAD CUSTOMER LIST ==================
    function DisplayCustomerList() {
        $.ajax({
            type: "POST",
            url: "crud.php",
            data: { func_name: "DisplayCustomer" },
            success: function (res) {
                let response = JSON.parse(res);

                if (!response.success) {
                    $.alert(response.message);
                    return;
                }

                let tbody = $("#customerTable tbody").empty();
                response.data.forEach(c => {
                    tbody.append(`
                        <tr>
                            <td>${c.customer_id}</td>
                            <td>${c.first_name}</td>
                            <td>${c.last_name}</td>
                            <td>${c.phone_number}</td>
                            <td>${c.email}</td>
                            <td>${c.barangay}</td>
                            <td>${c.city_municipality}</td>
                            <td>${c.province}</td>
                            <td>${c.postal_code}</td>
                            <td>
                                <button class="btn btn-warning btn-sm btnEdit">EDIT</button>
                                <button class="btn btn-danger btn-sm btnDelete">DELETE</button>
                            </td>
                        </tr>
                    `);
                });
            },
            error: function (xhr, status, error) {
                console.error("Error loading customers:", error);
            }
        });
    }

    // Initial load
    DisplayCustomerList();

    // ================== SAVE / UPDATE ==================
    $("#customerForm").on("submit", function (e) {
        e.preventDefault();

        let form = this;

        if (!form.checkValidity()) {
            form.classList.add("was-validated");
            return;
        }

        let formData = new FormData(form);
        formData.append(
            "func_name",
            $("#btnSaveCustomer").text() === "SAVE CHANGES"
                ? "UpdateCustomer"
                : "AddCustomer"
        );

        $.ajax({
            type: "POST",
            url: "crud.php",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                let response = JSON.parse(res);
                $.alert(response.message);

                // Reset form after save
                form.reset();
                $(form).removeClass("was-validated");

                // Reset button and hidden ID
                $("#btnSaveCustomer").text("CONFIRM");
                $("#customerID").val("");

                // Focus first input
                $("#firstName").focus();

                // Reload customer table
                DisplayCustomerList();
            },
            error: function (xhr, status, error) {
                console.error("Error saving customer:", error);
            }
        });
    });

    // ================== EDIT ==================
    $(document).on("click", ".btnEdit", function () {
        let td = $(this).closest("tr").find("td");

        $("#customerID").val(td.eq(0).text());
        $("#firstName").val(td.eq(1).text());
        $("#lastName").val(td.eq(2).text());
        $("#phone").val(td.eq(3).text());
        $("#email").val(td.eq(4).text());
        $("#barangay").val(td.eq(5).text());
        $("#city").val(td.eq(6).text());
        $("#province").val(td.eq(7).text());
        $("#postalCode").val(td.eq(8).text());

        $("#btnSaveCustomer").text("SAVE CHANGES");
    });

    // ================== DELETE ==================
    $(document).on("click", ".btnDelete", function () {
        let row = $(this).closest("tr");
        let id = row.find("td:eq(0)").text();
        let name = row.find("td:eq(1)").text() + " " + row.find("td:eq(2)").text();

        $.confirm({
            title: "Confirm Delete",
            content: `Delete <b>${name}</b>?`,
            buttons: {
                Yes: function () {
                    $.post("crud.php", { func_name: "DeleteCustomer", customerID: id }, function (res) {
                        let response = JSON.parse(res);
                        $.alert(response.message);
                        DisplayCustomerList();
                    });
                },
                No: function () { }
            }
        });
    });

});
