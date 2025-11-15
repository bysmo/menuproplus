<?php

namespace App\Livewire\Pos;

use Livewire\Component;

class ItemVariations extends Component
{

    public $menuItem;
    public $itemVariation;
    public $variationName;
    public $variationPrice;
    public $showEditVariationsModal = false;
    public $showDeleteVariationsModal = false;
    public $orderTypeId;
    public $deliveryAppId;

    public function mount($menuItem, $orderTypeId = null, $deliveryAppId = null)
    {
        $this->menuItem = $menuItem->load('variations');
        $this->orderTypeId = $orderTypeId;
        $this->deliveryAppId = $deliveryAppId;

        $this->applyPriceContext();
    }

    public function applyPriceContext()
    {
        if (!$this->orderTypeId) {
            return;
        }

        $this->menuItem->setPriceContext($this->orderTypeId, $this->deliveryAppId);

        foreach ($this->menuItem->variations as $variation) {
            $variation->setPriceContext($this->orderTypeId, $this->deliveryAppId);
        }
    }

    public function hydrate()
    {
        $this->applyPriceContext();
    }

    public function setItemVariation($id)
    {
        $this->dispatch('setPosVariation', variationId: $id);
    }

    public function render()
    {
        return view('livewire.pos.item-variations');
    }

}
