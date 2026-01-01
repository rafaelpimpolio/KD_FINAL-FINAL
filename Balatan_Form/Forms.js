document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("kdForm");
  const tableBody = document.querySelector("#recordsTable tbody");

  const dropdownIds = [
    "jerseySando",
    "jerseyNeck",
    "tshirt",
    "poloSize",
    "others",
    "jerseyShort",
    "sublimationDTF",
    "otherService"
  ];

  function loadRecords() {
    fetch("crud.php", {
      method: "POST",
      body: new URLSearchParams({ func_name: "DisplayRecord" })
    })
      .then(res => res.json())
      .then(data => {
        tableBody.innerHTML = "";
        data.forEach(row => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
            <td>${row.customer || ""}</td>
            <td>${row.jerseySando || ""}, ${row.jerseyNeck || ""}, ${row.tshirt || ""}</td>
            <td>${row.jerseyShort || ""}</td>
            <td>${row.otherService || ""}</td>
            <td>${row.colorSelection || ""}</td>
            <td>${row.materialType || ""}</td>
            <td>
              <button class="btn-edit" onclick="editRecord(${row.id})">Edit</button>
              <button class="btn-delete" onclick="deleteRecord(${row.id})">Delete</button>
            </td>
          `;
          tableBody.appendChild(tr);
        });
      });
  }

  function validateDropdowns() {
    let selectedCount = 0;

    dropdownIds.forEach(id => {
      const el = document.getElementById(id);
      if (el) el.classList.remove("input-error");
    });
   
    dropdownIds.forEach(id => {
      const el = document.getElementById(id);
      if (el && el.value !== "") {
        selectedCount++;
      }
    });

    if (selectedCount === 0) {
      dropdownIds.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.classList.add("input-error");
      });
      return false;
    }
    return true;
  }

  function setupDropdownCheckmarks() {
    dropdownIds.forEach(id => {
      const selectEl = document.getElementById(id);
      if (selectEl) {
        if (selectEl.value !== "") selectEl.classList.add("selected-option");

        selectEl.addEventListener("change", () => {
          if (selectEl.value !== "") {
            selectEl.classList.add("selected-option");
          } else {
            selectEl.classList.remove("selected-option");
          }
        });
      }
    });
  }

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    if (!validateDropdowns()) {
      alert("Please select at least ONE option from any dropdown.");
      return;
    }

    const formData = new FormData(form);
    formData.append(
      "func_name",
      document.getElementById("recordId").value ? "UpdateRecord" : "AddRecord"
    );

    fetch("crud.php", {
      method: "POST",
      body: formData
    })
      .then(res => res.json())
      .then(data => {
        alert(data.message);
        form.reset();
        document.getElementById("recordId").value = "";
        setupDropdownCheckmarks(); 
        loadRecords();
      });
  });

  window.deleteRecord = function (id) {
    if (!confirm("Delete this record?")) return;

    fetch("crud.php", {
      method: "POST",
      body: new URLSearchParams({ func_name: "DeleteRecord", id })
    })
      .then(res => res.json())
      .then(data => {
        alert(data.message);
        loadRecords();
      });
  };

  window.editRecord = function (id) {
  fetch("crud.php", {
    method: "POST",
    body: new URLSearchParams({ func_name: "GetRecord", id })
  })
    .then(res => res.json())
    .then(row => {
      document.getElementById("recordId").value = row.id;

      document.getElementById("customer").value = row.customer || "";

      dropdownIds.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
          el.value = row[id] || "";
          el.classList.toggle("selected-option", el.value !== "");
        }
      });

      document.querySelectorAll("input[type='radio']").forEach(radio => {
        radio.checked = radio.value === row[radio.name];
      });

      document
        .querySelectorAll("input[name='colorSelection[]']")
        .forEach(cb => {
          cb.checked = row.colorSelection
            ? row.colorSelection.split(",").includes(cb.value)
            : false;
        });

      document
        .querySelectorAll("input[name='materialType']")
        .forEach(radio => {
          radio.checked = radio.value === row.materialType;
        });

      window.scrollTo({ top: 0, behavior: "smooth" });
    });
};


  setupDropdownCheckmarks();
  loadRecords();
});
