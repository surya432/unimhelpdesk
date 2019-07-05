<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Laravolt\Avatar\Facade as Avatar;
class LoginController extends Controller
{
    //

    public function Login(Request $request){
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $dataUser = \App\User::where('id',$request->user()->id)->with('roles')->first();
            $success['id'] =  $dataUser->id;
            $dataUser->tokenApi()->delete();
            $success['name'] =  $dataUser->name;
            $success['email'] =  $dataUser->email;
            $success['role'] =  $dataUser->roles[0]['name'];
            if (!isset($dataUser->avatar)) {
                $avatar = Avatar::create($dataUser->name)->toBase64();
                $success['avatar'] =  $avatar;
            } else {
                $location = \URL::to('') . '/uploads/users/' . $dataUser->avatar;
                $b64image = base64_encode(file_get_contents($location));
                $success['avatar'] = $b64image;
            }
            $success['token'] =  $dataUser->createToken(request('email'))->accessToken;
            return response()->json(["status" => "success", 'data' => $success]);
        } else {
            return response()->json([ "status" => "failed", 'error' => 'Unauthorised'], 401);
        }
    }

}
