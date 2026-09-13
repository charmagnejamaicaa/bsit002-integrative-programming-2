<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    // 1. GET ALL EMPLOYEES (With Pagination, Search, Filter, & Relationship for Lab 5)
    public function index(Request $request)
    {
        $query = Employee::with('department');

        // Search by first_name, last_name, or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by department_id
        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Paginate results (Lab 5 requirement)
        return response()->json($query->paginate(10), 200);
    }

    // 2. CREATE EMPLOYEE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'email'         => 'required|email|unique:employees,email',
            'position'      => 'required|string|max:100',
            'department_id' => 'required|exists:departments,id',
        ]);

        $employee = Employee::create($validated);

        return response()->json([
            'message' => 'Employee created successfully!',
            'data'    => $employee->load('department')
        ], 201);
    }

    // 3. GET SINGLE EMPLOYEE
    public function show($id)
    {
        $employee = Employee::with('department')->find($id);

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        return response()->json($employee, 200);
    }

    // 4. UPDATE EMPLOYEE
    public function update(Request $request, $id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $validated = $request->validate([
            'first_name'    => 'sometimes|string|max:100',
            'last_name'     => 'sometimes|string|max:100',
            'email'         => 'sometimes|email|unique:employees,email,' . $id,
            'position'      => 'sometimes|string|max:100',
            'department_id' => 'sometimes|exists:departments,id',
        ]);

        $employee->update($validated);

        return response()->json([
            'message' => 'Employee updated successfully!',
            'data'    => $employee->load('department')
        ], 200);
    }

    // 5. DELETE EMPLOYEE
    public function destroy($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $employee->delete();

        return response()->json([
            'message' => 'Employee deleted successfully!'
        ], 200);
    }
}