<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payout Slip - {{ $payout->month }} {{ $payout->year }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 13px; color: #333; padding: 30px; }
        .header { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 20px 25px; border-radius: 6px; margin-bottom: 24px; }
        .header h2 { font-size: 20px; font-weight: bold; }
        .header p { font-size: 12px; opacity: 0.9; margin-top: 4px; }
        .section { margin-bottom: 20px; }
        .section-title { font-size: 12px; font-weight: bold; text-transform: uppercase; color: #764ba2; border-bottom: 1px solid #764ba2; padding-bottom: 4px; margin-bottom: 12px; letter-spacing: 0.5px; }
        .info-grid { display: table; width: 100%; }
        .info-row { display: table-row; }
        .info-label { display: table-cell; width: 40%; font-weight: bold; color: #555; padding: 5px 0; }
        .info-value { display: table-cell; padding: 5px 0; }
        table.breakdown { width: 100%; border-collapse: collapse; }
        table.breakdown th { background: #f0eeff; color: #555; font-size: 12px; text-align: left; padding: 8px 10px; border: 1px solid #ddd; }
        table.breakdown td { padding: 8px 10px; border: 1px solid #ddd; }
        table.breakdown tr.net-row td { background: #667eea; color: white; font-weight: bold; font-size: 14px; }
        .footer { margin-top: 30px; text-align: center; font-size: 11px; color: #888; border-top: 1px solid #eee; padding-top: 12px; }
        .badge-active { background: #28a745; color: white; padding: 2px 8px; border-radius: 3px; font-size: 11px; }
    </style>
</head>
<body>

    <div class="header">
        <h2>Pal Finsarv — Agent Payout Slip</h2>
        <p>{{ $payout->month }} {{ $payout->year }} &nbsp;|&nbsp; Generated: {{ now()->format('d M Y') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Agent Details</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Agent Name</div>
                <div class="info-value">{{ $payout->agent->user->name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Employee Code</div>
                <div class="info-value">{{ $payout->agent->employee_code }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Phone</div>
                <div class="info-value">{{ $payout->agent->phone }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $payout->agent->user->email ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Payout Period</div>
                <div class="info-value">{{ $payout->month }} {{ $payout->year }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Commission Breakdown</div>
        <table class="breakdown">
            <tr>
                <th style="width:60%">Description</th>
                <th style="width:40%">Amount</th>
            </tr>
            <tr>
                <td>Policies Sold</td>
                <td>{{ $payout->total_policies }}</td>
            </tr>
            <tr>
                <td>Total Amount</td>
                <td>₹{{ number_format($payout->total_amount, 2) }}</td>
            </tr>
            <tr>
                <td>Commission Earned</td>
                <td>₹{{ number_format($payout->commission, 2) }}</td>
            </tr>
            <tr>
                <td>Deductions</td>
                <td>- ₹{{ number_format($payout->deductions, 2) }}</td>
            </tr>
            <tr class="net-row">
                <td>Net Payout</td>
                <td>₹{{ number_format($payout->net_amount, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        This is a system-generated payout slip. For queries, please contact the admin.<br>
        Slip ID: #{{ $payout->id }} &nbsp;|&nbsp; Pal Finsarv Digital System
    </div>

</body>
</html>
