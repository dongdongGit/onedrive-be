<?php

namespace App\Http\Controllers\Admin;

use App\Models\Image;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Unit\BindGoogleTFARequest;
use App\Http\Requests\Admin\Unit\DestroyImageRequest;
use App\Http\Requests\Admin\Unit\StoreImageRequest;
use App\Http\Requests\Admin\Unit\UnbindGoogleTFARequest;
use App\Models\OneDrive;

class UtilController extends Controller
{
    /**
     * 多图传图
     */
    public function storeImage(StoreImageRequest $request)
    {
        $data = $request->validated();

        $file = request()->file('image');

        if (empty($file) || !$file->isValid()) {
            return $this->error('file_not_exists')->respond(422);
        }

        $image = (new ImageService($file->path()))->save();

        return $this->success([
            'id'   => $image->id,
            'path' => $image->path
        ]);
    }

    /**
     * 多图删图
     */
    public function destroyImage(DestroyImageRequest $request)
    {
        $data = $request->validated();
        $ids = array_unique($data['image_ids']);

        if (!empty($ids)) {
            $images = Image::whereIn('id', $ids)->where('admin_id', $this->user()->id)->get();

            foreach ($images as $image) {
                $image->delete();
            }
        }

        return $this->success();
    }

    public function list()
    {
        $onedrives = OneDrive::get();

        return $this->success($onedrives);
    }

    public function generateGoogle2fa()
    {
        $secret = app('pragmarx.google2fa')->generateSecretKey();
        $admin = auth('admin')->user();
        $qrcode = app('pragmarx.google2fa')->getQRCodeInline(
            $admin->name,
            $admin->email,
            $secret
        );

        return $this->success([
            'qrcode' => $qrcode,
            'secret' => $secret
        ]);

        // return view('default.admin.google2fa', compact('qrcode', 'secret'));
    }

    public function authGoogle2fa(Request $request)
    {
        $redirect = redirect()->route('admin.basic');

        if ($request->input('remember') == 'on') {
            $cookie_remember = cookie()->forever('remember_2fa', 1);
            $redirect = $redirect->cookie($cookie_remember);
        }

        return $this->success();
        return $redirect;
    }

    public function bindGoogle2fa(BindGoogleTFARequest $request)
    {
        $data = $request->validated();

        $admin = auth('admin')->user();

        if ($admin->is_tfa) {
            return $this->error('bind_tfa_over')->respond(422);
        }

        if (!app('pragmarx.google2fa')->verifyKey($data['tfa_secret'], $data['code'])) {
            return $this->error('tfa_valid')->respond(422);
        }

        $admin->is_tfa = true;
        $admin->tfa_secret = $data['tfa_secret'];
        $admin->save();

        return $this->success();
    }

    public function unbindGoogle2fa(UnbindGoogleTFARequest $request)
    {
        $data = $request->validated();

        $admin = auth('admin')->user();

        if (!$admin->is_tfa) {
            return $this->error('bind_tfa')->respond(422);
        }

        if (!app('pragmarx.google2fa')->verifyKey($admin->tfa_secret, $data['code'])) {
            return $this->error('tfa_valid')->respond(422);
        }

        $admin->is_tfa = false;
        $admin->tfa_secret = null;
        $admin->save();

        return $this->success();
    }

    public function aria2c()
    {
        return view()->exists('ng') ? view('ng') : abort(404, '请先编译Aria2c');
    }

    public function checkHealth()
    {
        return $this->success([
            'instance' => config('app.name')
        ]);
    }
}
