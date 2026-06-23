<?php

namespace App\Listeners;

use App\Events\AppointmentBooked;
use App\Models\Notification;
use App\Jobs\SendAppointmentNotificationJob;

class CreateAppointmentNotification
{
    public function handle(AppointmentBooked $event): void
    {
        Notification::create([
            'appointment_id' => $event->appointment->id,
            'type'           => 'booking',
            'message'        => 'Appointment booked successfully',
            'is_sent'        => false
        ]);

        SendAppointmentNotificationJob::dispatch(
            $event->appointment
        );
    }
}