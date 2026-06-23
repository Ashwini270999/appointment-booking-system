<?php

namespace App\Repositories;

use App\Models\Appointment;

class AppointmentRepository
{
    public function isSlotBooked(int $doctorId,string $appointmentDate,string $startTime,
        ?int $excludeAppointmentId = null): bool 
        {

            $query = Appointment::where(
                'doctor_id',
                $doctorId
            )
            ->where(
                'appointment_date',
                $appointmentDate
            )
            ->where(
                'start_time',
                $startTime
            )
            ->where(
                'status',
                'booked'
            );

            if ($excludeAppointmentId) {
                $query->where(
                    'id',
                    '!=',
                    $excludeAppointmentId
                );
            }

            return $query->exists();
        }

    public function create(array $data): Appointment 
    {

        return Appointment::create(
            $data
        );
    }

    public function update(Appointment $appointment,array $data): bool 
    {

        return $appointment->update(
            $data
        );
    }

    public function isSlotBookedForReschedule(int $doctorId,string $appointmentDate,string $startTime,
    int $appointmentId): bool 
    {
        return Appointment::where(
            'doctor_id',
            $doctorId
        )
        ->where(
            'appointment_date',
            $appointmentDate
        )
        ->where(
            'start_time',
            $startTime
        )
        ->where(
            'status',
            'booked'
        )
        ->where(
            'id',
            '!=',
            $appointmentId
        )
        ->exists();
    }
}