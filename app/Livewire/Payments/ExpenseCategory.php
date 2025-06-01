<?php

namespace App\Livewire\Payments;

use App\Models\ExpenseCategory as ModelsExpenseCategory;
use Livewire\Component;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ExpenseCategory extends Component
{
    use WithPagination, LivewireAlert;
    public $search;
    public $deleteExpenseCategory;
    public $confirmDeleteExpenseCategory = false;
    public $showAddExpenseCategoryModal = false;
    public $showEditExpenseCategoryModal = false;

    public $selectedExpenseCategory;

    public function showEditExpenseCategory($id)
    {
        $this->showEditExpenseCategoryModal = true;
        $this->selectedExpenseCategory = ModelsExpenseCategory::find($id);
    }

    public function showDeleteExpenseCategory($id)
    {

         ModelsExpenseCategory::find($id)->delete();
         $this->confirmDeleteExpenseCategory = false;
          $this->deleteExpenseCategory = null;

         $this->alert('success', __('messages.expenseCategoryDeleted'), [
         'toast' => true,
         'position' => 'top-end',
         'showCancelButton' => false,
         'cancelButtonText' => __('app.close')
         ]);
    }

    public function render()
    {
        $query = ModelsExpenseCategory::query();
        $expenseCategories = $query->paginate(10);
        return view('livewire.payments.expense-category', ['expenseCategories' => $expenseCategories]);
    }

}
