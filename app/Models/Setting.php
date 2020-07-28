<?php

namespace App\Models;

class Setting extends Model
{
    public $timestamps = false;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'admin_id';

    protected $columns = [
        'admin_id',
        'site_name',
        'theme',
        'hotlink_protection',
        'copyright',
        'statistics'
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
