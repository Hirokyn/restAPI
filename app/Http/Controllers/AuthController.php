<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function auth(Request $request){

        $credenciais = $request->all(['email','password']);

        $token = auth('api')->attempt($credenciais);

        if($token){
            return response()->json(['token' => $token], 200);
        }else{
            return response()->json(['erro' => 'Usuário ou senha inválido'], 403);
        }
        
    }
    public function logout(){
        auth('api')->logout();
        return responser()->json(['msg' => 'Logout foi realizado com sucesso']);
    }
    public function refresh(){
        //Só poderá solicitar um refresh desde que tenha um token válido
        $token = auth('api')->refresh();
        return response()->json(['token' => $token]);
    }
    public function login(){
        return response()->json(auth()->user()->get(['name','email']), 200);
    }
}
