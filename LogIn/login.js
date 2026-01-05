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

    const loginForm = document.querySelector("#login form");

    loginForm.addEventListener("submit", function (e) {
        e.preventDefault();

        const role = this.querySelector("select[name='role']").value;
        const username = this.querySelector("input[name='username']").value;
        const password = this.querySelector("input[name='password']").value;

        if (!role || !username || !password) {
            alert("Please fill all fields.");
            return;
        }

        if (role === "customer") {
            window.location.href = "CustomerDashboard/customer_dashboard.html";
        } else if (role === "employee") {
            window.location.href = "EmployeeDashboard/employee_dashboard.html";
        } else {
            alert("Invalid role selected.");
        }
    });

    const signupForm = document.getElementById("signupForm");

    signupForm.addEventListener("submit", function (e) {
        e.preventDefault();

        const inputs = this.querySelectorAll("input");
        let allFilled = true;

        inputs.forEach(input => {
            if (!input.value) {
                allFilled = false;
            }
        });

        if (!allFilled) {
            alert("Please fill all fields in the signup form.");
            return;
        }

        const notification = document.getElementById("signupNotification");
        notification.style.display = "block";
        notification.textContent = "Account created successfully! You can now log in.";

        this.reset();
        document.querySelector(".tab-btn[data-tab='login']").click();
    });

});
