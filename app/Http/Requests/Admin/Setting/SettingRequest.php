<?php

namespace App\Http\Requests\Admin\Setting;

use App\Http\Requests\Request;
use App\Services\OneDrive\Constants;

class SettingRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'               => 'sometimes|nullable|string',
            'theme'              => 'sometimes|nullable|string|in:' . implode(',', Constants::SITE_THEME),
            'hotlink_protection' => 'sometimes|nullable|string',
            'copyright'          => 'sometimes|nullable|string',
            'statistics'         => 'sometimes|nullable|string',
        ];
    }
}
