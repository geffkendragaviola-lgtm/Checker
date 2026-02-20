<?php

namespace App\Repositories;

use App\Models\Department;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class DepartmentRepository
{
    public function create(array $data): Department
    {
        return Department::create($data);
    }

    public function findAll(?int $perPage = null): Collection|LengthAwarePaginator
    {
        if ($perPage) {
            return Department::orderBy('name')->paginate($perPage);
        }

        return Department::orderBy('name')->get();
    }

    public function findById(int $id): ?Department
    {
        return Department::find($id);
    }

    public function update(Department $department, array $data): Department
    {
        $department->fill($data);
        $department->save();

        return $department;
    }

    public function softDelete(Department $department): void
    {
        $department->delete();
    }
}

