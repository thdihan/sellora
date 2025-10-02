<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Presentations Export</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .header-info {
            margin-bottom: 20px;
        }
        .export-date {
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header-info">
        <h1>Presentations Export Report</h1>
        <p class="export-date">Generated on: {{ date('F j, Y \\a\\t g:i A') }}</p>
        <p>Total Presentations: {{ count($presentations) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Category</th>
                <th>Status</th>
                <th>Views</th>
                <th>Downloads</th>
                <th>Created By</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($presentations as $presentation)
            <tr>
                <td>{{ $presentation->id }}</td>
                <td>{{ $presentation->title }}</td>
                <td>{{ $presentation->category }}</td>
                <td>{{ ucfirst($presentation->status) }}</td>
                <td>{{ $presentation->view_count ?? 0 }}</td>
                <td>{{ $presentation->download_count ?? 0 }}</td>
                <td>{{ $presentation->user->name }}</td>
                <td>{{ $presentation->created_at->format('Y-m-d H:i:s') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
        <p><strong>Summary Statistics:</strong></p>
        <ul>
            <li>Total Views: {{ $presentations->sum('view_count') }}</li>
            <li>Total Downloads: {{ $presentations->sum('download_count') }}</li>
            <li>Most Popular Category: {{ $presentations->groupBy('category')->map->count()->sortDesc()->keys()->first() ?? 'N/A' }}</li>
        </ul>
    </div>
</body>
</html>