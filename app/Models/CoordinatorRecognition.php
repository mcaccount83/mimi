<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoordinatorRecognition extends Model
{
    protected $table = 'coordinator_recognition';

    protected $primaryKey = 'coordinator_id';

    protected $guarded = []; // ALL columns are mass-assignable

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
