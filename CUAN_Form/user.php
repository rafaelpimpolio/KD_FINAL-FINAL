<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>User Forms</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>

  <div class="container">
    <form id="userForm" novalidate>
      <h2>KD Register User</h2>
      <input type="hidden" id="userId" />

      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" placeholder="Enter username" />
        <div class="error" id="usernameError"></div>
      </div>

      <div class="form-group">
        <label for="phone">Phone Number</label>
        <input type="tel" id="phone" placeholder="09XXXXXXXXX" />
        <div class="error" id="phoneError"></div>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" placeholder="Active if filled, Inactive if empty" />
        <div class="error" id="passwordError"></div>
      </div>

      <button type="submit" id="submitBtn">Create User</button>
    </form>

    <div class="table-container">
      <h2>User List</h2>
      <table id="userTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Phone</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="userTableBody">
        </tbody>
      </table>
    </div>
  </div>

  <script src="crud.js"></script>
</body>
</html>