<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture Ardoise #{{ $slate->code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #000;
        }

        .restaurant-logo {
            max-width: 150px;
            margin-bottom: 10px;
        }

        .restaurant-info {
            margin-top: 10px;
        }

        .restaurant-name {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .restaurant-address {
            font-size: 10pt;
            color: #666;
        }

        .invoice-title {
            font-size: 24pt;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }

        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .info-left, .info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .info-box {
            padding: 15px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            margin-bottom: 10px;
        }

        .info-box strong {
            display: block;
            margin-bottom: 5px;
            font-size: 12pt;
            color: #495057;
        }

        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .order-table thead {
            background: #343a40;
            color: white;
        }

        .order-table th,
        .order-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        .order-table th {
            font-weight: bold;
        }

        .order-table tbody tr:hover {
            background: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totals-section {
            float: right;
            width: 300px;
            margin-top: 20px;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 15px;
            border-bottom: 1px solid #dee2e6;
        }

        .totals-row.grand-total {
            background: #343a40;
            color: white;
            font-weight: bold;
            font-size: 14pt;
            margin-top: 10px;
        }

        .footer {
            clear: both;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 9pt;
            color: #666;
        }

        .payment-status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 4px;
            font-weight: bold;
            margin: 10px 0;
        }

        .payment-status.paid {
            background: #28a745;
            color: white;
        }

        .payment-status.unpaid {
            background: #dc3545;
            color: white;
        }

        .payment-status.partial {
            background: #ffc107;
            color: #000;
        }

        .order-group {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .order-header {
            background: #e9ecef;
            padding: 10px 15px;
            font-weight: bold;
            margin-top: 20px;
            border-left: 4px solid #007bff;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    {{-- Bouton d'impression (masqué à l'impression) --}}
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 30px; font-size: 14pt; cursor: pointer;">
            🖨️ Imprimer
        </button>
    </div>

    {{-- En-tête --}}
    <div class="header">
        @if($receiptSettings && $receiptSettings->logo)
            <img src="{{ asset($receiptSettings->logo) }}" alt="{{ $restaurant->name }}" class="restaurant-logo">
        @endif

        <div class="restaurant-info">
            <div class="restaurant-name">{{ $restaurant->name }}</div>
            <div class="restaurant-address">
                @if($branch)
                    {{ $branch->address }}<br>
                    @if($branch->phone)
                        Tél: {{ $branch->phone }}<br>
                    @endif
                @endif
                @if($restaurant->email)
                    Email: {{ $restaurant->email }}
                @endif
            </div>
        </div>
    </div>

    {{-- Titre --}}
    <div class="invoice-title">
        FACTURE ARDOISE
    </div>

    {{-- Informations --}}
    <div class="info-section">
        <div class="info-left">
            <div class="info-box">
                <strong>Informations Ardoise</strong>
                Code: <strong>#{{ $slate->code }}</strong><br>
                Date: {{ $slate->created_at->format('d/m/Y H:i') }}<br>
                Branche: {{ $branch?->name ?? 'N/A' }}<br>
                UUID: {{ Str::limit($slate->device_uuid, 20) }}
            </div>
        </div>

        <div class="info-right">
            <div class="info-box">
                <strong>Client</strong>
                @if($slate->customer)
                    Nom: {{ $slate->customer->name }}<br>
                    @if($slate->customer->phone)
                        Téléphone: {{ $slate->customer->phone }}<br>
                    @endif
                    @if($slate->customer->email)
                        Email: {{ $slate->customer->email }}
                    @endif
                @else
                    <em>Client anonyme</em>
                @endif
            </div>

            <div class="info-box">
                <strong>Statut de paiement</strong>
                @if($slate->status === 'paid')
                    <span class="payment-status paid">PAYÉ</span>
                @elseif($slate->paid_amount > 0 && $slate->remaining_amount > 0)
                    <span class="payment-status partial">PARTIELLEMENT PAYÉ</span>
                @else
                    <span class="payment-status unpaid">NON PAYÉ</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Commandes et articles --}}
    @foreach($slate->orders as $order)
        <div class="order-group">
            <div class="order-header">
                Commande #{{ $order->id }}
                @if($order->table)
                    - Table: {{ $order->table->name }}
                @endif
                - {{ $order->created_at->format('d/m/Y H:i') }}
                - Statut: {{ ucfirst($order->status) }}
            </div>

            <table class="order-table">
                <thead>
                    <tr>
                        <th>Article</th>
                        <th class="text-center">Quantité</th>
                        <th class="text-right">Prix unitaire</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @if($order->items && $order->items->count() > 0)
                        @foreach($order->items as $item)
                            <tr>
                                <td>
                                    {{ $item->menuItem?->name ?? $item->item_name }}
                                    @if($item->notes)
                                        <br><small style="color: #666;">Note: {{ $item->notes }}</small>
                                    @endif
                                </td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-right">{{ number_format($item->price, 2) }}€</td>
                                <td class="text-right">{{ number_format($item->price * $item->quantity, 2) }}€</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="text-center"><em>Aucun article</em></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    @endforeach

    {{-- Totaux --}}
    <div class="totals-section">
        <div class="totals-row">
            <span>Sous-total:</span>
            <span>{{ number_format($subtotal, 2) }}€</span>
        </div>

        @if($totalDiscount > 0)
            <div class="totals-row">
                <span>Remise:</span>
                <span>-{{ number_format($totalDiscount, 2) }}€</span>
            </div>
        @endif

        @if($totalTaxAmount > 0)
            <div class="totals-row">
                <span>Taxes:</span>
                <span>{{ number_format($totalTaxAmount, 2) }}€</span>
            </div>
        @endif

        <div class="totals-row grand-total">
            <span>TOTAL:</span>
            <span>{{ number_format($grandTotal, 2) }}€</span>
        </div>

        @if($slate->paid_amount > 0)
            <div class="totals-row" style="background: #28a745; color: white;">
                <span>Payé:</span>
                <span>{{ number_format($slate->paid_amount, 2) }}€</span>
            </div>
        @endif

        @if($slate->remaining_amount > 0)
            <div class="totals-row" style="background: #dc3545; color: white;">
                <span>Reste à payer:</span>
                <span>{{ number_format($slate->remaining_amount, 2) }}€</span>
            </div>
        @endif
    </div>

    {{-- Pied de page --}}
    <div class="footer">
        @if($receiptSettings && $receiptSettings->footer_text)
            {{ $receiptSettings->footer_text }}<br>
        @endif
        Facture générée le {{ now()->format('d/m/Y à H:i') }}<br>
        Merci de votre visite !
    </div>
</body>
</html>
