<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;

    protected $fillable = ['form_id', 'type', 'name', 'label', 'validation_rules', 'send_in_email', 'options'];

    // Define a relationship back to the Form model
    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id');
    }
}

