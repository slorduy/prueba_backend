<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;


class userController extends Controller
{
    public function registerUser(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:user',
            'cellphone' => 'required|string|unique:user',
            'password' => 'required|string'
        ],[
            'name.required' => 'El nombre es requerido',
            'name.string' => 'El nombre no es valido',
            'password.required' => 'La contraseña es requerida',
            'password.string' => 'La contraseña no es valida',
            'cellphone.required' => 'El celular es requerido',
            'cellphone.string' => 'El celular no es valido',
            'cellphone.unique' => 'El celular ya esta registrado',
            'email.required' => 'El email es requerido',
            'email.unique' => 'El email ya esta registrado',
            'email.email' => 'El email no es valido',
        ]);
       
        if($validator->fails()){
            return response()->json(['status'=>true,'message'=>$validator->errors()->first()], 400);
        }

        $user = new User();
        $user->name=$request->name;
        $user->cellphone=$request->cellphone;
        $user->email=$request->email;
        $user->password=bcrypt($request->password);

        if($user->save()){
            return response()->json(['status'=>false,'message'=>'Usuario creado exitosamente'],200);
        }else{
            return response()->json(['status'=>true,'message'=>'Usuario no pudo ser creado'],500);
        }
    }

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ],
        [
            'email.email' => 'El email no es valido',
            'email.required' => 'El email es requerido',
            'password.required' => 'La contraseña es requerida'
        ]
    
        );
        if ($validator->fails()) {
            return response()->json(['status'=>true,'message'=>$validator->errors()->first()], 400);
        }

        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['status' => true,'message'=>'Email y/o contraseña incorrectos'], 401);
        }
        
        return response()->json([
            'status' => false,
            'message' => 'Acceso exitoso',
            'access_token' => $token,
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ],200);
    }

    public function logout()
    {
        JWTAuth::invalidate();
        return response()->json([
            'status' => false,
            'message' => 'Sesion cerrada exitosamente'
        ]);
    }
}
