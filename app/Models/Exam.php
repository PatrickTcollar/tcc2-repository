<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Importado
use Illuminate\Database\Eloquent\Relations\HasOne;    // Importado

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'file_path',
        'original_filename',
        'upload_date',
    ];

    protected $casts = [
        'upload_date' => 'datetime', // CORREÇÃO AQUI: De 'date' para 'datetime'
    ];

    /**
     * Define o relacionamento BelongsTo com o Patient (um exame pertence a um paciente).
     * @return BelongsTo
     */
    public function patient(): BelongsTo
    {
        // Explicitamos as chaves, embora 'patient_id' seja o padrão.
        return $this->belongsTo(Patient::class, 'patient_id', 'id');
    }

    /**
     * Define o relacionamento HasOne com o Report (um exame tem um laudo).
     * @return HasOne
     */
    public function report(): HasOne
    {
        // Explicitamos as chaves, embora 'exam_id' seja o padrão.
        return $this->hasOne(Report::class, 'exam_id', 'id');
    }
}
