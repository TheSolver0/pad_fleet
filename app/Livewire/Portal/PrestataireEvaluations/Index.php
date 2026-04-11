<?php

namespace App\Livewire\Portal\PrestataireEvaluations;

use App\Models\Garage;
use App\Models\PrestataireEvaluation;
use App\Models\Supplier;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $type_filter = ''; // '', 'Supplier', 'Garage'
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;

    public string $evaluable_type = 'Supplier';
    public ?int $evaluable_id = null;
    public string $quality_score = '3';
    public string $delivery_score = '';
    public string $reputation_score = '';
    public string $comment = '';
    public string $context = '';
    public string $evaluated_at = '';

    protected $queryString = ['search' => ['except' => ''], 'type_filter' => ['except' => '']];
    protected $paginationTheme = 'bootstrap'; 

    public function mount(): void
    {
        if ($this->evaluated_at === '') {
            $this->evaluated_at = now()->format('Y-m-d');
        }
        $type = request()->query('evaluable_type');
        $id = request()->query('evaluable_id');
        if (in_array($type, ['Supplier', 'Garage'], true) && $id) {
            $this->evaluable_type = $type;
            $this->evaluable_id = (int) $id;
            $this->showFormModal = true;
        }
    }

    protected function rules(): array
    {
        $table = $this->evaluable_type === 'Supplier' ? 'suppliers' : 'garages';
        return [
            'evaluable_type' => 'required|in:Supplier,Garage',
            'evaluable_id' => ['required', 'integer', Rule::exists($table, 'id')],
            'quality_score' => 'required|numeric|min:1|max:5',
            'delivery_score' => 'nullable|numeric|min:1|max:5',
            'reputation_score' => 'nullable|numeric|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
            'context' => 'nullable|string|max:255',
            'evaluated_at' => 'required|date',
        ];
    }

    public function updatedEvaluableType(): void
    {
        $this->evaluable_id = null;
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->evaluated_at = now()->format('Y-m-d');
        $this->showFormModal = true;
    }

    public function openEdit(int $id): void
    {
        $e = PrestataireEvaluation::findOrFail($id);
        $this->editingId = $e->id;
        $this->evaluable_type = class_basename($e->evaluable_type);
        $this->evaluable_id = $e->evaluable_id;
        $this->quality_score = (string) $e->quality_score;
        $this->delivery_score = $e->delivery_score !== null ? (string) $e->delivery_score : '';
        $this->reputation_score = $e->reputation_score !== null ? (string) $e->reputation_score : '';
        $this->comment = $e->comment ?? '';
        $this->context = $e->context ?? '';
        $this->evaluated_at = $e->evaluated_at->format('Y-m-d');
        $this->showFormModal = true;
    }

    public function saveEvaluation(): void
    {
        $this->validate();
        $fullType = $this->evaluable_type === 'Supplier' ? Supplier::class : Garage::class;
        $data = [
            'evaluable_type' => $fullType,
            'evaluable_id' => $this->evaluable_id,
            'quality_score' => (float) $this->quality_score,
            'delivery_score' => $this->delivery_score !== '' ? (float) $this->delivery_score : null,
            'reputation_score' => $this->reputation_score !== '' ? (float) $this->reputation_score : null,
            'comment' => $this->comment ?: null,
            'context' => $this->context ?: null,
            'evaluated_at' => $this->evaluated_at,
            'user_id' => auth()->id(),
        ];
        if ($this->editingId) {
            PrestataireEvaluation::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Évaluation mise à jour.');
        } else {
            PrestataireEvaluation::create($data);
            $this->dispatch('notify', type: 'success', message: 'Évaluation enregistrée.');
        }
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteEvaluation(): void
    {
        if ($this->editingId) {
            PrestataireEvaluation::findOrFail($this->editingId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Évaluation supprimée.');
        }
        $this->showDeleteModal = false;
        $this->editingId = null;
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->evaluable_type = 'Supplier';
        $this->evaluable_id = null;
        $this->quality_score = '3';
        $this->delivery_score = '';
        $this->reputation_score = '';
        $this->comment = '';
        $this->context = '';
        $this->evaluated_at = now()->format('Y-m-d');
        $this->editingId = null;
        $this->resetValidation();
    }

    public function getEvaluableOptionsProperty(): \Illuminate\Support\Collection
    {
        if ($this->evaluable_type === 'Supplier') {
            return Supplier::orderBy('name')->get(['id', 'name'])->mapWithKeys(fn ($s) => [$s->id => $s->name]);
        }
        return Garage::orderBy('name')->get(['id', 'name'])->mapWithKeys(fn ($g) => [$g->id => $g->name]);
    }

    public function render(): View
    {
        $query = PrestataireEvaluation::query()->with(['evaluable', 'user:id,name']);

        if ($this->type_filter !== '') {
            $query->where('evaluable_type', $this->type_filter === 'supplier' ? Supplier::class : Garage::class);
        }
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('comment', 'like', '%' . $this->search . '%')
                    ->orWhere('context', 'like', '%' . $this->search . '%')
                    ->orWhereHasMorph('evaluable', [Supplier::class, Garage::class], function ($m, $type) {
                        $m->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        $evaluations = $query->orderByDesc('evaluated_at')->orderByDesc('id')->paginate(15);

        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);
        $garages = Garage::orderBy('name')->get(['id', 'name']);

        return view('livewire.portal.prestataire-evaluations.index', [
            'evaluations' => $evaluations,
            'suppliers' => $suppliers,
            'garages' => $garages,
        ])->layout('layouts.app', ['title' => 'Évaluations prestataires']);
    }
}
