<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Request;
class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
   
        return Validator::make($data, [
            'user_type' => 'required|string|max:15',
            'profile_picture' => 'required',
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'email' => 'required|email|unique:users',
            'username' => 'required|string|max:15|unique:users',
            'password' =>'required|min:8|max:25',
            'address' => 'required|max:255',
            'gender' => 'required|string|in:male,female,other',
            'day_of_birth' => 'Required|Integer|Between:0,32',
            'month_of_birth' => 'Required|Integer|Between:0,13',
            'year_of_birth' => 'required|numeric',
            'hobbies' => 'required|array|min:1'
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        /*if(Input::hasfile($data['profile_picture'])){
            echo $image = Input::file($data['profile_picture']);
            echo $upload = base_path().'/'.$data['username'].'/';
            echo $filename = $data['profile_picture'];
            //$image->move($upload, $filename);
            //$path = $upload.$filename;
        }*/
        $request = app('request');
        if($request->hasFile('profile_picture')) {
            // Get filename with extension            
            $filenameWithExt = $request->file('profile_picture')->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);            
           // Get just ext
            $extension = $request->file('profile_picture')->getClientOriginalExtension();
            //Filename to store
            $avtar = $data['username'].'.'.$extension;                       
            // Upload Image
            $path = $request->file('profile_picture')->storeAs('public/avtars', $avtar);
        } else {
            $avtar = 'default.jpg';
        }
        $user = new User();
        $user->user_type=$data['user_type'];
        $user->profile_picture=$avtar;
        $user->first_name=$data['first_name'];
        $user->last_name=$data['last_name'];
        $user->email=$data['email'];
        $user->username=$data['username'];
        $user->password=Hash::make($data['password']);
        $user->address=$data['address'];
        $user->gender=$data['gender'];
        $user->date_of_birth=$data['year_of_birth'].'-'.$data['month_of_birth'].'-'.$data['day_of_birth'];
        $user->hobbies=implode('|',$data['hobbies']);
        $user->save();
        return $user;
    }
}
