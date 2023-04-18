<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Services\CurrentUser;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class UserController
 *
 * @package App\Http\Controllers\Api
 */
class UserController extends BaseController
{
    /**
     * Getting users action
     *
     * @param Employee $employee
     * @return AnonymousResourceCollection
     */
    public function __invoke(Employee $employee): AnonymousResourceCollection
    {
        $token     = app(CurrentUser::class)->getToken();
        $employees = $employee->getByToken($token);

        return EmployeeResource::collection($employees);
    }
}