<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    // Admin: List all teachers with user info

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $teacher = Teacher::with('user')->paginate($perPage);
        return response()->json($teacher);
    }

    // Admin: Show single teacher
    public function show($id)
    {
        $teacher = Teacher::with('user')->findOrFail($id);
        return response()->json($teacher);
    }

    // Admin: Update teacher details (not creating user here)
    public function update(Request $request, $id)
    {
        $teacher = Teacher::with('user')->findOrFail($id);
        $request->validate([
        'username' => ['sometimes','min:3',Rule::unique('users', 'username')->ignore($teacher->user_id),],
        'first_name' => ['sometimes','min:2','regex:/^[A-Za-z ]+$/',],
        'last_name' => ['nullable','regex:/^[A-Za-z ]+$/',],
        'email' => ['sometimes','email',Rule::unique('users', 'email')->ignore($teacher->user_id),],
        'phone' => ['sometimes','digits:10',],
        'employee_id'=> ['sometimes','numeric',Rule::unique('teachers', 'employee_id')->ignore($teacher->id),],        
        'status'     => 'sometimes|in:active,inactive',
        'subject_specialization' => 'sometimes|alpha',
        'date_of_joining' => 'sometimes|date',
        ]);

        $teacher->user->update($request->only(['first_name', 'last_name', 'email', 'username']));
        $teacher->update($request->only(['phone', 'subject_specialization', 'employee_id', 'date_of_joining', 'status']));

 return response()->json([
    'message' => 'Teacher updated successfully',
    'teacher' => $teacher->load(['user']),
]);

    }

    // Admin: Delete teacher and linked user
    public function destroy($id)
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->user->delete(); // deletes linked user
        $teacher->delete();

        return response()->json(['message' => 'Teacher deleted successfully']);
    }

    // Teacher: View own profile
    public function myProfile(Request $request)
    {       
        $user = $request->user();
        $teacher = Teacher::with('user')->where('user_id', $user->id)->first();
        if (!$teacher) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        return response()->json($teacher);
    }
}
