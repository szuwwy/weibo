<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;

use App\Models\Status;

class StatusesController extends Controller
{
    public function __construct()
    {
        //首选 except 方法，这样的话，当你新增一个控制器方法时，默认是安全的，此为最佳实践。Laravel 提供的 Auth 中间件在过滤指定动作时，如该用户未通过身份验证（未登录用户），默认将会被重定向到 login 登录页面。
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'content' => 'required|max:140'
        ]);

        Auth::user()->statuses()->create([
            'content' => $request->content
        ]);

        session()->flash('success', '发布成功！');

        return redirect()->back();
    }


    public function destroy(Status $status)
    {
        $this->authorize('destroy', $status);
        $status->delete();
        session()->flash('success', '微博已被成功删除！');
        return redirect()->back();
    }
}
