@extends('layouts.app')
@section('content')
    <div>
        {{-- Filtres --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    class="form-control"
                    placeholder="Rechercher (code, UUID, client...)"
                >
            </div>

            <div class="col-md-2">
                <select wire:model.live="statusFilter" class="form-select">
                    <option value="all">Tous les statuts</option>
                    <option value="active">Actif</option>
                    <option value="paid">Payé</option>
                    <option value="expired">Expiré</option>
                </select>
            </div>

            <div class="col-md-2">
                <input
                    type="date"
                    wire:model.live="dateFrom"
                    class="form-control"
                    placeholder="Date début"
                >
            </div>

            <div class="col-md-2">
                <input
                    type="date"
                    wire:model.live="dateTo"
                    class="form-control"
                    placeholder="Date fin"
                >
            </div>

            <div class="col-md-3">
                <button wire:click="resetFilters" class="btn btn-secondary">
                    <i class="bx bx-reset"></i> Réinitialiser
                </button>
            </div>
        </div>

        {{-- Messages flash --}}
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Statistiques rapides --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-primary rounded-circle font-size-18">
                                        <i class="bx bx-clipboard"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1">Total Ardoises</p>
                                <h5 class="mb-0">{{ $slates->total() }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-success rounded-circle font-size-18">
                                        <i class="bx bx-check-circle"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1">Payées</p>
                                <h5 class="mb-0">{{ $slates->where('status', 'paid')->count() }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-warning rounded-circle font-size-18">
                                        <i class="bx bx-time"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1">En attente</p>
                                <h5 class="mb-0">{{ $slates->where('status', 'active')->count() }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-info">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-info rounded-circle font-size-18">
                                        <i class="bx bx-euro"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1">Montant Total</p>
                                <h5 class="mb-0">{{ number_format($slates->sum('total_amount'), 2) }}€</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table des ardoises --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle table-nowrap mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Code</th>
                        <th>Client</th>
                        <th>Branche</th>
                        <th>Commandes</th>
                        <th>Total</th>
                        <th>Payé</th>
                        <th>Reste</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($slates as $slate)
                        @php
                            $paymentStatus = $this->getPaymentStatus($slate);
                        @endphp
                        <tr>
                            <td>
                                <strong class="text-primary">#{{ $slate->code }}</strong>
                                <br>
                                <small class="text-muted">{{ Str::limit($slate->device_uuid, 12) }}</small>
                            </td>
                            <td>
                                @if($slate->customer)
                                    <div>
                                        <strong>{{ $slate->customer->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $slate->customer->phone }}</small>
                                    </div>
                                @else
                                    <span class="badge bg-secondary">Anonyme</span>
                                @endif
                            </td>
                            <td>{{ $slate->branch?->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-info">{{ $slate->orders->count() }}</span>
                            </td>
                            <td>
                                <strong>{{ number_format($slate->total_amount, 2) }}€</strong>
                            </td>
                            <td>
                                <span class="text-success">{{ number_format($slate->paid_amount, 2) }}€</span>
                            </td>
                            <td>
                                <span class="text-danger">{{ number_format($slate->remaining_amount, 2) }}€</span>
                            </td>
                            <td>
                                <span class="badge {{ $paymentStatus['class'] }}">
                                    {{ $paymentStatus['label'] }}
                                </span>
                            </td>
                            <td>
                                {{ $slate->created_at->format('d/m/Y H:i') }}
                                <br>
                                <small class="text-muted">
                                    {{ $slate->created_at->diffForHumans() }}
                                </small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    {{-- Bouton Détails --}}
                                    <a
                                        href="#"
                                        class="btn btn-sm btn-info"
                                        title="Détails"
                                        wire:click.prevent="$dispatch('show-slate-details', { slateId: {{ $slate->id }} })"
                                    >
                                        <i class="bx bx-show"></i>
                                    </a>

                                    {{-- Bouton Confirmer Paiement --}}
                                    @if($slate->remaining_amount > 0)
                                        <button
                                            wire:click="confirmPayment({{ $slate->id }})"
                                            wire:confirm="Êtes-vous sûr de vouloir confirmer le paiement de cette ardoise ?"
                                            class="btn btn-sm btn-success"
                                            title="Confirmer le paiement"
                                        >
                                            <i class="bx bx-check"></i>
                                        </button>
                                    @endif

                                    {{-- Bouton Imprimer Facture --}}
                                    <a
                                        href="{{ route('slates.print-invoice', $slate->id) }}"
                                        target="_blank"
                                        class="btn btn-sm btn-primary"
                                        title="Imprimer la facture"
                                    >
                                        <i class="bx bx-printer"></i>
                                    </a>

                                    {{-- Bouton Télécharger PDF --}}
                                    <a
                                        href="{{ route('slates.print-invoice', $slate->id) }}"
                                        class="btn btn-sm btn-secondary"
                                        title="Télécharger PDF"
                                    >
                                        <i class="bx bx-download"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <i class="bx bx-info-circle font-size-24 text-muted"></i>
                                <p class="text-muted mt-2">Aucune ardoise trouvée</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $slates->links() }}
        </div>

        {{-- Indicateur de chargement --}}
        <div wire:loading class="position-fixed top-50 start-50 translate-middle">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
        </div>
    </div>
@endsection
