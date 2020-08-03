<?php

namespace App\Http\Requests\Admin\OneDrive;

use App\Http\Requests\Request;

class BindRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'redirect_uri'  => 'required|url',
            'client_id'     => 'required|string',
            'client_secret' => 'required|string',
            'account_type'  => 'required|in:com,cn',
        ];
    }
}
