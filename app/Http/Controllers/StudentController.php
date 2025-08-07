<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class StudentController extends Controller
{
    //Admin: List all students with user & teacher info
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $students = Student::with(['user', 'assignedTeacher.user'])->paginate($perPage);
        return response()->json($students);
    }

    //Admin: Show single student
    public function show($id)
    {
        $student = Student::with(['user', 'assignedTeacher.user'])->findOrFail($id);
        return response()->json($student);
    }

    //Admin: Update student
    public function update(Request $request, $id)
{
    $student = Student::with('user')->findOrFail($id);

    $request->validate([
        'username' => ['sometimes','min:3',Rule::unique('users', 'username')->ignore($student->user_id),],
        'first_name' => ['sometimes','min:2','regex:/^[A-Za-z ]+$/',],
        'last_name' => ['nullable','regex:/^[A-Za-z ]+$/',],
        'email' => ['sometimes','email',Rule::unique('users', 'email')->ignore($student->user_id),],
        'phone' => ['sometimes','digits:10',],
        'roll_number' => ['sometimes','numeric','min:1',Rule::unique('students', 'roll_number')->ignore($student->id),],
        'student_class' => ['sometimes','numeric',],
        'date_of_birth' => ['sometimes','date',],
        'admission_date' => ['sometimes','date',],
        'assigned_teacher_id' => ['nullable','exists:teachers,id',],
    ]);

    // Update user and student records
    $student->user->update($request->only(['first_name', 'last_name', 'email', 'username']));
    $student->update($request->only([
        'phone', 'roll_number', 'student_class', 'date_of_birth',
        'admission_date', 'status', 'assigned_teacher_id'
    ]));

    return response()->json([
        'message' => 'Student updated successfully',
        'student' => $student->load(['user', 'assignedTeacher.user'])
    ]);
}

    //Admin: Delete student and linked user
    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->user->delete();
        $student->delete();
        return response()->json(['message' => 'Student deleted successfully']);
    }


    // Admin: View students under a specific teacher
    public function studentsByTeacher($teacherId, Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $students = Student::with('user')
            ->where('assigned_teacher_id', $teacherId)
            ->paginate($perPage);

        return response()->json($students);
    }

    //Teacher: View only students assigned to them (paginated)
    public function myStudents(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $user = $request->user(); 
        $teacher = \App\Models\Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return response()->json(['message' => 'Teacher profile not found'], 404);
        }

        $students = Student::with('user')
            ->where('assigned_teacher_id', $teacher->id)
            ->paginate($perPage);

        return response()->json($students);
    }

    //Student: View own profile
    public function myProfile(Request $request)
    {
        $user = $request->user();
        $student = Student::with(['user'])->where('user_id', $user->id)->first();

        if (!$student) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        return response()->json($student);
    }
}


