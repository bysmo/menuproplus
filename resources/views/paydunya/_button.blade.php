{{-- resources/views/paydunya/_button.blade.php --}}
{{-- Intégrer ce composant dans votre vue de commande existante --}}

@if(isset($paymentGateways) && !empty($paymentGateways->paydunya_master_key))
<div class="paydunya-payment-section mt-3">
    <button
        id="paydunya-pay-btn"
        type="button"
        class="btn btn-warning w-100 d-flex align-items-center justify-content-center gap-2"
        data-order-id="{{ $order->id }}"
        onclick="initiatePaydunyaPayment(this)"
    >
        {{-- Logo PayDunya (SVG inline ou image) --}}
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="11" fill="#FF6600"/>
            <text x="12" y="16" font-size="10" text-anchor="middle" fill="white" font-weight="bold">PD</text>
        </svg>
        <span>{{ __('Payer avec PayDunya') }}</span>
        <span id="paydunya-spinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
    </button>

    <p class="text-muted small text-center mt-1">
        <i class="fas fa-mobile-alt me-1"></i>
        Orange Money · Wave · Free Money · MTN · Moov · Carte bancaire
    </p>
</div>

@push('scripts')
<script>
function initiatePaydunyaPayment(button) {
    const orderId  = button.dataset.orderId;
    const spinner  = document.getElementById('paydunya-spinner');
    const btnText  = button.querySelector('span:not(.spinner-border)');

    // État de chargement
    button.disabled = true;
    spinner.classList.remove('d-none');
    btnText.textContent = '{{ __("Connexion à PayDunya...") }}';

    fetch('{{ route("paydunya.initiate-payment") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ order_id: orderId }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success' && data.redirect_url) {
            // Redirection vers la page de paiement PayDunya
            window.location.href = data.redirect_url;
        } else {
            alert(data.message || '{{ __("Une erreur est survenue. Veuillez réessayer.") }}');
            button.disabled = false;
            spinner.classList.add('d-none');
            btnText.textContent = '{{ __("Payer avec PayDunya") }}';
        }
    })
    .catch(error => {
        console.error('PayDunya error:', error);
        alert('{{ __("Erreur de connexion. Veuillez réessayer.") }}');
        button.disabled = false;
        spinner.classList.add('d-none');
        btnText.textContent = '{{ __("Payer avec PayDunya") }}';
    });
}
</script>
@endpush
@endif