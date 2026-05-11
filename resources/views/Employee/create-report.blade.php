<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Submit Report</title>
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
          <h2>Submit New Report</h2>
          <div>
            <strong>{{ auth()->user()->first_name ?? '' }} {{ auth()->user()->last_name ?? '' }}</strong>
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
              @csrf
              <button type="submit" class="btn btn-sm btn-danger ms-2">Logout</button>
            </form>
          </div>
        </div>

        <!-- Messages -->
        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif

        <!-- Form -->
        <div class="card shadow p-4">
          <form method="POST" action="{{ route('employee.reports.store') }}">
            @csrf

            <div class="mb-3">
              <label class="form-label">Title *</label>
              <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                value="{{ old('title') }}" required>
              @error('title')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">Report Date *</label>
              <input type="date" name="report_date" class="form-control @error('report_date') is-invalid @enderror"
                value="{{ old('report_date') }}" required>
              @error('report_date')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">Description *</label>
              <textarea name="description" rows="8" class="form-control @error('description') is-invalid @enderror"
                required>{{ old('description') }}</textarea>
              @error('description')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <button type="submit" class="btn btn-success">
              <i class="fa fa-paper-plane"></i> Submit Report
            </button>
            <a href="{{ route('employee.dashboard') }}" class="btn btn-secondary"><i class="fa fa-times"></i> Cancel
            </a>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>