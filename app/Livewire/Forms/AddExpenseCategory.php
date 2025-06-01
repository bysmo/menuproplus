<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use App\Models\ExpenseCategory;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AddExpenseCategory extends Component
{

    use LivewireAlert;

    public $name;
    public $description;
    public $is_active = true;
    public $showExpenseCategoryModal = false;

    protected $rules = [
        'name' => 'required|min:2|unique:expense_categories,name',
        'description' => 'nullable|string',
        'is_active' => 'boolean'
    ];

    public function save()
    {
        $validated = $this->validate();
        ExpenseCategory::create($validated);
        $this->showExpenseCategoryModal = false;
        $this->dispatch('hideExpenseCategoryModal');
        $this->reset(['name', 'description', 'is_active']);
         $this->alert('success', __('messages.expenseCategoryAdded'), [
         'toast' => true,
         'position' => 'top-end',
         'showCancelButton' => false,
         'cancelButtonText' => __('app.close')
         ]);
    }

    public function render()
    {
        return view('livewire.forms.add-expense-category');
    }

}
