<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manager Reports</title>

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
                <a href="{{ route('manager.profile') }}"><i class="fa fa-id-card"></i> My Profile</a>
                <a href="{{ route('manager.reports') }}"><i class="fa fa-chart-line"></i> Reports</a>
                <a href="{{ route('manager.edit-profile') }}"><i class="fa fa-edit"></i> Edit Profile</a>
            </div>

            <!-- 🔹 Main Content -->
            <div class="col-md-10 p-4">

                <!-- Top Bar -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Employee Reports</h2>
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

                <!-- Messages -->
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <!-- 🔹 Tasks Table -->
                <div class="card shadow p-3">
                    <h5>Received Tasks</h5>

                    <table class="table table-striped mt-3 align-middle">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Action</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasks ?? [] as $task)
                            <tr>
                                <td>{{ $task->user->first_name ?? 'N/A' }} {{ $task->user->last_name ?? '' }}</td>
                                <td>{{ $task->title }}</td>
                                <td>{{ $task->description }}</td>
                                <td>
                                    @if($task->status == 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($task->status == 'approved')
                                    <span class="badge bg-success">Approved</span>
                                    @elseif($task->status == 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                    @else
                                    <span class="badge bg-secondary">{{ $task->status }}</span>
                                    @endif
                                </td>
                                
                                <td>
                                    @if($task->status == 'pending')
                                    <form method="POST" action="{{ route('manager.reports.validate', $task->id) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fa fa-check"></i> Validate
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('manager.reports.reject', $task->id) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fa fa-times"></i> Reject
                                        </button>
                                    </form>
                                    @else
                                    <span class="text-success">
                                        <i class="fa fa-check-circle"></i> Already {{ $task->status }}
                                    </span>
                                    @endif
                                </td>

                                <td>
                                    <form method="POST" action="{{ route('manager.task.destroy', $task) }}" 
                                          onsubmit="return confirm('Are you sure you want to delete this report?');" 
                                          style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fa fa-trash"></i> Delete
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>