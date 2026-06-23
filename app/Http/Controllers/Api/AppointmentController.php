<?php

namespace App\Http\Controllers\Api;
use App\Repositories\AppointmentRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookAppointmentRequest;
use App\Http\Requests\CancelAppointmentRequest;
use App\Http\Requests\RescheduleAppointmentRequest;
use App\Models\DoctorAvailability;
use App\Models\AppointmentCancellation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Events\AppointmentBooked;

class AppointmentController extends Controller
{
    protected AppointmentRepository $appointmentRepository;

    public function __construct(AppointmentRepository $appointmentRepository)
    {
        $this->appointmentRepository = $appointmentRepository;
    }

    public function book(BookAppointmentRequest $request)
    {
        return DB::transaction(function () use ($request) {

        $availability = DoctorAvailability::where(
            'doctor_id',
            $request->doctor_id
        )
        ->where(
            'date',
            $request->appointment_date
        )
        ->first();

        if (!$availability) {

            return response()->json([
                'message' => 'Doctor is not available on selected date'
            ], 422);
        }

        $slots = [];

        $start = Carbon::parse(
            $availability->start_time
        );

        $end = Carbon::parse(
            $availability->end_time
        );

        while ($start < $end) {

            $slots[] = $start->format('H:i');

            $start->addMinutes(
                $availability->slot_duration
            );
        }
        if (!in_array($request->start_time,$slots)) 
        {
            return response()->json([
                'message' => 'Selected slot is not available'
            ], 422);
        }

        $appointmentExists = $this->appointmentRepository->isSlotBooked(
            $request->doctor_id,
            $request->appointment_date,
            $request->start_time
        );

            if ($appointmentExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Slot already booked'
                ], 422);
            }


            $appointment = $this->appointmentRepository->create([
                'reference_no'     => 'APT-' . time(),
                'doctor_id'        => $request->doctor_id,
                'patient_id'       => $request->patient_id,
                'appointment_date' => $request->appointment_date,
                'start_time'       => $request->start_time,
                'end_time'         => Carbon::parse($request->start_time)
                                      ->addMinutes($availability->slot_duration)
                                      ->format('H:i:s'),
                'status'           => 'booked'
            ]);

            event(new AppointmentBooked($appointment));

            return response()->json([
                'success'      => true,
                'message'      => 'Appointment booked successfully',
                'reference_no' => $appointment->reference_no,
                'data'         => $appointment
            ], 201);
        });
    }

    public function cancel(CancelAppointmentRequest $request,Appointment $appointment)
    {
        if ($appointment->status === 'cancelled') {

            return response()->json([
                'message' => 'Appointment already cancelled'
            ], 422);
        }

        $appointment->update([
            'status' => 'cancelled'
        ]);

        AppointmentCancellation::create([
            'appointment_id' => $appointment->id,
            'reason' => $request->reason,
            'cancelled_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Appointment cancelled successfully'
        ]);
    }

    public function reschedule(RescheduleAppointmentRequest $request,Appointment $appointment)
    {
        $availability = DoctorAvailability::where(
            'doctor_id',
            $appointment->doctor_id
        )
        ->where(
            'date',
            $request->appointment_date
        )
        ->first();

        if (!$availability) {

            return response()->json([
                'message' => 'Doctor is not available on selected date'
            ], 422);
        }

        $slots = [];

        $start = Carbon::parse(
            $availability->start_time
        );

        $end = Carbon::parse(
            $availability->end_time
        );

        while ($start < $end) {

            $slots[] = $start->format('H:i');

            $start->addMinutes(
                $availability->slot_duration
            );
        }

        if (!in_array($request->start_time,$slots)) 
        {
            return response()->json([
                'message' => 'Selected slot is not available'
            ], 422);
        }

        $slotExists = $this->appointmentRepository->isSlotBookedForReschedule(
            $appointment->doctor_id,
            $request->appointment_date,
            $request->start_time,
            $appointment->id
        );

        if ($slotExists) {
            return response()->json([
                'message' => 'Requested slot is already booked'
            ], 422);
        }

        $appointment->update([
            'appointment_date' => $request->appointment_date,
            'start_time'       => $request->start_time,
            'end_time'         => Carbon::parse($request->start_time)
                                  ->addMinutes($availability->slot_duration)
                                  ->format('H:i:s')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Appointment rescheduled successfully',
            'data' => $appointment->fresh()
        ]);
    }
}
