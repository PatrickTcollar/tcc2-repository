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
        'patient_id', // Certifique-se de que este campo exista na sua tabela e está aqui
        'report_content',
        'generation_date',
    ];

    protected $casts = [
        'generation_date' => 'datetime',
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
