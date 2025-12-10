<?php

namespace App\Livewire\Shop;

use App\Models\Slate;
use Livewire\Component;
use Livewire\WithPagination;

class SlateList extends Component
{
    use WithPagination;

    public $restaurant;
    public $branch;
    public $search = '';
    public $statusFilter = 'all';
    public $dateFrom = '';
    public $dateTo = '';
    public $perPage = 20;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => '']
    ];

    public function mount()
    {
        $this->restaurant = restaurant();
        $this->branch = branch();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = 'all';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    public function getSlatesProperty()
    {
        $query = Slate::with([
            'orders' => function($q) {
                $q->select('id', 'slate_id', 'status', 'total',  'created_at');
            },
            'customer:id,name,email,phone',
            'branch:id,name'
        ])
        ->where('restaurant_id', $this->restaurant->id);

        // Filtre par branche si sélectionnée
        if ($this->branch && $this->branch->id) {
            $query->where('branch_id', $this->branch->id);
        }

        // Filtre par recherche (code ou device_uuid)
        if ($this->search) {
            $query->where(function($q) {
                $q->where('code', 'like', '%' . $this->search . '%')
                  ->orWhere('device_uuid', 'like', '%' . $this->search . '%')
                  ->orWhereHas('customer', function($cq) {
                      $cq->where('name', 'like', '%' . $this->search . '%')
                         ->orWhere('phone', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Filtre par statut
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Filtre par date
        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        return $query->orderBy('created_at', 'desc')
                    ->paginate($this->perPage);
    }

    public function confirmPayment($slateId)
    {
        try {
            $response = \Http::post(route('shop.slates.confirm-payment', $slateId));

            if ($response->successful()) {
                $this->dispatch('payment-confirmed', slateId: $slateId);
                session()->flash('success', 'Paiement confirmé avec succès');
            } else {
                session()->flash('error', 'Erreur lors de la confirmation du paiement');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la confirmation du paiement');
        }
    }

    /**
     * Calculer le statut de paiement d'une ardoise
     */
    public function getPaymentStatus($slate)
    {
        if ($slate->status === 'paid') {
            return [
                'label' => 'Payé',
                'class' => 'bg-success'
            ];
        }

        if ($slate->status === 'pending_verification') {
            return [
                'label' => 'En attente de vérification',
                'class' => 'bg-warning'
            ];
        }

        if ($slate->paid_amount > 0 && $slate->remaining_amount > 0) {
            return [
                'label' => 'Partiellement payé',
                'class' => 'bg-warning'
            ];
        }

        if ($slate->remaining_amount > 0) {
            return [
                'label' => 'Non payé',
                'class' => 'bg-danger'
            ];
        }

        return [
            'label' => 'Actif',
            'class' => 'bg-secondary'
        ];
    }

    public function render()
    {
        return view('livewire.shop.slate-list', [
            'slates' => $this->slates
        ]);
    }
}
