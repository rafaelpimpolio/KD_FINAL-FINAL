document.addEventListener("DOMContentLoaded", function () {

    const tabButtons = document.querySelectorAll(".tab-btn");
    const tabContents = document.querySelectorAll(".tab-content");

    tabButtons.forEach(button => {
        button.addEventListener("click", function () {
            const targetTab = this.dataset.tab;

            tabButtons.forEach(btn => btn.classList.remove("active"));
            tabContents.forEach(content => content.classList.remove("active"));

            this.classList.add("active");
            document.getElementById(targetTab).classList.add("active");
        });
    });

    const inquiriesTableBody = document.querySelector("#inquiriesTable tbody");
    const inquiryForm = document.getElementById("kdForm");
    const inquiryNotification = document.getElementById("inquiryNotification");
    let inquiryId = 1;

    inquiryForm.addEventListener("submit", function (e) {
        e.preventDefault();

        const customerDesign = document.getElementById("customer").value;

        if (!customerDesign) {
            alert("Please fill the design field.");
            return;
        }

        const newRow = document.createElement("tr");
        newRow.innerHTML = `
            <td>${inquiryId}</td>
            <td>${customerDesign}</td>
            <td>Details submitted</td>
            <td>Pending</td>
            <td>${new Date().toLocaleString()}</td>
        `;
        inquiriesTableBody.appendChild(newRow);

        inquiryNotification.style.display = "block";
        inquiryNotification.textContent = "Inquiry submitted successfully!";

        inquiryForm.reset();
        inquiryId++;

        document.querySelector(".tab-btn[data-tab='view-inquiries']").click();
    });

});
