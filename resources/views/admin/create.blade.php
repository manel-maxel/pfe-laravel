<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Create User</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f5f7fa;
        }

        .sidebar {
            min-height: 100vh;
            position: sticky;
            top: 0;
            background: #2c3e50;
            color: white;
        }

        .sidebar a {
            color: #ccc;
            display: block;
            padding: 10px;
            text-decoration: none;
        }

        .sidebar a:hover {
            background: #34495e;
            color: white;
            border-left: 3px solid #3498db;
        }

        .card {
            border-radius: 15px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <div class="container-fluid">
        <div class="row">

            <!-- SIDEBAR -->
            <div class="col-md-2 sidebar p-3">
                <h4>ADMIN</h4>
                <hr>
                <a href="{{ route('admin.dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                <a href="{{ route('admin.users') }}"><i class="fa fa-users"></i> Manage Users</a>
                <a href="{{ route('admin.profile') }}"><i class="fa fa-id-card"></i> My Profile</a>
                <a href="{{ route('admin.edit-profile') }}"><i class="fa fa-edit"></i> Edit Profile</a>
            </div>

            <!-- MAIN -->
            <div class="col-md-10 p-4">

                <!-- TOP BAR -->
                <div class="topbar">
                    <h2>Create New User</h2>

                    <div>
                        <strong>{{ auth()->user()->full_name ?? auth()->user()->email }}</strong>

                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button class="btn btn-danger btn-sm ms-2">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>

                <!-- FORM -->
                <div class="card shadow p-4">

                    <form method="POST" action="{{ url('/admin/users/store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="employee">Employee</option>
                                <option value="manager">Manager</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success">
                            Create User
                        </button>

                        <a href="/admin/users" class="btn btn-secondary">
                            Cancel
                        </a>

                    </form>

                </div>

            </div>
        </div>
    </div>

</body>

</html>