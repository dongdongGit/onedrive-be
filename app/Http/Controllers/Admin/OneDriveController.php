<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OneDrive\BindRequest;
use App\Http\Requests\Admin\OneDrive\StoreRequest;
use App\Http\Requests\Admin\OneDrive\UpdateRequest;
use App\Models\OneDrive;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;

class OneDriveController extends Controller
{
    public function __construct(OneDrive $model)
    {
        // TODO:
        $this->model = $model;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $models = $this->model->where('admin_id', $this->user()->id)->exclude('settings')->get();

        return $this->success($models);

        // return themeView('admin.onedrive.index', compact('oneDrives'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();

        $this->user()->oneDrives()->create($data);

        return $this->success();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = $this->model->where('admin_id', $this->user()->id)->with('cover')->findOrFail($id);
        getDefaultOneDriveAccount($id);

        return $this->success($model);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        $data = $request->validated();

        $model = $this->model->where('admin_id', $this->user()->id)->with('cover')->findOrFail($id);

        if ($model->is_default && !Arr::get($data, 'is_default')) {
            return $this->error('change_default_onedrive')->respond(422);
        }

        $data['settings'] = array_merge(config('onedrive'), Arr::get($data, 'settings', config(config('onedrive'))));
        $model->update($data);

        return $this->success();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = $this->model->where('admin_id', $this->user()->id)->findOrFail($id);
        $model->delete();

        return $this->success();
    }

    public function showBind($id)
    {
        $model = $this->model->where('admin_id', $this->user()->id)->findOrFail($id);

        if ($model->is_binded) {
            return $this->errorTrans('bind_onedrive_exists', ['name' => $model->name])->respond(422);
        }

        return $this->success();
    }

    public function apply(Request $request, $id)
    {
        $data = $request->validate([
            'redirect_uri' => 'required|url'
        ]);

        $model = $this->model->where('admin_id', $this->user()->id)->findOrFail($id);

        if ($model->is_binded) {
            return $this->errorTrans('bind_onedrive_exists', ['name' => $model->name])->respond(422);
        }

        $ru = 'https://developer.microsoft.com/en-us/graph/quick-start?appID=_appId_&appName=_appName_&redirectUrl='
            . $data['redirect_uri'] . '&platform=option-php';
        $deepLink = '/quickstart/graphIO?publicClientSupport=false&appName=OLAINDEX&redirectUrl='
            . $data['redirect_uri'] . '&allowImplicitFlow=false&ru='
            . urlencode($ru);
        $app_url = 'https://apps.dev.microsoft.com/?deepLink='
            . urlencode($deepLink);

        $model->update($data);

        return redirect()->away($app_url);
    }

    public function bind(BindRequest $request, $id)
    {
        $data = $request->validated();

        $model = $this->model->where('admin_id', $this->user()->id)->findOrFail($id);

        if ($model->is_binded) {
            return $this->errorTrans('bind_onedrive_exists', ['name' => $model->name])->respond(422);
        }

        $data['is_configuraed'] = 1;
        $model->update($data);

        return redirect()->route('oauth', ['onedrive' => $model->id]); // TODO:
    }

    public function unbind($id)
    {
        $model = $this->model->where('admin_id', $this->user()->id)->findOrFail($id);

        if (!$model->is_binded) {
            return $this->errorTrans('bind_onedrive_not_found', ['name' => $model->name])->respond(422);
        }

        $model->update([
            'is_binded' => 0
        ]);

        return $this->success();
    }

    /**
     * 缓存清理
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clear($id)
    {
        $model = $this->model->where('admin_id', $this->user()->id)->findOrFail($id);

        if (!$model->is_binded) {
            return $this->errorTrans('bind_onedrive_not_found', ['name' => $model->name])->respond(422);
        }

        clearOnedriveCache($model->id);

        return $this->success();
    }

    /**
     * 刷新缓存
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function refresh($id)
    {
        $model = $this->model->where('admin_id', $this->user()->id)->findOrFail($id);

        if (!$model->is_binded) {
            return $this->errorTrans('bind_onedrive_not_found', ['name' => $model->name])->respond(422);
        }

        Artisan::call('od:cache', [
            '--one_drive_id' => $model->id
        ]);

        return $this->success();
    }
}
