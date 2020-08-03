<?php

namespace App\Http\Requests\Admin\OneDrive;

use App\Http\Requests\Request;

class UpdateRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'                        => 'sometimes|string|max:255',
            'root'                        => 'sometimes|string|max:255',
            'is_default'                  => 'sometimes|boolean',
            'expires'                     => 'sometimes|integer|min:1',
            'cover_id'                    => 'sometimes|exists:images,id',
            'settings'                    => 'array',
            'settings.image_hosting'      => 'in:enabled,disabled,admin_enabled',
            'settings.image_home'         => 'boolean',
            'settings.image_view'         => 'boolean',
            'settings.image_hosting_path' => 'string|max:255',
            'settings.image'              => 'string|max:255',
            'settings.video'              => 'string|max:255',
            'settings.dash'               => 'string|max:255',
            'settings.audio'              => 'string|max:255',
            'settings.doc'                => 'string|max:255',
            'settings.code'               => 'string|max:255',
            'settings.stream'             => 'string|max:255',
            'settings.encrypt_path'       => 'string|max:255',
            'settings.encrypt_option'     => 'array',
            'settings.encrypt_option.*'   => 'string|in:list,show,download,view',
        ];
    }
}
