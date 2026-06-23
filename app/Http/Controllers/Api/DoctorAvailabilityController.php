<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAvailabilityRequest;
use App\Models\DoctorAvailability;
use App\Models\Doctor;
use App\Models\Appointment;
use Carbon\Carbon;

class DoctorAvailabilityController extends Controller
{
    public function store(StoreAvailabilityRequest $request)
    {
        $availability = DoctorAvailability::create(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Availability created successfully',
            'data' => $availability
        ], 201);
    }

    public function availableSlots(Request $request, Doctor $doctor)
    {
        $date = $request->date;

        $availability = $doctor->availabilities()
            ->where('date', $date)
            ->first();

        if (!$availability) {
            return response()->json([
                'message' => 'No availability found'
            ], 404);
        }

        $slots = [];

        $start = Carbon::parse($availability->start_time);
        $end = Carbon::parse($availability->end_time);

        while ($start < $end) {

            $slots[] = $start->format('H:i');

            $start->addMinutes(
                $availability->slot_duration
            );
        }

        $bookedSlots = Appointment::where('doctor_id', $doctor->id)
    ->where('appointment_date', $date)
    ->where('status', 'booked')
    ->pluck('start_time')
    ->map(function ($time) {
        return Carbon::parse($time)->format('H:i');
    })
    ->toArray();

$availableSlots = array_values(
    array_diff($slots, $bookedSlots)
);
return response()->json([
    'doctor_id' => $doctor->id,
    'date' => $date,
    'slots' => $availableSlots
]);
    }
}