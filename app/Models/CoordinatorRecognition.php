<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class CoordinatorRecognition extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'coordinator_recognition';

    protected $primaryKey = 'coordinator_id';

    protected $fillable = [
        'coordinator_id', 'recognition0', 'recognition1', 'recognition2', 'recognition3', 'recognition4', 'recognition5', 'recognition6', 'recognition7', 'recognition8', 'recognition9',
        'year0', 'year1', 'year2', 'year3', 'year4', 'year5', 'year6', 'year7', 'year8', 'year9', 'recognition_toptier', 'recognition_necklace',
    ];

    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(Coordinators::class, 'coordinator_id', 'id');  // 'coordinator_id' in coordinator_tree BelongsTo 'id' in coordinators
    }

    public function recognitionGift0(): BelongsTo
    {
        return $this->belongsTo(RecognitionGifts::class, 'recognition0', 'id');  // 'recognition0' in coordinator_recognition BelongsTo 'id' in recognition_gifts
    }

    public function recognitionGift1(): BelongsTo
    {
        return $this->belongsTo(RecognitionGifts::class, 'recognition1', 'id');  // 'recognition1' in coordinator_recognition BelongsTo 'id' in recognition_gifts
    }

    public function recognitionGift2(): BelongsTo
    {
        return $this->belongsTo(RecognitionGifts::class, 'recognition2', 'id');  // 'recognition2' in coordinator_recognition BelongsTo 'id' in recognition_gifts
    }

    public function recognitionGift3(): BelongsTo
    {
        return $this->belongsTo(RecognitionGifts::class, 'recognition3', 'id');  // 'recognition3' in coordinator_recognition BelongsTo 'id' in recognition_gifts
    }

    public function recognitionGift4(): BelongsTo
    {
        return $this->belongsTo(RecognitionGifts::class, 'recognition4', 'id');  // 'recognition4' in coordinator_recognition BelongsTo 'id' in recognition_gifts
    }

    public function recognitionGift5(): BelongsTo
    {
        return $this->belongsTo(RecognitionGifts::class, 'recognition5', 'id');  // 'recognition5' in coordinator_recognition BelongsTo 'id' in recognition_gifts
    }

    public function recognitionGift6(): BelongsTo
    {
        return $this->belongsTo(RecognitionGifts::class, 'recognition6', 'id');  // 'recognition6' in coordinator_recognition BelongsTo 'id' in recognition_gifts
    }

    public function recognitionGift7(): BelongsTo
    {
        return $this->belongsTo(RecognitionGifts::class, 'recognition7', 'id');  // 'recognition7' in coordinator_recognition BelongsTo 'id' in recognition_gifts
    }

    public function recognitionGift8(): BelongsTo
    {
        return $this->belongsTo(RecognitionGifts::class, 'recognition8', 'id');  // 'recognition8' in coordinator_recognition BelongsTo 'id' in recognition_gifts
    }

    public function recognitionGift9(): BelongsTo
    {
        return $this->belongsTo(RecognitionGifts::class, 'recognition9', 'id');  // 'recognition9' in coordinator_recognition BelongsTo 'id' in recognition_gifts
    }
}
