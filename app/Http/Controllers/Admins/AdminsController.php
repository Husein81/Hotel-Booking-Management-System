<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;
use App\Models\Apartment\Apartment;
use App\Models\Hotel\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class AdminsController extends Controller
{
    public function viewLogin(){

        return view('admins.login');
    }
    public function checkLogin(Request $request){

        $remember_me = $request->has('remember_me') ? true : false;

        if (auth()->guard('admin')->attempt(['email' => $request->input("email"), 'password' => $request->input("password")], $remember_me)) {

            return redirect()->route('admins.dashboard');
        }
        return redirect()->back()->with(['error' => 'error logging in']);


    }

    public function index(){

        $adminsCount = Admin::select()->count();
        $hotelsCount = Hotel::select()->count();
        $roomsCount = Apartment::select()->count();
        return view('admins.index',compact('adminsCount','hotelsCount','roomsCount'));
    }

    public function allAdmins(){

        $admins = Admin::select()->orderBy('id','desc')->get();
        return view('admins.alladmins',compact('admins'));
    }

    public function createAdmins(){

        return view('admins.createadmins');
    }

    public function storeAdmins(Request $request){

        $storeAdmins = Admin::create(
            ["name"=>$request->name,
            "email"=>$request->email,
            "password"=> Hash::make($request->password),]
        );
        if($storeAdmins){
            return Redirect::route('admins.all')->with(['success','Admin created successfully']);
        }
    }


}
