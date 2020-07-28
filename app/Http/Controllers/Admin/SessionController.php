<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Session\ProfileRequest;
use App\Http\Transformers\AuthTransformer;
use Illuminate\Support\Arr;

class SessionController extends Controller
{
    /**
     * 修改密码
     */
    public function profile(ProfileRequest $request)
    {
        $data = $request->validated();
        $admin = auth('admin')->user();

        if (!app('hash')->check($data['old_password'], $admin->password)) {
            return $this->error('old_password_valid')->respond(422);
        }

        $admin->update(Arr::only($data, 'password'));

        return $this->success($admin, AuthTransformer::class);
    }
}
