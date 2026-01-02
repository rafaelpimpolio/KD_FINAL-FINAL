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
