<?php

namespace App\Http\Requests\Admin\OneDrive;

use App\Http\Requests\Request;

class StoreRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'     => 'required|string|max:255',
            'root'     => 'required|string|max:255',
            'cover_id' => 'required|exists:images,id',
        ];
    }
}
