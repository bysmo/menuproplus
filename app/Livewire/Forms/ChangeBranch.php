<?php

namespace App\Livewire\Forms;

use App\Models\Branch;
use App\Models\OnboardingStep;
use App\Providers\RouteServiceProvider;
use Livewire\Component;

class ChangeBranch extends Component
{

    public $branches;

    public function mount()
    {
        $this->branches = branches() ?? restaurant()->branches;
    }

    public function updateBranch($id)
    {
        $branch = Branch::find($id);

        if (!$branch) {
            abort(404);
        }

        // A user restricted to a single branch may not switch to another branch,
        // even within the same restaurant.
        if (user()->branch_id && (int) user()->branch_id !== (int) $branch->id) {
            abort(403, "Vous n'êtes pas autorisé à accéder à cette branche.");
        }

        session(['branch' => $branch]);

        session()->forget(['today_order_count']);
        session()->forget(['active_waiter_requests_count']);

        $onboardingSteps = OnboardingStep::where('branch_id', $branch->id)->first();

        if (
            $onboardingSteps
            && (
                !$onboardingSteps->add_area_completed
                || !$onboardingSteps->add_table_completed
                || !$onboardingSteps->add_menu_completed
                || !$onboardingSteps->add_menu_items_completed
            )
        ) {
            return $this->redirect(RouteServiceProvider::ONBOARDING_STEPS, navigate: true);
        } else {
            $this->js('window.location.reload()');
        }
    }

    public function render()
    {
        return view('livewire.forms.change-branch');
    }
}
