<?php

namespace App\Http\Requests\Admin\Unit;

use App\Http\Requests\Request;

class StoreImageRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'image' => 'required|image'
        ];
    }
}
