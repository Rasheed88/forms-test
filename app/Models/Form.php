<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Define a one-to-many relationship with the Field model
    public function fields()
    {
        return $this->hasMany(Field::class, 'form_id');
    }
}

