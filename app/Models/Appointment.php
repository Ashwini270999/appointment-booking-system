<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Appointment extends Model
{
    use HasFactory;
    protected $fillable = [
        'reference_no',
        'doctor_id',
        'patient_id',
        'appointment_date',
        'start_time',
        'end_time',
        'status'
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function cancellation()
    {
        return $this->hasOne(AppointmentCancellation::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}