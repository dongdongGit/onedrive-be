<?php

namespace App\Http\Requests\Admin\Unit;

use App\Http\Requests\Request;

class DestroyImageRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'image_ids'   => 'required|array',
            'image_ids.*' => 'integer'
        ];
    }
}
