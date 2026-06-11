<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    protected $fillable = ['name', 'cnpj', 'email', 'phone', 'address'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
