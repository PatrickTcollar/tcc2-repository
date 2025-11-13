<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // Importe o HasMany

class Patient extends Model
{
    use HasFactory;

    /**
     * Os atributos que são preenchíveis (mass assignable).
     * Garantido que 'user_id' está incluído.
     */
    protected $fillable = [
        'name',
        'birth_date',
        'gender',
        'user_id',
    ];

    /**
     * Os atributos que devem ser convertidos (cast) para tipos nativos.
     */
    protected $casts = [
        'birth_date' => 'date',
    ];

    /**
     * Define o relacionamento BelongsTo com o User (usuário criador).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define o relacionamento HasMany com o Exam (um paciente tem muitos exames).
     * @return HasMany
     */
    public function exams(): HasMany
    {
        // Assume que a chave estrangeira na tabela 'exams' é 'patient_id'
        return $this->hasMany(Exam::class);
    }
}
