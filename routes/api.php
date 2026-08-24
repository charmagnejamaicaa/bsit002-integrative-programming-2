<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/getStudents', function () {
    $students = DB::table('students')->get();
    return response()->json($students);
});


Route::post('/addStudent', function (Request $request) {
    $validated = $request->validate([
        'studentId'   => 'required|string|max:20|unique:students,studentId',
        'studentName' => 'required|string|max:50',
        'course'      => 'required|string|max:10',
    ]);

    // 3. Save directly to your MySQL 'students' table
    DB::table('students')->insert([
        'studentId'   => $validated['studentId'],
        'studentName' => $validated['studentName'],
        'course'      => $validated['course'],
    ]);

    // 4. Return response
    return response()->json([
        'message' => 'Student saved to database successfully!',
        'data'    => $validated
    ], 201);
});