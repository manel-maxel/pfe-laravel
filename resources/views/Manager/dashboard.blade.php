<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manager Dashboard</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f5f7fa;
        }

        .sidebar {
            height: 100vh;
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
    </style>
</head>

<body>

    <div class="container-fluid">
        <div class="row">

            <!-- 🔹 Sidebar -->
            <div class="col-md-2 sidebar p-3">
                <h4>MANAGER</h4>
                <hr>

                <a href="{{ route('manager.dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                <a href="{{ route('manager.reports') }}"><i class="fa fa-chart-line"></i> Reports</a>
                <a href="{{ route('manager.profile') }}"><i class="fa fa-id-card"></i> My Profile</a>
                <a href="{{ route('manager.edit-profile') }}"><i class="fa fa-edit"></i> Edit Profile</a>
            </div>

            <!-- 🔹 Main Content -->
            <div class="col-md-10 p-4">

                <!-- Top Bar -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Dashboard</h2>
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

                <!-- 🔹 Cards -->
                <div class="row">

                    <div class="col-md-4">
                        <div class="card shadow p-3 text-center">
                            <i class="fa fa-user fa-2x text-warning"></i>
                            <h4 class="mt-2">{{ $employees ?? 0 }}</h4>
                            <p>Employees</p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow p-3 text-center">
                            <i class="fa fa-tasks fa-2x text-success"></i>
                            <h4 class="mt-2">{{ $activeTasks ?? 0 }}</h4>
                            <p>Active Reports</p>
                        </div>
                    </div>

                </div>

                <!-- 🔹 Employee Table -->
                <!-- Employee Table -->
                <div class="card shadow mt-4 p-3">
                    <h5>Employees Overview</h5>

                    <table class="table table-striped mt-3">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users ?? [] as $user)
                            <tr>
                                <!-- ✅ Pour Keycloak (tableau) -->
                                <td>{{ $user['firstName'] ?? '' }} {{ $user['lastName'] ?? '' }}</td>
                                <td>{{ $user['email'] ?? '' }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $user['role'] ?? 'employee' }}</span>
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