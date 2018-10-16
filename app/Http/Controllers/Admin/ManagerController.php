<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Validator;
use Exception;
use App\Http\Controllers\Controller as Controller;
use App\Manager;
use App\ManagerActionLog;

use Illuminate\Http\Request;

class ManagerController extends Controller
{
  protected $guard = 'back';

  public function login(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name' => 'between:3,32|alpha_dash|exists:managers',
      'password' => 'required|between:6,32'
    ]);
    if ($validator->fails()) {
      return $this->failure(-1, $validator->errors()->first());
    }
      if (Auth::guard('back')->attempt(['name' => $request->name, 'password' => $request->password])) {
          $log = new ManagerActionLog();
          $manager = Auth::guard('back')->authenticate();
          $log->type = 1;
          $log->author_id = $manager->id;
          $log->author_name = $manager->name;
          $log->action = 'Manager "' . $manager->name . '" login.';
          $log->ip_address = request()->ip();
          $log->save();
          return ['code'=>200];
      }
      return ['code'=>400,'message'=>'Password Error'];
  }

  public function getManagerInfo()
  {
      try{
          $manager = Auth::guard('back')->authenticate();
      }catch (Exception $e){
          return view('new.login');
      }
      return ['code'=>200,$manager];
  }

  public function logout(Request $request)
  {
      $log = new ManagerActionLog();
      $manager = Auth::guard('back')->authenticate();
      $log->type = 1;
      $log->author_id = $manager->id;
      $log->author_name = $manager->name;
      $log->action = 'Manager "' . $manager->name . '" login out.';
      $log->ip_address = request()->ip();
      $log->save();
      Auth::guard('back')->logout();
      return redirect('/');
  }
}
