<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller
{
    public function __construct(){
      $this->middleware('guest',[
        'only' => ['create'],
      ]);
    }
    public function create(){
      return view('sessions.create');
    }

    public function store(Request $request){
      $credentials = $this->validate($request,[
        'email' => 'required|email|max:255',
        'password' => 'required',
      ]);
      if(Auth::attempt($credentials,$request->has('remember'))){
        session()->flash('success', '欢迎回来！');
          return redirect()->intended(route('users.show', [Auth::user()]));
      }else{
        session()->flash('danger','很抱歉，您的邮箱和密码不匹配');
      }
      return;
    }

    public function destory(){
      Auth::logout();
      session()->flash('success', '您已成功退出！');
      return redirect('login');
    }
}