<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CCMS Official Complaints Report</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #172B4D;
            line-height: 1.4;
            margin: 0;
            padding: 15px;
        }
        .header {
            border-bottom: 2px solid #172B4D;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            color: #172B4D;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .subtitle {
            font-size: 11px;
            color: #64748B;
            margin-top: 2px;
        }
        .meta {
            font-size: 9px;
            color: #64748B;
            margin-top: 5px;
        }
        .stats-grid {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .stats-grid td {
            background-color: #F8FAFC;
            border: 1px solid #E2E8F0;
            padding: 8px;
            text-align: center;
        }
        .stat-val {
            font-size: 14px;
            font-weight: bold;
            color: #172B4D;
        }
        .stat-lbl {
            font-size: 8px;
            text-transform: uppercase;
            color: #64748B;
            font-weight: bold;
            margin-top: 2px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data-table th {
            background-color: #172B4D;
            color: #FFFFFF;
            font-size: 8px;
            text-transform: uppercase;
            padding: 6px 8px;
            text-align: left;
            border: 1px solid #172B4D;
        }
        table.data-table td {
            padding: 5px 8px;
            border: 1px solid #E2E8F0;
            font-size: 9px;
        }
        table.data-table tr:nth-child(even) td {
            background-color: #F8FAFC;
        }
        .badge {
            display: inline-block;
            padding: 2px 4px;
            font-size: 8px;
            font-weight: bold;
            border-radius: 2px;
            border: 1px solid #E2E8F0;
        }
        .footer {
            margin-top: 20px;
            border-top: 1px solid #E2E8F0;
            padding-top: 8px;
            text-align: center;
            font-size: 8px;
            color: #94A3B8;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Apex Institute of Technology & Engineering</div>
        <div class="subtitle">Central College Complaint Management System &mdash; Audit & Grievance Report</div>
        <div class="meta">Generated: {{ $generatedAt }} &bull; Total Filtered Records: {{ $complaints->count() }}</div>
    </div>

    <table class="stats-grid">
        <tr>
            <td>
                <div class="stat-val">{{ $analytics['total'] }}</div>
                <div class="stat-lbl">Total Filed</div>
            </td>
            <td>
                <div class="stat-val">{{ $analytics['pending'] }}</div>
                <div class="stat-lbl">Pending Review</div>
            </td>
            <td>
                <div class="stat-val">{{ $analytics['resolved'] }}</div>
                <div class="stat-lbl">Resolved & Closed</div>
            </td>
            <td>
                <div class="stat-val">{{ $analytics['overdue'] }}</div>
                <div class="stat-lbl">SLA Overdue</div>
            </td>
            <td>
                <div class="stat-val">{{ $analytics['avg_resolution_hours'] }}h</div>
                <div class="stat-lbl">Avg Resolution</div>
            </td>
            <td>
                <div class="stat-val">{{ $analytics['verification_rejection_rate'] }}%</div>
                <div class="stat-lbl">Reject Rate</div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th>Complaint #</th>
                <th>Date</th>
                <th>Student</th>
                <th>Department</th>
                <th>Category</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Assigned Management</th>
            </tr>
        </thead>
        <tbody>
            @forelse($complaints as $c)
                <tr>
                    <td><strong>{{ $c->complaint_number }}</strong></td>
                    <td>{{ $c->created_at->format('Y-m-d') }}</td>
                    <td>{{ $c->student?->full_name }} ({{ $c->student?->register_number }})</td>
                    <td>{{ $c->department?->code }}</td>
                    <td>{{ $c->category?->name }}</td>
                    <td>{{ $c->priority }}</td>
                    <td>{{ $c->status }}</td>
                    <td>{{ $c->activeAssignment?->management?->full_name ?? 'Unassigned' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">No complaints found for the specified report parameters.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Confidential &bull; Apex College Grievance Redressal Committee &bull; Official System Generated Document
    </div>
</body>
</html>
