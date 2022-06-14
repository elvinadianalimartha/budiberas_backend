<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Province;
use App\Models\Regency;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request) {
        $userRegister = $request->all();
        $validate = Validator::make($userRegister, [
            'name' => 'required',
            'email' => 'required|email:rfc,dns|unique:users',
            'password' => 'required|min:8|',
            'regency' => 'required',
            'district' => 'required',
            'address' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'phone_number' => 'required|min:10|max:13',
        ]);

        if($validate->fails()) {
            return ResponseFormatter::error(
                null,
                $validate->errors(),
                400
            );
        }

        $userRegister['password'] = Hash::make($request->password);

        $storeUser = User::create([
            'name' => $userRegister['name'],
            'email' => $userRegister['email'],
            'password' => $userRegister['password'],
            'fcm_token' => $userRegister['fcm_token'],
        ]);

        UserDetail::create([
            'user_id' => $storeUser->id,
            'address_owner' => $storeUser->name,
            'regency' => $userRegister['regency'],
            'district' => $userRegister['district'],
            'address' => $userRegister['address'],
            'address_notes' => $userRegister['address_notes'],
            'latitude' => $userRegister['latitude'],
            'longitude' => $userRegister['longitude'],
            'phone_number' => $userRegister['phone_number'],
            'default_address' => 1
        ]);

        $user = User::where('email', $request->email)->first();

        return ResponseFormatter::success(
            $user, 
            'Pendaftaran akun berhasil dilakukan'
        );
    }

    public function login(Request $request) {
        $loginData = $request->all();
        $validate = Validator::make($loginData, [
            'email' => 'email|required',
            'password' => 'required',
        ]);

        if($validate->fails()) {
            return ResponseFormatter::error(
                null,
                $validate->errors(),
                400
            );
        }

        if(!Auth::attempt($loginData)){
            return ResponseFormatter::error(
                ['message' => 'Login gagal! Email atau password Anda salah'],
                'Authentication failed',
                500,
            );
        }

        $user = User::where('email', $request->email)->first();
        $token = $user->createToken('authToken')->plainTextToken;

        return ResponseFormatter::success([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 'Terautentikasi');
    }

    public function logout(Request $request) {
        $tokenToRevoked = $request->user()->currentAccessToken()->delete();
        return ResponseFormatter::success(
            $tokenToRevoked,
            'Berhasil logout',
        );
    }

    //get data kabupaten di DIY
    public function getLocalRegencies() {
        $DIYprovinceId = Province::where('name', '=', 'DI YOGYAKARTA')->value('id');

        $DIYregencies = Regency::where('province_id', '=', $DIYprovinceId)->get();

        return $DIYregencies;
    }

    //get data kecamatan di DIY based on kabupaten yg dipilih
    public function getLocalDistricts($regencyId) {
        $DIYdistrict = District::where('regency_id', '=', $regencyId)->get();
        return $DIYdistrict;
    }
}
