<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\LoginRequest;
use App\Http\Transformers\AuthTransformer;
use App\Models\Admin;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $ip_trial_key = 'login:lock:ip:' . $request->ip();
        $ip_trial = Cache::get($ip_trial_key, 0);

        if ($ip_trial > 10) {
            return $this->error('login_attempts');
        }

        try {
            $cache_key = 'admin_username_login:' . $data['username'];
            $trial = Cache::get($cache_key, 0);

            if (!$token = Auth::attempt(Arr::only($data, ['username', 'password']))) {
                Cache::put($cache_key, ++$trial, now()->addHours(4));
                Cache::put($ip_trial_key, ++$ip_trial, now()->addMinutes(15));

                if ($trial >= 5) {
                    $admin = Admin::where('username', $data['username'])->firstOrFail();
                    $admin->update([
                        'locked' => 1
                    ]);

                    return $this->error('locked')->respond(401);
                }

                return $this->errorTrans('incorrect_username_or_password', ['left' => 5 - $trial])->respond(422);
            }
        } catch (ModelNotFoundException $e) {
            return $this->error('admin_not_exists');
        }

        $admin = Auth::guard('admin')->user();

        return $this->success($admin, AuthTransformer::class);
    }
}
