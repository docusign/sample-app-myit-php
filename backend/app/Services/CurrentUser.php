<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Token;

class CurrentUser
{
    /**
     * Get current user
     *
     * @return User
     */
    public function get(): User
    {
        return Auth::user();
    }

    /**
     * Get token ID
     *
     * @return string
     */
    public function getTokenId(): string
    {
        return $this->getToken()->id;
    }

    /**
     * Get token
     *
     * @return Token
     */
    public function getToken(): Token
    {
        return $this->get()->token();
    }
}