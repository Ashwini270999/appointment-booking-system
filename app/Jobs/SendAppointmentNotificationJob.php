<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendAppointmentNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Appointment $appointment
    ) {}

    public function handle(): void
    {
        \Log::info(
            'Appointment notification sent for appointment ID: '
            . $this->appointment->id
        );

        Notification::where(
            'appointment_id',
            $this->appointment->id
        )->update([
         'is_sent' => true
      ]);
    }
}