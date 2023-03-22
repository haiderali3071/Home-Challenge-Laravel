<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Preference;
use Illuminate\Support\Facades\Auth;
   
class PreferenceController extends BaseController
{ 
    public function setPreferences(Request $request)
    {    
        $user_id =  Auth::guard('api')->user()['id'];
        $input = $request->all();
        Preference::updateOrCreate(
            ['user_id'=>$user_id],
            $input,
        ); 
        return $this->sendResponse(null,'preferences updated');
    }

    public function getPreferences(){
        $user_id =  Auth::guard('api')->user()['id'];
        $user_preferenecs = Preference::where('user_id','=',$user_id)->first();
        return $this->sendResponse($user_preferenecs,'user preferences');
    }

    
}