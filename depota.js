// Tabs
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

// Login redirect logic
const loginForm = document.getElementById("loginForm");

if (loginForm) {
    loginForm.addEventListener("submit", (e) => {
        e.preventDefault(); // stop reload

        const role = document.getElementById("login-role").value;

        if (!role) {
            alert("Please select a role");
            return;
        }

        if (role === "customer") {
            window.location.href = "customer.html";
        } else if (role === "employee") {
            window.location.href = "employee.html";
        }
    });
}

// Sign-up redirect
const goToCustomerForm = document.getElementById("goToCustomerForm");
if (goToCustomerForm) {
    goToCustomerForm.addEventListener("click", () => {
        window.location.href = "./Pimpolio_Form/customer.html";
    });
}
