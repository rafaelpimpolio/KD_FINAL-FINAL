// employee.js

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

// --- Redirect to employee form ---
const goToEmployeeForm = document.getElementById("goToEmployeeForm");
if (goToEmployeeForm) {
    goToEmployeeForm.addEventListener("click", () => {
        window.location.href = "./Magan_Form/employee.html";
    });
}

// --- Login form redirect ---
const loginForm = document.querySelector("#login form");
if (loginForm) {
    loginForm.addEventListener("submit", (e) => {
        e.preventDefault();
        // Redirect to employee dashboard after login
        window.location.href = "./employee/dashboard.html";
    });
}

// --- Fetch and display all customer inquiries ---
const tableBody = document.querySelector("#recordsTable tbody");
if (tableBody) {
    // Fetch all inquiries from backend
    fetch("./php/getAllInquiries.php") // PHP returns all inquiries
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
                    <td>
                        <button class="btn btn-success btn-sm">Update</button>
                        <button class="btn btn-danger btn-sm">Delete</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch(err => console.error("Error fetching inquiries:", err));
}
