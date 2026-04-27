<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>

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

        table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        table th {
            background: #1e2a38;
            color: white;
        }

        table td,
        table th {
            padding: 12px;
        }

        .role-form {
            display: inline-block;
        }

        .role-select {
            width: 140px;
            padding: 5px;
        }

        .role-btn {
            padding: 5px 10px;
            background: #ffc107;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .role-btn:hover {
            background: #e0a800;
        }

        .delete-btn {
            padding: 5px 10px;
            background: #dc3545;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
        }

        .delete-btn:hover {
            background: #bb2d3b;
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Manage Users</h2>

                    <div>
                        <strong>{{ auth()->user()->full_name ?? auth()->user()->email }}</strong>

                        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger ms-2">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>

                <!-- SUCCESS -->
                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif

                <!-- ADD USER -->
                <a href="/admin/users/create" class="btn btn-success mb-3">
                    <i class="fa fa-plus"></i> Add User
                </a>

                <!-- TABLE -->
                <div class="card shadow p-3">

                    <table class="table table-striped">

                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($users as $user)
                            <tr>

                                <!-- username -->
                                <td>
                                    {{ $user['username'] ?? $user['email'] }}
                                </td>

                                <!-- email -->
                                <td>
                                    {{ $user['email'] ?? 'N/A' }}
                                </td>

                                <!-- role -->
                                <td>
                                    @php
                                    $role = $user['role'] ?? 'employee';

                                    $colors = [
                                    'admin' => 'danger',
                                    'manager' => 'warning',
                                    'employee' => 'info'
                                    ];
                                    @endphp

                                    <span class="badge bg-{{ $colors[$role] ?? 'secondary' }}">
                                        {{ ucfirst($role) }}
                                    </span>
                                </td>

                                <!-- actions -->
                                <td>

                                    <!-- DELETE -->
                                    <form method="POST"
                                        action="{{ url('/admin/users/'.$user['id']) }}"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')

                                        <button class="delete-btn">
                                            Delete
                                        </button>
                                    </form>

                                    <!-- ROLE UPDATE -->
                                    <form method="POST"
                                        action="{{ route('admin.users.updateRole', $user['id']) }}"
                                        class="role-form">

                                        @csrf

                                        <select name="role" class="role-select">
                                            <option value="admin" {{ ($user['role'] ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="manager" {{ ($user['role'] ?? '') == 'manager' ? 'selected' : '' }}>Manager</option>
                                            <option value="employee" {{ ($user['role'] ?? '') == 'employee' ? 'selected' : '' }}>Employee</option>
                                        </select>

                                        <button type="submit" class="role-btn">
                                            Change role
                                        </button>

                                    </form>

                                </td>

                            </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>
        </div>
    </div>

</body>

</html>