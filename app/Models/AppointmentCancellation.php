<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class AppointmentCancellation extends Model
{
    use HasFactory;
    protected $fillable = [
        'appointment_id',
        'reason',
        'cancelled_at'
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}