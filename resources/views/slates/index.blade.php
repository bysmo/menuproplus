@extends('layouts.app')

@section('content')
    @livewire('shop.slate-list')
@endsection

@push('scripts')
<script>
    // Écouter l'événement de confirmation de paiement
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('payment-confirmed', (event) => {
            // Rafraîchir la liste
            Livewire.dispatch('$refresh');

            // Afficher une notification de succès
            toastr.success('Paiement confirmé avec succès');
        });
    });
</script>
@endpush
