<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use Mail;

class UsersController extends Controller
{
    public function __construct(){
        $this->middleware('auth',[
          'except' => ['show','create','store','index','confirmEmail'],
        ]);

        $this->middleware('guest',[
          'only' => ['create'],
        ]);
    }

    public function index(){
      $users = User::paginate(10);
      return view('users.index',compact('users'));
    }
    public function create(){
      return view('users.create');
    }

    public function show(User $user){
      $statuses = $user->statuses()
                       ->orderBy('created_at','desc')
                       ->paginate(20);
      return view('users.show', compact('user','status'));
    }

    public function store(Request $request){
      $this->validate($request,[
        'name' => 'required|max:50',
        'email' => 'required|email|unique:users|max:255',
        'password' => 'required',
        ]
      );
      $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
      ]);

      // Auth::login($user);
      $this->sendMailConfirmTo($user);
      session()->flash('success','验证邮件已经发送到你的注册邮箱上，请注意查收');
      return redirect()->route('users.show',[$user]);
    }

    public function edit(User $user){
      $this->authorize('update', $user);
      return view('users.edit',compact('user'));
    }
    protected function sendMailConfirmTo($user){
      $view = 'emails.confirm';
      $data = compact('user');
      $from = 'zfb1993@mymails.com';
      $name = 'zfb';
      $to = $user->email;
      $subject = '感谢注册应用。请教检查你的邮箱';

      Mail::send($view,$data,function($message) use ($from,$name,$to,$subject){
          $message->from($from,$name)->to($to)->subject($subject);
      });
    }
    public function update(User $user,Request $request){
        $this->validate($request,[
          'name' => 'required|max:50',
          'password' => 'nullable|confirmed|min:6',
        ]);

        $this->authorize('update',$user);

        $data = array();
        $data['name'] = $request->name;

        if($request->password){
          $data['password'] = $request->password;
        }

        $user->update($data);
        return redirect()->route('users.show', $user->id);
    }

    public function destroy(User $user){
      $this->authorize('destroy',$user);
      $user->delete();
      session()->flash('success','成功删除用户！');
      return back();
    }

    public function confirmEmail($token){
      $user = User::where('activation_token', $token)->firstOrFail();

      $user->activated = true;
      $user->activation_token = null;
      $user->save();

      Auth::login($user);
      session()->flash('success', '恭喜你，激活成功！');
      return redirect()->route('users.show', [$user]);
    }
}
