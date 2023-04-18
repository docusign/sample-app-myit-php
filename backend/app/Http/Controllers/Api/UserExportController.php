<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Employee;
use App\Services\CurrentUser;
use App\Services\DocuSign\ExportUsersService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class UserExportController
 *
 * @package App\Http\Controllers\Api
 */
class UserExportController extends BaseController
{
    /**
     * Download users action
     *
     * @param Employee $employee
     * @param ExportUsersService $exportService
     * @return BinaryFileResponse
     */
    public function __invoke(Employee $employee, ExportUsersService $exportService): BinaryFileResponse
    {
        $token     = app(CurrentUser::class)->getToken();
        $employees = $employee->getByToken($token);

        return response()->download($exportService->create($employees));
    }
}