<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Services\EmployeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct(
        protected EmployeeService $service,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page');

        $employees = $this->service->getAll($perPage ?: null);

        return response()->json($employees);
    }

    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $employee = $this->service->createWithContributions($request->validated());

        return response()->json($employee, 201);
    }

    public function show(int $id): JsonResponse
    {
        $employee = $this->service->getById($id);

        return response()->json($employee);
    }

    public function update(UpdateEmployeeRequest $request, int $id): JsonResponse
    {
        $employee = $this->service->update($id, $request->validated());

        return response()->json($employee);
    }
}

