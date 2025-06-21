<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HourlyRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_type_id',
        'day_of_week',
        'start_time',
        'end_time',
        'price_per_hour',
    ];

    /**
     * Get the field type that owns the rate.
     */
    public function fieldType()
    {
        return $this->belongsTo(FieldType::class);
    }
}