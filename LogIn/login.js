const tabButtons = document.querySelectorAll(".tab-btn");
const tabContents = document.querySelectorAll(".tab-content");

tabButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
        tabButtons.forEach((b) => b.classList.remove("active"));
        tabContents.forEach((c) => c.classList.remove("active"));

        btn.classList.add("active");
        document.getElementById(btn.dataset.tab).classList.add("active");
    });
});

const signupForm = document.getElementById("signupForm");
const signupNotification = document.getElementById("signupNotification");

signupForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(signupForm);

    fetch("crud.php", {
        method: "POST",
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                signupNotification.style.color = "green";
                signupNotification.textContent = "Account created successfully!";
                signupNotification.style.display = "block";
                signupForm.reset();
            } else {
                signupNotification.style.color = "red";
                signupNotification.textContent = "Error: " + (data.message || "Signup failed");
                signupNotification.style.display = "block";
            }
        })
        .catch(() => {
            signupNotification.style.color = "red";
            signupNotification.textContent = "An unexpected error occurred. Please try again.";
            signupNotification.style.display = "block";
        });
});

