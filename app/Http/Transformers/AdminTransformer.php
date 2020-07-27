<?php

namespace App\Http\Transformers;

use App\Models\Admin;
use Flugg\Responder\Transformers\Transformer;

class AdminTransformer extends Transformer
{
    /**
     * List of available relations.
     *
     * @var string[]
     */
    protected $relations = [
        // 'recent_health_data' => HealthDataTransformer::class,
    ];

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
    public function transform(Admin $admin): array
    {
        return $admin->toArray();
    }
}
