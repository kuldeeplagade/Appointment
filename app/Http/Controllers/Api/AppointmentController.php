<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Appointment;
use App\Helpers\ApiResponse;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'date' => ['required', 'date', 'after_or_equal:today'],
            'slots' => ['required', 'string'],
        ]);
    
        if ($validator->fails()) {
            return ApiResponse::error(
                'Validation errors occurred',
                400,
                $validator->messages()
            );
        }
    
        $authenticatedUserId = auth()->id(); // Get the authenticated user ID
    
        // Check for existing slots on the same date
        $existingAppointment = Appointment::where('date', $request->date)
            ->where('slots', $request->slots)
            ->exists();
    
        if ($existingAppointment) {
            return ApiResponse::error('The selected slot is already booked for the given date.',409);
        }
    
        // Create the appointment
        $appointment = Appointment::create([
            'user_id' => $authenticatedUserId,
            'date' => $request->date,
            'slots' => $request->slots,
            'status' => 'Pending',
        ]);

        return ApiResponse::success('Appointment created successfully',[
            'appointment' => $appointment,
        ],201);
    }
    
    
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        // Validate the request input
        $request->validate([
            'status' => [
                'required',
                Rule::in(['Pending', 'Confirmed', 'Cancelled']),
            ],
        ]);

        // Check if the authenticated user is an admin
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Access denied.'], 403);
        }

        // Update the status
        $appointment->status = $request->status;
        $appointment->save();

        // Return the updated appointment details
        return response()->json([
            'message' => 'Appointment status updated successfully.',
            'appointment' => $appointment,
        ]);
    }

}
