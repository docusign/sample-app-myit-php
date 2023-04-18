<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Api\LoginRequest;
use App\Models\User;
use App\Services\DocuSign\AuthService;
use App\Services\EmployeeService;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * Class LoginController
 *
 * @package App\Http\Controllers\Api
 */
class LoginController extends BaseController
{
    /**
     * Login action
     *
     * @param LoginRequest $request
     * @param AuthService $authService
     * @return JsonResponse
     * @throws Exception
     */
    public function __invoke(LoginRequest $request, AuthService $authService): JsonResponse
    {
        $user  = app(User::class)->getByEmail($request->get('login'));
        $token = $user->createToken('Manager Token');

        if (!$authService->login($token->token->id)) {
            $token->token->delete();

            return response()->json([
                'message' => $authService->getErrorMessage(),
            ], 400);
        }

        $employeeService = new EmployeeService($token->token->id);
        $employeeService->generate();

        return response()->json([
            'token' => $token->accessToken,
        ]);
    }
}