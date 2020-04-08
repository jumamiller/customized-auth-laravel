<?php

namespace App\Http\Controllers\Auth;

use App\Notifications\UserRegisteredSuccessfully;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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
    protected $redirectTo = RouteServiceProvider::HOME;

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
    /*protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],

            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    /*protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }*/

    /**
     * Register new account
     * @param Request $request
     * @return User
     */

     protected function register(Request $request){
         /**
          * @var User $user
          */
          $validatedData=$request->validate([
              'name'            =>['required','string','max:255'],
              'username'        =>['required','string','max:255'],
              'email'           =>['required','string','max:255','unique:users'],
              'password'        =>['required','string','min:8','confirmed'],
          ]);

          try{
              $validatedData['password']        =bcrypt(Arr::get($validatedData,'password'));
              $validatedData['activation_code'] =Str::random(30).time();
              $user                             =app(User::class)->create($validatedData);
          }catch(\Exception $exception){
              logger()->error($exception);
              return redirect()->back()->with('message','unable to create new user');
          }

          $user->notify(new UserRegisteredSuccessfully($user));
          return redirect()-back()->with('message','user created successfully,please check your email to activate your account');

     }
    /**
     * Activate the user with given activation code.
     * @param string $activationCode
     * @return string
     */

     public function activateUser(string $activationCode){
         try{
             $user=app(User::class)->where('activation_code',$activationCode)->first();
             if(!$user){
                 return "The code doesnt exist for any user in our system";
             }
             $user->status  =true;
             $user->activation_code=null;
             $user->save();
             auth()->login($user);
         }catch(\Exception $exception){
             logger()->error($exception);
             return "Ops! something went wrong";
         }

         return redirect()->to('/home');
     }
}
