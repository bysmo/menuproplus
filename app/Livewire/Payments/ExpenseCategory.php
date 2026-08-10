<?php

namespace App\Livewire\Payments;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\ExpenseCategory as ModelsExpenseCategory;

class ExpenseCategory extends Component
{
    use WithPagination, LivewireAlert;
    public $search;
    public $deleteExpenseCategory;
    public $confirmDeleteExpenseCategory = false;
    public $showAddExpenseCategoryModal = false;
    public $showEditExpenseCategoryModal = false;

    public $selectedExpenseCategory;

    public function mount()
    {
        abort_if(!user_can('Show Expense Category'), 403);
    }

    public function showEditExpenseCategory($id)
    {
        abort_if(!user_can('Update Expense Category'), 403);

        $this->showEditExpenseCategoryModal = true;
        $this->selectedExpenseCategory = ModelsExpenseCategory::find($id);
    }

     #[On('hideEditExpenseCategory')]
    public function hideEditExpenseCategory()
    {
        $this->showEditExpenseCategoryModal = false;
    }

    public function showAddExpenseCategory()
    {
        $this->showAddExpenseCategoryModal = true;
    }

    #[On('hideAddExpenseCategory')]
    public function hideAddExpenseCategory()
    {
        $this->showAddExpenseCategoryModal = false;
    }

    public function showDeleteExpenseCategory($id)
    {
        abort_if(!user_can('Delete Expense Category'), 403);

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
