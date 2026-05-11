<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Profile - MANAGER</title>
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
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">

            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-3">
                <h4>MANAGER</h4>
                <hr>
                <a href="{{ route('manager.dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                <a href="{{ route('manager.reports') }}"><i class="fa fa-chart-line"></i> Reports</a>
                <a href="{{ route('manager.profile') }}"><i class="fa fa-id-card"></i> My Profile</a>
                <a href="{{ route('manager.edit-profile') }}"><i class="fa fa-edit"></i> Edit Profile</a>
            </div>


            <!-- Main Content -->
            <div class="col-md-10 p-4">

                <!-- Top Bar -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Edit Profile</h2>
                    <div>
                        <strong>{{ auth()->user()->full_name ?? auth()->user()->email }}</strong>
                        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger ms-2">Logout</button>
                        </form>
                    </div>
                </div>

                <div class="card shadow p-4">
                    <form method="POST" action="{{ route('manager.profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control"
                                value="{{ old('first_name', auth()->user()->first_name ?? '') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control"
                                value="{{ old('last_name', auth()->user()->last_name ?? '') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                value="{{ old('email', auth()->user()->email) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-control bg-light"
                                value="{{ auth()->user()->role ?? 'manager' }}" disabled>
                            <small class="text-muted">Your role can only be changed by an admin.</small>
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-save"></i> Save Changes
                        </button>
                        <a href="{{ route('manager.profile') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>