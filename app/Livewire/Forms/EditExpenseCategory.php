<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use App\Models\ExpenseCategory;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditExpenseCategory extends Component
{
    use LivewireAlert;

    public $expenseCategory;
    public $expenseCategoryId;
    public $name;
    public $description;
    public $is_active = true;
    public $showExpenseCategoryModal = false;

    public function mount()
    {
        // Add your code here
        $this->expenseCategory = $this->expenseCategoryId;
        $this->name = $this->expenseCategory->name;
        $this->description = $this->expenseCategory->description;
    }

    public function save()
    {

        $expenseCategory = ExpenseCategory::findOrFail($this->expenseCategoryId->id);
        $expenseCategory->update([
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active
        ]);

        $this->dispatch('expenseCategoryUpdated');

        $this->alert('success', __('messages.expenseCategoryUpdated'), [
           'toast' => true,
           'position' => 'top-end',
           'showCancelButton' => false,
           'cancelButtonText' => __('app.close')
           ]);
    }

    public function render()
    {
        return view('livewire.forms.edit-expense-category');
    }

}
