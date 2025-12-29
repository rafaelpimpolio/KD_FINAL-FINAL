document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("kdForm");
  const tableBody = document.querySelector("#recordsTable tbody");

  const selectionError = document.createElement("div");
  selectionError.id = "selection-error";
  form.appendChild(selectionError);

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
          <td>${row.customer || ''}</td>
          <td>
            ${row.jerseySando || ''}, ${row.jerseyNeck || ''}, ${row.jerseySandoSize || ''}, 
            ${row.longsleeves || ''}, ${row.tshirt || ''}, ${row.tshirtSize || ''}, 
            ${row.poloSize || ''}, ${row.others || ''}
          </td>
          <td>
            ${row.jerseyShort || ''}, ${row.shortSize || ''}, ${row.joggingPants || ''}
          </td>
          <td>
            ${row.warmer || ''}, ${row.sublimationDTF || ''}, ${row.otherService || ''}
          </td>
          <td>${row.colorSelection || ''}</td>
          <td>
            <button class="btn-edit" onclick="editRecord(${row.id})">Edit</button>
            <button class="btn-delete" onclick="deleteRecord(${row.id})">Delete</button>
          </td>
        `;
        tableBody.appendChild(tr);
      });
    });
  }

  function validateForm() {
    let hasSelection = false;

    // Reset previous errors
    selectionError.textContent = "";
    form.querySelectorAll("select").forEach(s => s.classList.remove("error"));
    form.querySelectorAll(".color-selection label").forEach(l => l.classList.remove("error"));

    // Check selects
    form.querySelectorAll("select").forEach(select => {
      if (select.value) hasSelection = true;
      else select.classList.add("error");
    });

    // Check colors
    const colorChecked = Array.from(form.querySelectorAll('input[name="colorSelection[]"]')).some(cb => cb.checked);
    if (colorChecked) hasSelection = true;
    else form.querySelectorAll(".color-selection label").forEach(l => l.classList.add("error"));

    if (!hasSelection) {
      selectionError.textContent = "You must select at least one option!";
      return false;
    }

    return true;
  }

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    if (!validateForm()) return;

    const formData = new FormData(form);
    formData.append(
      "func_name",
      formData.get("id") ? "UpdateRecord" : "AddRecord"
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
      form.querySelectorAll("select").forEach(s => s.classList.remove("selected-option", "error"));
      form.querySelectorAll(".color-selection label").forEach(l => l.classList.remove("error"));
      selectionError.textContent = "";
      loadRecords();
    });
  });

  window.deleteRecord = function (id) {
    if (confirm("Delete this record?")) {
      fetch("crud.php", {
        method: "POST",
        body: new URLSearchParams({ func_name: "DeleteRecord", id })
      })
      .then(res => res.json())
      .then(data => {
        alert(data.message);
        loadRecords();
      });
    }
  };

  window.editRecord = function (id) {
    fetch("crud.php", {
      method: "POST",
      body: new URLSearchParams({ func_name: "GetRecord", id })
    })
    .then(res => res.json())
    .then(row => {
      document.getElementById("recordId").value = row.id;
      document.getElementById("customer").value = row.customer || '';

      const selects = ["jerseySando","jerseyNeck","jerseySandoSize","longsleeves","tshirt","tshirtSize","poloSize","others","jerseyShort","shortSize","joggingPants","warmer","sublimationDTF","otherService"];
      selects.forEach(id => {
        const s = document.getElementById(id);
        s.value = row[id] || '';
        if(s.value) s.classList.add("selected-option");
        else s.classList.remove("selected-option");
      });

      document.querySelectorAll('input[name="colorSelection[]"]').forEach(cb => cb.checked = false);
      if (row.colorSelection) {
        row.colorSelection.split(",").forEach(color => {
          const checkbox = document.querySelector(`input[name="colorSelection[]"][value="${color.trim()}"]`);
          if (checkbox) checkbox.checked = true;
        });
      }

      window.scrollTo({ top: 0, behavior: "smooth" });
    });
  };

  form.querySelectorAll("select").forEach(select => {
    select.addEventListener("change", () => {
      if(select.value) select.classList.add("selected-option");
      else select.classList.remove("selected-option");
    });
  });

  loadRecords();
});
