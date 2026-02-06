<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Student;
use App\Models\GradeLevel;
use App\Models\Section;
use App\Models\AcademicYear;
use App\Models\Transaction;
use App\Models\Fine;
use App\Models\ActivityLog;

class StudentController extends Controller
{
    /**
     * Display a listing of students.
     */
    public function index(Request $request)
    {
        $query = Student::with(['gradeLevel', 'section']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('student_no', 'like', "%{$search}%")
                  ->orWhere('library_card_no', 'like', "%{$search}%")
                  ->orWhere('lrn', 'like', "%{$search}%");
            });
        }

        // Filter by grade level
        if ($request->filled('grade_level')) {
            $query->where('grade_level_id', $request->grade_level);
        }

        // Filter by section
        if ($request->filled('section')) {
            $query->where('section_id', $request->section);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $students    = $query->latest()->paginate(15)->withQueryString();
        $gradeLevels = GradeLevel::all();
        $sections    = Section::all();

        return view('students.index', compact('students', 'gradeLevels', 'sections'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        $gradeLevels   = GradeLevel::all();
        $sections      = Section::all();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        return view('students.create', compact('gradeLevels', 'sections', 'academicYears'));
    }

    /**
     * Store a newly created student.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_no'       => 'required|string|max:50|unique:students,student_no',
            'library_card_no'  => 'nullable|string|max:50|unique:students,library_card_no',
            'lrn'              => 'nullable|string|max:20|unique:students,lrn',
            'first_name'       => 'required|string|max:50',
            'middle_name'      => 'nullable|string|max:50',
            'last_name'        => 'required|string|max:50',
            'grade_level_id'   => 'required|exists:grade_levels,id',
            'section_id'       => 'required|exists:sections,id',
            'school_year'      => 'nullable|string|max:20',
            'contact_number'   => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:100',
            'address'          => 'nullable|string|max:255',
            'guardian_name'    => 'nullable|string|max:100',
            'guardian_contact' => 'nullable|string|max:20',
            'photo'            => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status'           => 'required|in:active,inactive',
            'max_books_allowed' => 'nullable|integer|min:1|max:10',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('students/photos', 'public');
        }

        $student = Student::create($validated);

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'Create Student',
            'module'     => 'Student Management',
            'details'    => "Registered student {$student->full_name} (Student No: {$student->student_no}).",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('students.index')
            ->with('success', "Student '{$student->full_name}' has been registered successfully.");
    }

    /**
     * Display the student profile.
     */
    public function show(Student $student)
    {
        $student->load(['gradeLevel', 'section']);

        $transactions = Transaction::where('student_id', $student->id)
            ->with(['bookCopy.book'])
            ->latest()
            ->paginate(10);

        $fines = Fine::where('student_id', $student->id)
            ->with('transaction')
            ->latest()
            ->get();

        $activeIssues = Transaction::where('student_id', $student->id)
            ->where('status', 'issued')
            ->with(['bookCopy.book'])
            ->get();

        return view('students.show', compact('student', 'transactions', 'fines', 'activeIssues'));
    }

    /**
     * Show the form for editing a student.
     */
    public function edit(Student $student)
    {
        $gradeLevels   = GradeLevel::all();
        $sections      = Section::all();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        return view('students.edit', compact('student', 'gradeLevels', 'sections', 'academicYears'));
    }

    /**
     * Update the specified student.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'student_no'       => ['required', 'string', 'max:50', Rule::unique('students', 'student_no')->ignore($student->id)],
            'library_card_no'  => ['nullable', 'string', 'max:50', Rule::unique('students', 'library_card_no')->ignore($student->id)],
            'lrn'              => ['nullable', 'string', 'max:20', Rule::unique('students', 'lrn')->ignore($student->id)],
            'first_name'       => 'required|string|max:50',
            'middle_name'      => 'nullable|string|max:50',
            'last_name'        => 'required|string|max:50',
            'grade_level_id'   => 'required|exists:grade_levels,id',
            'section_id'       => 'required|exists:sections,id',
            'school_year'      => 'nullable|string|max:20',
            'contact_number'   => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:100',
            'address'          => 'nullable|string|max:255',
            'guardian_name'    => 'nullable|string|max:100',
            'guardian_contact' => 'nullable|string|max:20',
            'photo'            => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status'           => 'required|in:active,inactive',
            'max_books_allowed' => 'nullable|integer|min:1|max:10',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($student->photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($student->photo);
            }
            $validated['photo'] = $request->file('photo')->store('students/photos', 'public');
        }

        $student->update($validated);

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'Update Student',
            'module'     => 'Student Management',
            'details'    => "Updated student {$student->full_name} (Student No: {$student->student_no}).",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('students.index')
            ->with('success', "Student '{$student->full_name}' has been updated successfully.");
    }

    /**
     * Remove the specified student.
     */
    public function destroy(Request $request, Student $student)
    {
        // Check if student has active transactions
        $activeTransactions = Transaction::where('student_id', $student->id)
            ->where('status', 'issued')
            ->count();

        if ($activeTransactions > 0) {
            return redirect()->route('students.index')
                ->with('error', "Cannot delete student '{$student->full_name}' because they have active book transactions.");
        }

        $studentName = $student->full_name;

        // Delete photo if exists
        if ($student->photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($student->photo);
        }

        $student->delete();

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'Delete Student',
            'module'     => 'Student Management',
            'details'    => "Deleted student {$studentName}.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('students.index')
            ->with('success', "Student '{$studentName}' has been deleted successfully.");
    }

    /**
     * AJAX search for students (used in circulation forms).
     */
    public function search(Request $request)
    {
        $search = $request->get('q', '');

        $students = Student::where('status', 'active')
            ->where(function ($q) use ($search) {
                $q->where('student_no', 'like', "%{$search}%")
                  ->orWhere('library_card_no', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            })
            ->with(['gradeLevel', 'section'])
            ->take(10)
            ->get()
            ->map(function ($student) {
                return [
                    'id'              => $student->id,
                    'student_no'      => $student->student_no,
                    'library_card_no' => $student->library_card_no,
                    'full_name'       => $student->full_name,
                    'grade_level'     => $student->gradeLevel->name ?? '',
                    'section'         => $student->section->name ?? '',
                ];
            });

        return response()->json($students);
    }

    /**
     * Get sections for a given grade level (AJAX).
     */
    public function getSections($gradeLevel)
    {
        $sections = Section::where('grade_level_id', $gradeLevel)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($sections);
    }
}
