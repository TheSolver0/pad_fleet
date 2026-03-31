<?php

namespace App\Livewire\Portal\Mechanics;

use App\Models\Mechanic;
use App\Models\Repair;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MyWork extends Component
{
    use WithPagination;

    public function render(): View
    {
        $mechanic = Mechanic::where('user_id', Auth::id())->first();

        $repairs = collect();
        if ($mechanic) {
            $repairs = Repair::with(['vehicle:id,registration', 'garage:id,name'])
                ->where('mechanic_id', $mechanic->id)
                ->orderByDesc('created_at')
                ->paginate(15);
        }

        return view('livewire.portal.mechanics.my-work', [
            'mechanic' => $mechanic,
            'repairs' => $repairs,
        ])->layout('layouts.app', ['title' => 'Mes travaux mécanicien']);
    }
}
