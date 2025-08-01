<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    //Admin: List all students with user & teacher info
    public function index()
    {
        return response()->json(Student::with(['user', 'assignedTeacher.user'])->get());
    }

    //Admin: Show single student
    public function show($id)
    {
        $student = Student::with(['user', 'assignedTeacher.user'])->findOrFail($id);
        return response()->json($student);
    }

    //Admin: Update student (not creating user here)
    public function update(Request $request, $id)
    {
        $student = Student::with('user')->findOrFail($id);

        $student->user->update($request->only(['first_name', 'last_name', 'email', 'username']));
        $student->update($request->only(['phone', 'roll_number', 'student_class', 'date_of_birth', 'admission_date', 'status', 'assigned_teacher_id']));

        return response()->json(['message' => 'Student updated successfully', 'student' => $student->load(['user', 'assignedTeacher.user'])]);
    }

    //Admin: Delete student and linked user
    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->user->delete();
        $student->delete();

        return response()->json(['message' => 'Student deleted successfully']);
    }

    //Teacher: View only students assigned to them
    public function myStudents(Request $request)
{
    $user = $request->user(); // logged-in teacher
    $teacher = \App\Models\Teacher::where('user_id', $user->id)->first();

    if (!$teacher) {
        return response()->json(['message' => 'Teacher profile not found'], 404);
    }

    $students = \App\Models\Student::with('user')
                ->where('assigned_teacher_id', $teacher->id)
                ->get();

    return response()->json($students);
}

    //Student: View own profile
    public function myProfile(Request $request)
    {
        $user = $request->user();
        $student = Student::with(['user', 'assignedTeacher.user'])->where('user_id', $user->id)->first();

        if (!$student) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        return response()->json($student);
    }
}
