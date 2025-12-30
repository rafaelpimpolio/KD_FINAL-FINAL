document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("kdForm");
  const tableBody = document.querySelector("#recordsTable tbody");

  const selectionError = document.createElement("div");
  selectionError.id = "selection-error";
  selectionError.style.color = "red";
  selectionError.style.marginTop = "6px";
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
          <td>${row.materialType || ''}</td>
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
    let isValid = true;
    selectionError.textContent = "";

    form.querySelectorAll("select").forEach(s => s.classList.remove("error"));
    form.querySelectorAll(".color-selection label, .material-checkbox").forEach(l => l.classList.remove("error"));
    form.querySelectorAll(".radio-group input").forEach(r => r.classList.remove("error"));

    form.querySelectorAll("select").forEach(select => {
      if (!select.value) {
        select.classList.add("error");
        isValid = false;
      }
    });

    const radioGroups = [
      "jerseySandoSize", "longsleeves", "tshirtSize",
      "shortSize", "joggingPants", "warmer"
    ];

    radioGroups.forEach(name => {
      const checked = form.querySelector(`input[name="${name}"]:checked`);
      if (!checked) {
        document.querySelectorAll(`input[name="${name}"]`).forEach(r => r.classList.add("error"));
        isValid = false;
      }
    });

    const colorChecked = Array.from(form.querySelectorAll('input[name="colorSelection[]"]')).some(cb => cb.checked);
    if (!colorChecked) {
      form.querySelectorAll('input[name="colorSelection[]"]').forEach(cb => {
        cb.parentElement.classList.add("error");
      });
      isValid = false;
    }

    const materialChecked = Array.from(form.querySelectorAll('input[name="materialType[]"]')).some(cb => cb.checked);
    if (!materialChecked) {
      form.querySelectorAll('input[name="materialType[]"]').forEach(cb => {
        cb.parentElement.classList.add("error");
      });
      isValid = false;
    }

    if (!isValid) selectionError.textContent = "Please complete all required selections!";

    return isValid;
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
      selectionError.textContent = "";
      document.querySelectorAll("select").forEach(s => s.classList.remove("selected"));
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

      const selects = [
        "jerseySando","jerseyNeck","tshirt","poloSize","others",
        "jerseyShort","sublimationDTF","otherService"
      ];

      selects.forEach(id => {
        const s = document.getElementById(id);
        if (s) {
          s.value = row[id] || '';
          if (s.value) s.classList.add("selected");
        }
      });

      const radioGroups = [
        "jerseySandoSize", "longsleeves", "tshirtSize",
        "shortSize", "joggingPants", "warmer"
      ];

      radioGroups.forEach(name => {
        document.querySelectorAll(`input[name="${name}"]`).forEach(radio => {
          radio.checked = radio.value === row[name];
        });
      });

      document.querySelectorAll('input[name="colorSelection[]"]').forEach(cb => cb.checked = false);
      if (row.colorSelection) {
        row.colorSelection.split(",").forEach(color => {
          const checkbox = document.querySelector(`input[name="colorSelection[]"][value="${color.trim()}"]`);
          if (checkbox) checkbox.checked = true;
        });
      }

      document.querySelectorAll('input[name="materialType[]"]').forEach(cb => cb.checked = false);
      if (row.materialType) {
        row.materialType.split(",").forEach(material => {
          const checkbox = document.querySelector(`input[name="materialType[]"][value="${material.trim()}"]`);
          if (checkbox) checkbox.checked = true;
        });
      }

      window.scrollTo({ top: 0, behavior: "smooth" });
    });
  };

  document.querySelectorAll("select").forEach(select => {
    select.classList.add("select-with-check");
    select.addEventListener("change", () => {
      if (select.value) select.classList.add("selected");
      else select.classList.remove("selected");
    });
  });

  loadRecords();
});
