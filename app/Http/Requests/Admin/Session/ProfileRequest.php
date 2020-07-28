<?php

namespace App\Http\Requests\Admin\Session;

use App\Http\Requests\Request;

class ProfileRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'old_password' => 'required|string',
            'password'     => 'required|string|different:old_password',
        ];
    }
}
