document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("kdForm");

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    // Ensure at least one color is selected
    const colors = document.querySelectorAll("input[name='color[]']:checked");
    if (colors.length === 0) {
      alert("Please select at least one color.");
      return;
    }

    const formData = new FormData(form);

    fetch("submit_inquiry.php", {
      method: "POST",
      body: formData
    })
      .then(res => res.json())
      .then(data => {
        alert(data.message);
        if (data.status === "success") {
          form.reset();
        }
      })
      .catch(err => {
        console.error(err);
        alert("An error occurred while submitting the inquiry.");
      });
  });
});
