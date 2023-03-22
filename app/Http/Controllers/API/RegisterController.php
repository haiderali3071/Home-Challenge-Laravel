<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use App\Models\Preference;
use Illuminate\Support\Facades\Auth;
use Validator;
   
class RegisterController extends BaseController
{ 
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required', 
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);
   
        if($validator->fails()){
            return $this->sendError('invalid required!');       
        }
   
        $input = $request->all(); 
        if (User::where('username', '=', $input['username'])->count() > 0){
            return $this->sendError('User already exists!');
        }
        else{
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input); 
            $success['token'] =  $user->createToken('Challenge')->accessToken; 
            Preference::create( 
                ['user_id'=>$user->id,
                'categories'=>[],
                 'sources'=>[]],
            ); 

            return $this->sendResponse($success, 'User register successfully.');
        }
    }
    
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required', 
            'password' => 'required', 
        ]);
   
        if($validator->fails()){
            return $this->sendError('username & password are required!');       
        }
   
        if(Auth::attempt(['username' => $request->username, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('Challenge')->accessToken;  
   
            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{ 
            return $this->sendError('Login failed!');
        } 
    }
}