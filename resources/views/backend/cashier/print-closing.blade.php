<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('modules.cashier.closingSlip') }} - Session #{{ $session->session_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            padding: 20px;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #dc2626;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #991b1b;
            font-size: 28px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header .subtitle {
            color: #64748b;
            font-size: 14px;
        }
        .restaurant-info {
            text-align: center;
            margin-bottom: 30px;
        }
        .restaurant-name {
            font-size: 22px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 5px;
        }
        .branch-name {
            color: #64748b;
            font-size: 16px;
        }
        .session-info {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }
        .info-item {
            display: flex;
            flex-direction: column;
        }
        .info-label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 15px;
            font-weight: 600;
            color: #1e293b;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #dc2626;
            color: white;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        .stat-card {
            padding: 15px;
            border-radius: 8px;
            border: 2px solid;
        }
        .stat-card.blue {
            background: #dbeafe;
            border-color: #3b82f6;
        }
        .stat-card.green {
            background: #dcfce7;
            border-color: #22c55e;
        }
        .stat-card.yellow {
            background: #fef3c7;
            border-color: #eab308;
        }
        .stat-card.red {
            background: #fee2e2;
            border-color: #ef4444;
        }
        .stat-label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .stat-value {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
        }
        .stat-detail {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }
        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .details-table th {
            background: #f1f5f9;
            padding: 10px;
            text-align: left;
            font-size: 11px;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #e2e8f0;
        }
        .details-table td {
            padding: 10px;
            border: 1px solid #e2e8f0;
            font-size: 13px;
        }
        .details-table .amount {
            text-align: right;
            font-weight: 600;
        }
        .details-table .positive {
            color: #16a34a;
        }
        .details-table .negative {
            color: #dc2626;
        }
        .details-table .neutral {
            color: #64748b;
        }
        .totals-row {
            background: #1e293b;
            color: white;
            font-weight: 700;
            font-size: 14px;
        }
        .discrepancy-section {
            margin-bottom: 30px;
            padding: 20px;
            border-radius: 8px;
        }
        .discrepancy-section.has-issue {
            background: #fef2f2;
            border: 2px solid #ef4444;
        }
        .discrepancy-section.balanced {
            background: #f0fdf4;
            border: 2px solid #22c55e;
        }
        .discrepancy-title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        .discrepancy-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }
        .discrepancy-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            background: white;
            border-radius: 6px;
        }
        .justification-box {
            padding: 15px;
            background: white;
            border-radius: 6px;
            margin-top: 15px;
        }
        .justification-label {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 8px;
        }
        .justification-text {
            font-size: 13px;
            color: #1e293b;
            line-height: 1.6;
        }
        .notes-section {
            margin-bottom: 30px;
            padding: 15px;
            background: #fef3c7;
            border: 1px solid #fde68a;
            border-radius: 8px;
        }
        .notes-title {
            font-size: 14px;
            font-weight: 600;
            color: #92400e;
            margin-bottom: 10px;
        }
        .notes-content {
            font-size: 13px;
            color: #78350f;
            line-height: 1.6;
        }
        .signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 50px;
            margin-bottom: 30px;
        }
        .signature-block {
            text-align: center;
        }
        .signature-label {
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 5px;
        }
        .signature-name {
            font-size: 14px;
            color: #1e293b;
            margin-bottom: 40px;
        }
        .signature-line {
            border-top: 2px solid #94a3b8;
            padding-top: 10px;
        }
        .signature-placeholder {
            color: #94a3b8;
            font-size: 11px;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #94a3b8;
            font-size: 11px;
        }
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background: #dc2626;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .print-button:hover {
            background: #991b1b;
        }
        @media print {
            body {
                padding: 0;
            }
            .print-button {
                display: none;
            }
            .container {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">{{ __('modules.cashier.print') }}</button>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>{{ __('modules.cashier.closingSlip') }}</h1>
            <div class="subtitle">{{ __('modules.cashier.closingSlipSubtitle') }}</div>
        </div>

        <!-- Restaurant Info -->
        <div class="restaurant-info">
            <div class="restaurant-name">{{ $session->branch->restaurant->name ?? 'MenuPro' }}</div>
            <div class="branch-name">{{ $session->branch->name }}</div>
        </div>

        <!-- Session Info -->
        <div class="session-info">
            <div class="info-item">
                <div class="info-label">{{ __('modules.cashier.sessionNumber') }}</div>
                <div class="info-value">#{{ $session->session_number }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('modules.cashier.openingDate') }}</div>
                <div class="info-value">{{ $session->opened_at->format('d/m/Y H:i') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('modules.cashier.openedBy') }}</div>
                <div class="info-value">{{ $session->openedByUser->name }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('modules.cashier.status') }}</div>
                <div class="info-value"><span class="status-badge">{{ __('modules.cashier.closed') }}</span></div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('modules.cashier.closingDate') }}</div>
                <div class="info-value">{{ $session->closed_at->format('d/m/Y H:i') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('modules.cashier.closedBy') }}</div>
                <div class="info-value">{{ $session->closedByUser->name }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('modules.cashier.sessionDuration') }}</div>
                <div class="info-value">{{ $session->getDuration() }}</div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-cards">
            <div class="stat-card blue">
                <div class="stat-label">{{ __('modules.cashier.initialFund') }}</div>
                <div class="stat-value">{{ currency_format($session->opening_balance, restaurant()->currency_id) }}</div>
                <div class="stat-detail">F CFA</div>
            </div>
            <div class="stat-card green">
                <div class="stat-label">{{ __('modules.cashier.totalSales') }}</div>
                <div class="stat-value">{{ currency_format($session->total_sales, restaurant()->currency_id) }}</div>
                <div class="stat-detail">{{ $session->total_transactions }} {{ __('modules.cashier.transactions') }}</div>
            </div>
            <div class="stat-card yellow">
                <div class="stat-label">{{ __('modules.cashier.expectedBalance') }}</div>
                <div class="stat-value">{{ currency_format($session->expected_balance, restaurant()->currency_id) }}</div>
                <div class="stat-detail">F CFA</div>
            </div>
            <div class="stat-card {{ $session->discrepancy == 0 ? 'green' : 'red' }}">
                <div class="stat-label">{{ __('modules.cashier.discrepancy') }}</div>
                <div class="stat-value">{{ currency_format($session->discrepancy, restaurant()->currency_id) }}</div>
                <div class="stat-detail">F CFA</div>
            </div>
        </div>

        <!-- Payment Methods Details -->
        <div class="section-title">{{ __('modules.cashier.detailsByPaymentMethod') }}</div>
        
        <table class="details-table">
            <thead>
                <tr>
                    <th>{{ __('modules.cashier.paymentMethod') }}</th>
                    <th style="text-align: right;">{{ __('modules.cashier.opening') }}</th>
                    <th style="text-align: right;">{{ __('modules.cashier.transactions') }}</th>
                    <th style="text-align: right;">{{ __('modules.cashier.expected') }}</th>
                    <th style="text-align: right;">{{ __('modules.cashier.closing') }}</th>
                    <th style="text-align: right;">{{ __('modules.cashier.difference') }}</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $paymentMethods = [
                        'cash' => __('modules.cashier.cash'),
                        'mobile_money' => __('modules.cashier.mobile_money'),
                        'card' => __('modules.cashier.card'),
                        'qr_code' => __('modules.cashier.qr_code'),
                    ];
                    $totalOpening = 0;
                    $totalTransactions = 0;
                    $totalExpected = 0;
                    $totalClosing = 0;
                    $totalDifference = 0;
                @endphp
                
                @foreach($session->details->where('type', 'opening')->groupBy('payment_method') as $method => $details)
                    @php
                        $opening = $details->sum('amount');
                        $transactions = $session->transactions->where('payment_method', $method)->sum('amount');
                        $expected = $opening + $transactions;
                        $closing = $session->details->where('type', 'closing')->where('payment_method', $method)->sum('amount');
                        $difference = $closing - $expected;
                        
                        $totalOpening += $opening;
                        $totalTransactions += $transactions;
                        $totalExpected += $expected;
                        $totalClosing += $closing;
                        $totalDifference += $difference;
                    @endphp
                    <tr>
                        <td><strong>{{ $paymentMethods[$method] ?? $method }}</strong></td>
                        <td class="amount neutral">{{ currency_format($opening, restaurant()->currency_id) }}</td>
                        <td class="amount positive">+{{ currency_format($transactions, restaurant()->currency_id) }}</td>
                        <td class="amount neutral">{{ currency_format($expected, restaurant()->currency_id) }}</td>
                        <td class="amount neutral">{{ currency_format($closing, restaurant()->currency_id) }}</td>
                        <td class="amount {{ $difference == 0 ? 'neutral' : ($difference > 0 ? 'positive' : 'negative') }}">
                            {{ $difference >= 0 ? '+' : '' }}{{ currency_format($difference, restaurant()->currency_id) }}
                        </td>
                    </tr>
                @endforeach
                
                <tr class="totals-row">
                    <td>{{ __('modules.cashier.totals') }}</td>
                    <td class="amount">{{ currency_format($totalOpening, restaurant()->currency_id) }}</td>
                    <td class="amount">+{{ currency_format($totalTransactions, restaurant()->currency_id) }}</td>
                    <td class="amount">{{ currency_format($totalExpected, restaurant()->currency_id) }}</td>
                    <td class="amount">{{ currency_format($totalClosing, restaurant()->currency_id) }}</td>
                    <td class="amount">{{ $totalDifference >= 0 ? '+' : '' }}{{ currency_format($totalDifference, restaurant()->currency_id) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Discrepancy Analysis -->
        @if($session->hasDiscrepancies())
        <div class="discrepancy-section has-issue">
            <div class="discrepancy-title" style="color: #dc2626;">
                ⚠️ {{ __('modules.cashier.discrepancyDetected') }}
            </div>
            
            <div class="discrepancy-grid">
                @if($session->discrepancy > 0)
                    <div class="discrepancy-item">
                        <span style="color: #dc2626; font-weight: 600;">{{ __('modules.cashier.surplus') }}:</span>
                        <span style="color: #dc2626; font-weight: 700;">+{{ currency_format($session->discrepancy, restaurant()->currency_id) }}</span>
                    </div>
                @else
                    <div class="discrepancy-item">
                        <span style="color: #dc2626; font-weight: 600;">{{ __('modules.cashier.shortage') }}:</span>
                        <span style="color: #dc2626; font-weight: 700;">{{ currency_format($session->discrepancy, restaurant()->currency_id) }}</span>
                    </div>
                @endif
            </div>

            @if($session->discrepancy_justification)
            <div class="justification-box">
                <div class="justification-label">{{ __('modules.cashier.justificationProvided') }}:</div>
                <div class="justification-text">{{ $session->discrepancy_justification }}</div>
            </div>
            @endif

            <div style="margin-top: 15px; padding: 15px; background: white; border-radius: 6px;">
                <div style="font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 10px;">
                    {{ __('modules.cashier.detailsByMethod') }}:
                </div>
                @foreach($session->details->where('type', 'opening')->groupBy('payment_method') as $method => $details)
                    @php
                        $opening = $details->sum('amount');
                        $transactions = $session->transactions->where('payment_method', $method)->sum('amount');
                        $expected = $opening + $transactions;
                        $closing = $session->details->where('type', 'closing')->where('payment_method', $method)->sum('amount');
                        $difference = $closing - $expected;
                    @endphp
                    @if($difference != 0)
                    <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                        <span style="font-size: 13px;">{{ $paymentMethods[$method] ?? $method }}</span>
                        <span style="font-size: 13px; font-weight: 600; color: {{ $difference > 0 ? '#16a34a' : '#dc2626' }};">
                            {{ $difference >= 0 ? '+' : '' }}{{ currency_format($difference, restaurant()->currency_id) }}
                        </span>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
        @else
        <div class="discrepancy-section balanced">
            <div class="discrepancy-title" style="color: #16a34a;">
                ✓ {{ __('modules.cashier.balancedCashRegister') }}
            </div>
            <p style="font-size: 13px; color: #15803d;">
                {{ __('modules.cashier.noDiscrepancyMessage') }}
            </p>
        </div>
        @endif

        <!-- Closing Notes -->
        @if($session->closing_notes)
        <div class="notes-section">
            <div class="notes-title">{{ __('modules.cashier.closingNotes') }}</div>
            <div class="notes-content">{{ $session->closing_notes }}</div>
        </div>
        @endif

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-block">
                <div class="signature-label">{{ __('modules.cashier.cashier') }}</div>
                <div class="signature-name">{{ $session->closedByUser->name }}</div>
                <div class="signature-line">
                    <div class="signature-placeholder">{{ __('modules.cashier.signatureAndDate') }}</div>
                </div>
            </div>
            <div class="signature-block">
                <div class="signature-label">{{ __('modules.cashier.manager') }}</div>
                <div class="signature-name">_________________________</div>
                <div class="signature-line">
                    <div class="signature-placeholder">{{ __('modules.cashier.signatureAndDate') }}</div>
                </div>
            </div>
            <div class="signature-block">
                <div class="signature-label">{{ __('modules.cashier.accountant') }}</div>
                <div class="signature-name">_________________________</div>
                <div class="signature-line">
                    <div class="signature-placeholder">{{ __('modules.cashier.signatureAndDate') }}</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            {{ __('modules.cashier.documentGeneratedAutomatically') }} {{ $session->branch->restaurant->name ?? 'MenuPro' }} - {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
</body>
</html>