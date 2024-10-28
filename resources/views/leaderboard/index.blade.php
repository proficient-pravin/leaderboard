<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1c1c1e;
            color: #ffffff;
        }
        .table {
            color: #ffffff;
        }
        .table thead th {
            border-bottom: 1px solid #444;
        }
        .form-select, .form-control, .btn, .table {
            background-color: #2c2c2e;
            color: #ffffff;
            border: 1px solid #444;
        }
        .btn-primary, .btn-secondary {
            background-color: #444;
            border-color: #444;
        }
        .btn-primary:hover, .btn-secondary:hover {
            background-color: #666;
            border-color: #666;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Leaderboard</h1>
        
      <!-- Filter and Search Form -->
        <form action="{{ route('leaderboard.index') }}" method="GET" class="d-flex justify-content-between mb-4">
            <div class="d-flex align-items-center">
                <select name="filter" class="form-select me-2">
                    <option value="day" {{ request('filter') === 'day' ? 'selected' : '' }}>Today</option>
                    <option value="month" {{ request('filter') === 'month' ? 'selected' : '' }}>This Month</option>
                    <option value="year" {{ request('filter') === 'year' ? 'selected' : '' }}>This Year</option>
                </select>
                <input type="text" name="user_id" placeholder="Search by User ID" class="form-control me-2" value="{{ old('user_id', request('user_id')) }}">
                <button type="submit" class="btn btn-primary">Filter/Search</button>
            </div>
            <button type="button" onclick="recalculateRanks()" class="btn btn-secondary">Recalculate</button>
        </form>

        <!-- Leaderboard Table -->
        <table class="table table-dark table-hover">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>User ID</th>
                    <th>Full Name</th>
                    <th>Total Points</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>
                            @if(request('filter') === 'day')
                            {{ $user->daily_rank ?? 1 }}
                        @elseif(request('filter') === 'month')
                            {{ $user->monthly_rank ?? 1 }}
                        @elseif(request('filter') === 'year')
                            {{ $user->yearly_rank ?? 1 }}
                        @else 
                            {{ $user->daily_rank ?? 1 }}
                        @endif
                        </td>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->total_points }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Recalculate Button Script -->
    <script>
        function recalculateRanks() {
            fetch("{{ route('leaderboard.recalculate') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert("Failed to recalculate ranks.");
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("An error occurred while recalculating ranks.");
            });
        }
    </script>
</body>
</html>
