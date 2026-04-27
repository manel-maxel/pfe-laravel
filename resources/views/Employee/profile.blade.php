<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Profile - Employee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: #34495e;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">

            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-3">
                <h4>EMPLOYEE</h4>
                <hr>
                <a href="{{ route('employee.dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                <a href="{{ route('employee.profile') }}"><i class="fa fa-id-card"></i> My Profile</a>
                <a href="{{ route('employee.reports.create') }}"><i class="fa fa-plus"></i> Submit Report</a>
                <a href="{{ route('employee.edit-profile') }}"><i class="fa fa-edit"></i> Edit Profile</a>
            </div>


            <!-- Main Content -->
            <div class="col-md-10 p-4">

                <!-- Top Bar -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>My Profile</h2>
                    <div>
                        <strong>{{ auth()->user()->full_name ?? auth()->user()->email }}</strong>
                        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger ms-2">Logout</button>
                        </form>
                    </div>
                </div>

                <!-- Profile Card -->
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card shadow border-0 p-4">
                            <div class="text-center mb-4">
                                <div class="profile-avatar mx-auto">
                                    {{ strtoupper(substr(auth()->user()->full_name ?? auth()->user()->email, 0, 1)) }}
                                </div>
                                <h3 class="mt-3">{{ auth()->user()->full_name }}</h3>
                                <span class="badge bg-info">{{ auth()->user()->role ?? 'employee' }}</span>
                            </div>

                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td class="text-muted fw-bold" style="width:150px">
                                            <i class="fa fa-user me-2"></i> Full Name
                                        </td>
                                        <td>{{ auth()->user()->full_name  }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-bold">
                                            <i class="fa fa-envelope me-2"></i> Email Address
                                        </td>
                                        <td>{{ auth()->user()->email }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-bold">
                                            <i class="fa fa-shield-alt me-2"></i> Role
                                        </td>
                                        <td><span class="badge bg-info">{{ auth()->user()->role  }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-bold">
                                            <i class="fa fa-key me-2"></i> Keycloak ID
                                        </td>
                                        <td>
                                            <code class="text-muted" style="font-size:0.85rem">
                                                {{ auth()->user()->keycloak_id  }}
                                            </code>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-bold">
                                            <i class="fa fa-calendar me-2"></i> Member Since
                                        </td>
                                        <td>{{ auth()->user()->created_at}}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="text-center mt-3">
                                <a href="{{ route('employee.edit-profile') }}" class="btn btn-warning">
                                    <i class="fa fa-edit me-2"></i> Edit Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>