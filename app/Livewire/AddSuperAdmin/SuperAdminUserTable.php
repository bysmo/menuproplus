<?php

namespace App\Livewire\AddSuperAdmin;

use App\Models\Role;
use App\Models\User;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class SuperAdminUserTable extends Component
{
    use LivewireAlert;
    use WithPagination;

    public $search = '';
    public $user;
    public $roles;
    public $showEditUserModal = false;
    public $confirmDeleteUserModal = false;

    protected $listeners = ['refreshUsers' => '$refresh'];

    public function mount()
    {
        abort_if(!user()->hasRole('Super Admin'), 403);

        $this->roles = Role::where('name', '<>', 'Super Admin')->get();
    }

    public function showEditUser($id)
    {
        $user = User::whereNull('restaurant_id')->find($id);
        if (!$user) {
            return;
        }

        // Dispatch event to the edit component
        $this->dispatch('showEditUser', $id);
    }

    #[On('hideEditUser')]
    public function hideEditUser()
    {
        $this->showEditUserModal = false;
    }

    public function showDeleteUser($id)
    {
        $this->user = User::whereNull('restaurant_id')->findOrFail($id);
        $this->confirmDeleteUserModal = true;
    }

    public function deleteUser($id)
    {
        $user = User::whereNull('restaurant_id')->findOrFail($id);

        // Don't allow superadmin to delete themselves
        if ($user->id == user()->id) {
            $this->alert('error', __('messages.cannotDeleteOwnAccount'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);
            return;
        }

        $user->delete();
        $this->confirmDeleteUserModal = false;
        $this->user = null;

        $this->alert('success', __('messages.userDeleted'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function setUserRole($role, $userID)
    {
        $user = User::whereNull('restaurant_id')->findOrFail($userID);

        // Don't allow superadmin to change their own role
        if ($user->id == user()->id) {
            $this->alert('error', __('messages.cannotEditOwnRole'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);
            return;
        }

        // Only a role from the allowed (non-Super-Admin) list offered by this
        // component may be assigned — never an arbitrary client-supplied value.
        if (!$this->roles->pluck('name')->contains($role)) {
            $this->alert('error', 'Invalid role.', [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);
            return;
        }

        $user->syncRoles([$role]);

        $this->alert('success', __('messages.userRoleUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function render()
    {
        $query = User::whereNull('restaurant_id')
            ->where(function ($q) {
                return $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);

        return view('livewire.add-super-admin.super-admin-user-table', [
            'users' => $query
        ]);
    }
}
