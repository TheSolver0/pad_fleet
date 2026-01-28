<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;

class InsuranceContractGlobalDocument extends Model
{
    protected $table = 'insurance_contract_global_documents';

    protected $fillable = ['insurance_contract_global_id', 'file_path', 'original_name'];

    public function insuranceContractGlobal(): BelongsTo
    {
        return $this->belongsTo(InsuranceContractGlobal::class, 'insurance_contract_global_id');
    }

    public static function storeUpload(InsuranceContractGlobal $contract, UploadedFile $file): self
    {
        $path = $file->store('insurance-global/' . $contract->id, 'public');
        return $contract->documents()->create([
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }
}
