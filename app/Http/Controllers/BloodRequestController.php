<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BloodRequestController extends Controller
{
    /**
     * Show the blood request form.
     *
     * @return \Illuminate\View\View
     */
    public function showForm()
    {
        $bloodGroups = collect([
            (object) ['id' => 1, 'group_name' => 'A+'],
            (object) ['id' => 2, 'group_name' => 'A-'],
            (object) ['id' => 3, 'group_name' => 'B+'],
            (object) ['id' => 4, 'group_name' => 'B-'],
            (object) ['id' => 5, 'group_name' => 'O+'],
            (object) ['id' => 6, 'group_name' => 'O-'],
            (object) ['id' => 7, 'group_name' => 'AB+'],
            (object) ['id' => 8, 'group_name' => 'AB-'],
        ]);

        return view('blood-request', compact('bloodGroups'));
    }

    /**
     * Handle the submission of the blood request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitForm(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile_number' => 'nullable|string|max:20',
            'email' => 'required|string|email|max:255',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date',
            'address' => 'nullable|string',
            'hospital_name' => 'required|string|max:255',
            'blood_group_id' => 'required',
            'units_requested' => 'required|integer|min:1',
            'urgency_level' => 'required|in:routine,urgent,emergency',
            'required_by_date' => 'nullable|date|after_or_equal:today',
            'description' => 'nullable|string',
        ]);

        return redirect()
            ->back()
            ->with('success', 'Blood request submitted successfully. Database saving will be connected in the next version.');
    }
}