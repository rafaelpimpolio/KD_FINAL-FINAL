// customer.js

// --- Tab switching ---
const tabButtons = document.querySelectorAll(".tab-btn");
const tabContents = document.querySelectorAll(".tab-content");

tabButtons.forEach(btn => {
    btn.addEventListener("click", () => {
        tabButtons.forEach(b => b.classList.remove("active"));
        tabContents.forEach(c => c.classList.remove("active"));

        btn.classList.add("active");
        document.getElementById(btn.dataset.tab).classList.add("active");
    });
});

// --- Redirect to customer form ---
const goToCustomerForm = document.getElementById("goToCustomerForm");
if (goToCustomerForm) {
    goToCustomerForm.addEventListener("click", () => {
        window.location.href = "./Pimpolio_Form/customer.html";
    });
}

// --- Login form redirect ---
const loginForm = document.querySelector("#login form");
if (loginForm) {
    loginForm.addEventListener("submit", (e) => {
        e.preventDefault();
        // Redirect to customer dashboard after login
        window.location.href = "./customer/dashboard.html";
    });
}

// --- Fetch and display customer inquiries ---
const tableBody = document.querySelector("#recordsTable tbody");
if (tableBody) {
    // Fetch customer inquiries from backend
    fetch("./php/getCustomerInquiries.php") // make sure PHP returns only this customer's inquiries
        .then(response => response.json())
        .then(data => {
            data.forEach(record => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${record.customer_comment}</td>
                    <td>${record.tops}</td>
                    <td>${record.bottoms}</td>
                    <td>${record.accessories}</td>
                    <td>${record.colors}</td>
                    <td>${record.material_type}</td>
                    <td><button class="btn btn-danger btn-sm">Delete</button></td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch(err => console.error("Error fetching customer inquiries:", err));
}
