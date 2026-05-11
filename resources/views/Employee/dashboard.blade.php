<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Employee Dashboard</title>
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
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #34495e;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
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
                <a href="{{ route('employee.reports.create') }}"><i class="fa fa-plus"></i> Submit Report</a>
                <a href="{{ route('employee.profile') }}"><i class="fa fa-id-card"></i> My Profile</a>
                <a href="{{ route('employee.edit-profile') }}"><i class="fa fa-edit"></i> Edit Profile</a>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">

                <!-- Top Bar -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Dashboard</h2>
                    <div>
                        <strong>{{ auth()->user()->full_name ?? auth()->user()->email }}</strong>
                        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger ms-2">Logout</button>
                        </form>
                    </div>
                </div>

                <!-- Messages -->
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <!-- Stats Cards -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="card shadow p-3 text-center">
                            <i class="fa fa-check-circle fa-2x text-success"></i>
                            <h4 class="mt-2">{{ $completedTasks ?? 0 }}</h4>
                            <p>Completed Reports</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow p-3 text-center">
                            <i class="fa fa-clock fa-2x text-warning"></i>
                            <h4 class="mt-2">{{ $pendingTasks ?? 0 }}</h4>
                            <p>Pending Reports</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow p-3 text-center">
                            <i class="fa fa-tasks fa-2x text-primary"></i>
                            <h4 class="mt-2">{{ $totalTasks ?? 0 }}</h4>
                            <p>Total Reports</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Reports Table -->
                <div class="card shadow mt-4 p-3">
                    <h5>Recent Reports</h5>
                    <table class="table table-striped mt-3">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTasks ?? [] as $task)
                            <tr>
                                <td>{{ $task->title }}</td>
                                <td>{{ $task->description }}</td>
                                <td>
                                    @if($task->status == 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($task->status == 'approved')
                                    <span class="badge bg-success">Approved</span>
                                    @else($task->status == 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>{{ $task->created_at->format('d/m/Y') ?? 'N/A' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No tasks found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>