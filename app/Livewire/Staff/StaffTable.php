<?php

namespace App\Livewire\Staff;

use App\Models\Role;
use App\Models\User;
use App\Scopes\BranchScope;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class StaffTable extends Component
{

    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;

    public $search;
    public $customer;
    public $roles;
    public $showEditCustomerModal = false;
    public $confirmDeleteCustomerModal = false;
    public $showCustomerOrderModal = false;

    protected $listeners = ['refreshCustomers' => '$refresh'];

    public function mount()
    {
        $this->roles = Role::where('name', '<>', 'Super Admin')->get();
    }

    public function showEditCustomer($id)
    {
        $this->customer = User::withoutGlobalScopes()->where('restaurant_id', restaurant()->id)->findOrFail($id);
        $this->showEditCustomerModal = true;
    }

    #[On('hideEditStaff')]
    public function hideEditStaff()
    {
        $this->showEditCustomerModal = false;
    }

    public function showDeleteCustomer($id)
    {
        $this->customer = User::withoutGlobalScopes()->where('restaurant_id', restaurant()->id)->findOrFail($id);
        $this->confirmDeleteCustomerModal = true;
    }

    public function deleteCustomer($id)
    {
        $user = User::withoutGlobalScopes()->where('restaurant_id', restaurant()->id)->find($id);

        if (!$user) {
            return;
        }

        // Never allow a staff member to delete their own account from this table.
        if ($user->id == user()->id) {
            $this->alert('error', __('messages.cannotDeleteOwnAccount'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);
            return;
        }

        $restaurantId = $user->restaurant_id;
        $user->delete();

        if ($restaurantId) {
            cache()->forget('restaurant_' . $restaurantId . '_staff_stats');
        }

        $this->confirmDeleteCustomerModal = false;
        $this->customer = null;

        $this->alert('success', __('messages.memberDeleted'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);

    }

    public function setUserRole($role, $userID)
    {
        // The target user must belong to this restaurant, and the role must be
        // one of the restaurant-scoped roles this component itself offers —
        // never an arbitrary client-supplied role name (e.g. a global role
        // such as Super Admin) or a user from another tenant.
        $employee = User::withoutGlobalScopes()->where('restaurant_id', restaurant()->id)->find($userID);

        if (!$employee || !$this->roles->pluck('name')->contains($role)) {
            return;
        }

        $employee->syncRoles([$role]);
        $this->redirect(route('staff.index'), navigate: true);
    }

    #[On('hideEditCustomer')]
    public function hideEditCustomer()
    {
        $this->showEditCustomerModal = false;
    }

    public function render()
    {
        $query = User::withoutGlobalScope(BranchScope::class)
            ->where(function($q) {
                return $q->where('branch_id', branch()->id)
                    ->orWhereNull('branch_id');
            })
            ->where('restaurant_id', restaurant()->id)
        ->where(function($q) {
            return $q->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('email', 'like', '%'.$this->search.'%');
        })
        ->paginate(10);

        return view('livewire.staff.staff-table', [
            'members' => $query
        ]);
    }

}
