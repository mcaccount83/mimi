<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailFields extends Model
{
    use HasFactory;

    protected $table = 'email_fields';

    protected $primaryKey = 'id';

    protected $fillable = [
        'toEmail', 'founder_first_name', 'founder_last_name', 'boundary_details',
        'name_details', 'created_at', 'updated_at',
    ];
}
