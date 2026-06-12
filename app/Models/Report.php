<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Importe BelongsTo

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'patient_id',
        'report_content',
        'generation_date',
        'signed_by',
        'signer_crf',
        'signed_at',
        'signature_image',
    ];

    protected $casts = [
        'generation_date' => 'datetime',
        'signed_at' => 'datetime',
    ];

    /**
     * Um laudo pertence a um Exame (se for um laudo de exame único).
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Um laudo pertence a um Paciente (se for um laudo de evolução ou de exame único).
     * Essa relação é usada para buscar o nome do paciente mesmo se exam_id for nulo.
     */
    public function patient(): BelongsTo
    {
        // Usa o campo 'patient_id' da tabela 'reports' para se ligar à chave primária do modelo 'Patient'.
        return $this->belongsTo(Patient::class, 'patient_id', 'id');
    }
}
