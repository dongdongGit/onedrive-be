<?php

namespace App\Http\Requests\Admin\Unit;

use App\Http\Requests\Request;

class BindGoogleTFARequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'tfa_secret' => 'required|string|size:16',
            'code'       => 'required|string|size:6'
        ];
    }
}
