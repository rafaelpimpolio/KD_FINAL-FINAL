// Database state (persists in LocalStorage)
let users = JSON.parse(localStorage.getItem('crud_users')) || [];
const form = document.getElementById('userForm');
const userTableBody = document.getElementById('userTableBody');

// Initial load of users into the list
renderTable();

form.addEventListener('submit', function (e) {
  e.preventDefault();
  clearErrors();

  const id = document.getElementById('userId').value;
  const username = document.getElementById('username').value.trim();
  const phone = document.getElementById('phone').value.trim();
  const password = document.getElementById('password').value;

  // Simple Validation
  let valid = true;
  if (username.length < 3) { showError('usernameError', 'Minimum 3 characters'); valid = false; }
  if (!/^09\d{9}$/.test(phone)) { showError('phoneError', 'Must be 11 digits starting with 09'); valid = false; }

  if (valid) {
    // 1. AUTOMATED STATUS: Active if password is provided
    const autoStatus = password.length > 0 ? 'Active' : 'Inactive';

    if (id) {
      // UPDATE: Find existing user and update details
      const index = users.findIndex(u => u.id === id);
      users[index] = { ...users[index], username, phone, status: autoStatus };
    } else {
      // CREATE: Generate new user with automated ID and Date
      const newUser = {
        id: Math.floor(1000 + Math.random() * 9000).toString(), // Randomized 4-digit ID
        username,
        phone,
        status: autoStatus,
        createdAt: new Date().toLocaleString('en-US', { dateStyle: 'medium', timeStyle: 'short' })
      };
      users.push(newUser);
    }
    
    saveToStorage();
    renderTable(); // This shows the user on the list immediately
    resetForm();
  }
});

// Function to refresh the visual table
function renderTable() {
  userTableBody.innerHTML = '';
  
  if (users.length === 0) {
    userTableBody.innerHTML = '<tr><td colspan="6" style="text-align:center">No users found.</td></tr>';
    return;
  }

  users.forEach(user => {
    const statusClass = user.status === 'Active' ? 'status-active' : 'status-inactive';
    const row = `
      <tr>
        <td>#${user.id}</td>
        <td>${user.username}</td>
        <td>${user.phone}</td>
        <td><span class="status-pill ${statusClass}">${user.status}</span></td>
        <td>${user.createdAt}</td>
        <td>
          <button class="btn-edit" onclick="editUser('${user.id}')">Edit</button>
          <button class="btn-delete" onclick="deleteUser('${user.id}')">Delete</button>
        </td>
      </tr>
    `;
    userTableBody.innerHTML += row;
  });
}

function editUser(id) {
  const user = users.find(u => u.id === id);
  document.getElementById('userId').value = user.id;
  document.getElementById('username').value = user.username;
  document.getElementById('phone').value = user.phone;
  document.getElementById('submitBtn').textContent = "Update User";
}

function deleteUser(id) {
  if (confirm("Delete this user?")) {
    users = users.filter(u => u.id !== id);
    saveToStorage();
    renderTable();
  }
}

function saveToStorage() {
  localStorage.setItem('crud_users', JSON.stringify(users));
}

function resetForm() {
  form.reset();
  document.getElementById('userId').value = '';
  document.getElementById('submitBtn').textContent = "Create User";
}

function showError(id, msg) { document.getElementById(id).textContent = msg; }
function clearErrors() { document.querySelectorAll('.error').forEach(e => e.textContent = ''); }