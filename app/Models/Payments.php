<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Payments extends Model
{
    use HasFactory;
    use Notifiable;

    protected $primaryKey = 'chapter_id';

    protected $fillable = [
        'chapter_id',
        'rereg_members',
        'rereg_payment',
        'rereg_date',
        'rereg_invoice',
        'rereg_notes',
        'm2m_payment',
        'm2m_date',
        'sustaining_donation',
        'sustaining_date',
        'donation_invoice',
        'created_at',
        'updated_at',
    ];

}
