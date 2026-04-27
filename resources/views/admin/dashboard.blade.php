<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f5f7fa;
            margin: 0;
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

        .main {
            padding: 20px;
        }

        .card-box {
            background: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .card-box i {
            font-size: 30px;
            margin-bottom: 10px;
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
            <div class="col-md-10 main">

                <!-- TOP BAR -->
                <div class="topbar">
                    <h2>Dashboard</h2>

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

                <!-- STATS -->
                <div class="row">

                    <div class="col-md-4">
                        <div class="card-box">
                            <i class="fa fa-users text-primary"></i>
                            <h4>{{ $totalUsers ?? 0 }}</h4>
                            <p>Total Users</p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card-box">
                            <i class="fa fa-user-shield text-success"></i>
                            <h4>{{ $admins ?? 0 }}</h4>
                            <p>Admins</p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card-box">
                            <i class="fa fa-user text-warning"></i>
                            <h4>{{ $employees ?? 0 }}</h4>
                            <p>Employees</p>
                        </div>
                    </div>

                </div>

                <!-- TABLE -->
                <div class="mt-4">

                    <h5>Recent Users</h5>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($recentUsers ?? [] as $user)
                            <tr>
                                <td>{{ $user['username'] ?? ($user['email'] ?? 'N/A') }}</td>
                                <td>{{ $user['email'] ?? 'N/A' }}</td>
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