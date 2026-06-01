<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;

#[Table('recognition_gifts', 'id')]
#[Unguarded]
class RecognitionGifts extends Model {}
