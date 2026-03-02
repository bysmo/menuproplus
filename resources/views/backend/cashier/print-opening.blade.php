<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('modules.cashier.openingSlip') }} - Session #{{ $session->session_number }}</title>
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
            max-width: 800px;
            margin: 0 auto;
            background: white;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #1e40af;
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
            grid-template-columns: repeat(2, 1fr);
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
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
        }
        .financial-section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
        }
        .amounts-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .amounts-table th {
            background: #f1f5f9;
            padding: 12px;
            text-align: left;
            font-size: 12px;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #e2e8f0;
        }
        .amounts-table td {
            padding: 12px;
            border: 1px solid #e2e8f0;
            font-size: 14px;
        }
        .amounts-table .amount {
            text-align: right;
            font-weight: 600;
            color: #1e293b;
        }
        .total-row {
            background: #dbeafe;
            font-weight: 700;
        }
        .total-row td {
            font-size: 16px;
            color: #1e40af;
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
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
            margin-top: 50px;
            margin-bottom: 30px;
        }
        .signature-block {
            text-align: center;
        }
        .signature-label {
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 5px;
        }
        .signature-name {
            font-size: 16px;
            color: #1e293b;
            margin-bottom: 50px;
        }
        .signature-line {
            border-top: 2px solid #94a3b8;
            padding-top: 10px;
        }
        .signature-placeholder {
            color: #94a3b8;
            font-size: 12px;
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
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .print-button:hover {
            background: #1e40af;
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
            <h1>{{ __('modules.cashier.openingSlip') }}</h1>
            <div class="subtitle">{{ __('modules.cashier.openingSlipSubtitle') }}</div>
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
                <div class="info-value">{{ $session->opened_at->format('d/m/Y') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('modules.cashier.openingTime') }}</div>
                <div class="info-value">{{ $session->opened_at->format('H:i:s') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('modules.cashier.cashier') }}</div>
                <div class="info-value">{{ $session->openedByUser->name }}</div>
            </div>
        </div>

        <!-- Financial Details -->
        <div class="financial-section">
            <div class="section-title">{{ __('modules.cashier.openingDetails') }}</div>
            
            <table class="amounts-table">
                <thead>
                    <tr>
                        <th>{{ __('modules.cashier.paymentMethod') }}</th>
                        <th style="text-align: right;">{{ __('modules.cashier.amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($session->details->where('type', 'opening') as $detail)
                    <tr>
                        <td>{{ $detail->payment_method_label }}</td>
                        <td class="amount">{{ currency_format($detail->amount, restaurant()->currency_id) }}</td>
                    </tr>
                    @endforeach
                    <tr class="total-row">
                        <td>{{ __('modules.cashier.totalOpeningFund') }}</td>
                        <td class="amount">{{ currency_format($session->opening_balance, restaurant()->currency_id) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Notes -->
        @if($session->opening_notes)
        <div class="notes-section">
            <div class="notes-title">{{ __('modules.cashier.notesAndRemarks') }}</div>
            <div class="notes-content">{{ $session->opening_notes }}</div>
        </div>
        @endif

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-block">
                <div class="signature-label">{{ __('modules.cashier.cashier') }}</div>
                <div class="signature-name">{{ $session->openedByUser->name }}</div>
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
        </div>

        <!-- Footer -->
        <div class="footer">
            {{ __('modules.cashier.documentGeneratedAutomatically') }} {{ $session->branch->restaurant->name ?? 'MenuPro' }} - {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
</body>
</html>