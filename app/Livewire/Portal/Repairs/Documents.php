<?php

namespace App\Livewire\Portal\Repairs;

use App\Models\Repair;
use App\Models\RepairDocument;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Documents extends Component
{
    use WithPagination;
    use WithFileUploads;

    public string $search = '';
    public string $status_filter = '';
    public bool $showFormModal = false;
    public bool $showValidationModal = false;
    public ?int $editingId = null;
    public ?int $repair_id = null;

    public string $document_type = RepairDocument::TYPE_DIAGNOSTIC;
    public $document_file = null;
    public string $title = '';
    public string $description = '';
    public string $validation_notes = '';
    public bool $physically_validated = false;
    public bool $digitally_validated = false;
    public ?int $validated_by = null;
    public string $validated_at = '';

    protected $queryString = ['search' => ['except' => ''], 'status_filter' => ['except' => '']];

    protected function rules(): array
    {
        return [
            'repair_id' => 'required|exists:repairs,id',
            'document_type' => 'required|in:diagnostic,estimate,invoice,technical_report,other',
            'document_file' => 'required|file|max:10240', // 10Mo
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'validation_notes' => 'nullable|string',
        ];
    }

    public function openCreate(?int $repairId = null): void
    {
        $this->resetForm();
        $this->repair_id = $repairId;
        $this->showFormModal = true;
    }

    public function saveDocument(): void
    {
        $this->validate();
        
        $repair = Repair::findOrFail($this->repair_id);
        
        // Stocker le fichier
        $filePath = $this->document_file->store('repair-documents', 'public');
        
        $document = RepairDocument::create([
            'repair_id' => $this->repair_id,
            'document_type' => $this->document_type,
            'title' => $this->title,
            'description' => $this->description ?: null,
            'file_path' => $filePath,
            'original_filename' => $this->document_file->getClientOriginalName(),
            'file_size' => $this->document_file->getSize(),
            'mime_type' => $this->document_file->getMimeType(),
            'physically_validated' => false,
            'digitally_validated' => false,
        ]);

        $this->dispatch('notify', type: 'success', message: 'Document ajouté avec succès.');
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function openValidation(int $documentId): void
    {
        $document = RepairDocument::findOrFail($documentId);
        $this->editingId = $documentId;
        $this->validation_notes = $document->validation_notes ?? '';
        $this->physically_validated = $document->physically_validated;
        $this->digitally_validated = $document->digitally_validated;
        $this->showValidationModal = true;
    }

    public function saveValidation(): void
    {
        $document = RepairDocument::findOrFail($this->editingId);
        
        $data = [
            'validation_notes' => $this->validation_notes ?: null,
        ];

        // Validation physique
        if ($this->physically_validated && !$document->physically_validated) {
            $data['physically_validated'] = true;
            $data['physically_validated_by'] = Auth::id();
            $data['physically_validated_at'] = now();
        }

        // Validation digitale
        if ($this->digitally_validated && !$document->digitally_validated) {
            $data['digitally_validated'] = true;
            $data['digitally_validated_by'] = Auth::id();
            $data['digitally_validated_at'] = now();
        }

        $document->update($data);

        $this->dispatch('notify', type: 'success', message: 'Validation mise à jour.');
        $this->showValidationModal = false;
        $this->editingId = null;
    }

    public function downloadDocument(int $documentId): void
    {
        $document = RepairDocument::findOrFail($documentId);
        // La redirection sera gérée par le controller
        $this->dispatch('download-document', documentId: $documentId);
    }

    public function deleteDocument(int $documentId): void
    {
        $document = RepairDocument::findOrFail($documentId);
        
        // Supprimer le fichier
        if (file_exists(storage_path('app/public/' . $document->file_path))) {
            unlink(storage_path('app/public/' . $document->file_path));
        }
        
        $document->delete();
        $this->dispatch('notify', type: 'success', message: 'Document supprimé.');
    }

    public function getDocumentTypeLabel(string $type): string
    {
        return match($type) {
            RepairDocument::TYPE_DIAGNOSTIC => 'Diagnostic',
            RepairDocument::TYPE_ESTIMATE => 'Devis',
            RepairDocument::TYPE_INVOICE => 'Facture',
            RepairDocument::TYPE_TECHNICAL_REPORT => 'Rapport technique',
            RepairDocument::TYPE_OTHER => 'Autre',
            default => $type,
        };
    }

    public function getValidationStatus(RepairDocument $document): string
    {
        if ($document->digitally_validated) {
            return '<span class="badge bg-success">Validé (numérique)</span>';
        } elseif ($document->physically_validated) {
            return '<span class="badge bg-warning">Validé (physique)</span>';
        } else {
            return '<span class="badge bg-secondary">En attente</span>';
        }
    }

    private function resetForm(): void
    {
        $this->repair_id = null;
        $this->document_type = RepairDocument::TYPE_DIAGNOSTIC;
        $this->document_file = null;
        $this->title = '';
        $this->description = '';
        $this->validation_notes = '';
        $this->physically_validated = false;
        $this->digitally_validated = false;
        $this->editingId = null;
        $this->resetValidation();
    }

    public function render(): View
    {
        $query = RepairDocument::with(['repair.vehicle', 'physicallyValidatedBy', 'digitallyValidatedBy'])
            ->orderBy('created_at', 'desc');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('repair', function ($q2) {
                        $q2->where('id', 'like', '%' . $this->search . '%')
                            ->orWhereHas('vehicle', function ($q3) {
                                $q3->where('registration', 'like', '%' . $this->search . '%');
                            });
                    });
            });
        }

        if ($this->status_filter !== '') {
            match($this->status_filter) {
                'pending' => $query->where('physically_validated', false)->where('digitally_validated', false),
                'physical' => $query->where('physically_validated', true)->where('digitally_validated', false),
                'digital' => $query->where('digitally_validated', true),
                default => null,
            };
        }

        $documents = $query->paginate(20);

        $repairs = Repair::with('vehicle:id,registration')
            ->orderByDesc('created_at')
            ->limit(300)
            ->get(['id', 'vehicle_id', 'created_at']);

        return view('livewire.portal.repairs.documents', [
            'documents' => $documents,
            'repairs'   => $repairs,
        ]);
    }
}
