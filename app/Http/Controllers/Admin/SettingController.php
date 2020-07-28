<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Setting\SettingRequest;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        return $this->success(auth('admin')->user()->setting);
    }

    /**
     * 基础设置
     */
    public function store(SettingRequest $request)
    {
        $data = $request->validated();
        $data = array_map(function (&$item) {
            return is_null($item) ? $item = '' : $item;
        }, $data);

        $setting = Setting::firstOrNew([
            'admin_id' => auth('admin')->user()->id
        ], $data);
        foreach ($data as $field => $value) {
            if (!in_array($field, $setting->getColumns())) {
                continue;
            }

            $setting->{$field} = $value;
        }
        $setting->save();

        return $this->success();
    }
}
