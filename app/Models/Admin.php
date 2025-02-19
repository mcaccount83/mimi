<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $table = 'admin';

    protected $primaryKey = 'id';

    protected $fillable = [
        'fiscal_year', 'display_testing', 'display_live', 'update_user_tables',
        'subscribe_list', 'unsubscribe_list', 'reset_AFTER_testing',
        'created_at', 'updated_id', 'updated_at',
    ];
}
