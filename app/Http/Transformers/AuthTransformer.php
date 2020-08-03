<?php

namespace App\Http\Transformers;

use App\Models\Admin;
use Flugg\Responder\Transformers\Transformer;
use Illuminate\Support\Facades\Auth;

class AuthTransformer extends Transformer
{
    /**
     * List of available relations.
     *
     * @var string[]
     */
    protected $relations = [];

    /**
     * List of autoloaded default relations.
     *
     * @var array
     */
    protected $load = [];

    /**
     * Transform the model.
     *
     * @param  \App\Essentail\Models\ $account
     * @return array
     */
    public function transform(Admin $admin)
    {
        return [
            'token'      => Auth::login($admin),
            'admin'      => transformation($admin, AdminTransformer::class)->transform(),
            'expires_in' => config('paseto.expires') * 3600
        ];
    }
}
