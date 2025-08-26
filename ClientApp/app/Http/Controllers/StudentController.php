<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Http\Resources\StudentResource;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;


class StudentController extends Controller
{

    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $sortable = [  //Potential search strings, prevents odd injections
            'last_name'         => 'last_name',
            'first_name'        => 'first_name',
            'created_at'        => 'created_at',
            'birthday'          => 'birthday',
            'balance'           => 'balance',
            'subscription'      => 'subscription',
        ];

        $sortParam = (string) $request->query('sort', 'last_name,first_name'); //Default sort by name
        $sorts = array_map('trim', explode(',', $sortParam));

        $perPage = (int) $request->query('per_page', 10); //Prevents API overload
        $perPage = max(1, min($perPage, 100));

        $query = Student::query()
            ->with(['tutor','guardian','franchise', 'address']) //eager loading
            ->search($request->query('q'))
            ->ofFranchise($request->query('franchise_id'))
            ->ofTutor($request->query('tutor_id'))
            ->withInstrument($request->query('instrument_id'));

        foreach ($sorts as $sort) {
            $dir = str_starts_with($sort, '-') ? 'desc' : 'asc'; //Creates asc, desc functionality and then trims - from search string for accurate search
            $key = ltrim($sort, '-');
            if (isset($sortable[$key])) {
                $query->orderBy($sortable[$key], $dir);
            }
        }

        $students = $query->paginate($perPage);

        return StudentResource::collection($students)->response();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentRequest $request): JsonResponse
    {
        $student = Student::create($request->validated());
        return (new StudentResource($student))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student): JsonResponse
    {
        $student->load(['tutor', 'guardian', 'franchise', 'instruments', 'address']);
        return (new StudentResource($student))->response();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentRequest $request, Student $student): JsonResponse
    {
        $student->update($request->validated());
        return (new StudentResource($student->load(['tutor', 'guardian', 'franchise', 'address'])))->response();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student): \Illuminate\Http\Response
    {
        $student -> delete();
        return response()->noContent();
    }
}
