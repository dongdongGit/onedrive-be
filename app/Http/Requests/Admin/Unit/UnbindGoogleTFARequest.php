<?php

namespace App\Http\Requests\Admin\Unit;

use App\Http\Requests\Request;

class UnbindGoogleTFARequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code' => 'required|size:6'
        ];
    }
}
