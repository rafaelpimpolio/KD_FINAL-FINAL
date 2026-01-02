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

// Go to customer registration
const goToCustomerForm = document.getElementById("goToCustomerForm");
if (goToCustomerForm) {
    goToCustomerForm.addEventListener("click", () => {
        // Make sure the path is correct relative to this file
        window.location.href = "Pimpolio_Form/customer.html";
    });
}


const loginForm = document.querySelector("#login form");
if (loginForm) {
    loginForm.addEventListener("submit", (e) => {
        e.preventDefault(); // Prevents the page from refreshing
        
        // Redirect to Inquiry.html after clicking Login
         window.location.href = "./customer/customers.html";
    
    });
}