<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use jcobhams\NewsApi\NewsApi;
use Illuminate\Support\Facades\Http;
use App\Models\Preference;
   
class NewsController extends BaseController
{  
    function getTopStories(){
        $api_key = env('NYTIMES_API_KEY');
        $query = 'https://api.nytimes.com/svc/topstories/v2/home.json?api-key='.$api_key;

        $response = Http::get($query);
        return $this->sendResponse($response->json(), 'success');
    }
    
    public function getPersonalizedNewsFeed(Request $request){
        if(!$request->page){
            return $this->sendError('incomplete required');
        }
        $user_id = Auth::guard('api')->user()['id'];
        $user_preferenecs = Preference::where('user_id','=',$user_id)->first();
        
        if(count($user_preferenecs->categories) > 0 || count($user_preferenecs->sources) > 0){
            $request->keyword = str_replace(", "," OR ",implode(", ", $user_preferenecs->categories));
            $request->sources = implode(", ", $user_preferenecs->sources);
            $response = $this->getNews($request);
            return $this->sendResponse($response, 'personalized news feed');
        }
        else{
            return $this->sendError('empty_preferences');
        }
    }

    public function getFilteredNews(Request $request){
        if(!$request->page || !$request->initial_total_pages || (!$request->keyword && !$request->category)){
            return $this->sendError('incomplete required');
        }
        $response = $this->getGuardianNews($request);
        if(!$request->sources && $request->page <= $request->initial_total_pages){
            return $this->sendResponse($response, 'guardian api');
        }
        else{
            if(!$request->sources){
                $request->page = $request->page-$request->initial_total_pages;
            }
            $response = $this->getNews($request);
            return $this->sendResponse($response, 'news api');
        }
    }

    // keyword, category(only few results), start & end date 
    // with categroy(date doesn't work | )
    // Source The Guardians
    private function getGuardianNews(Request $request){
        $api_key =env('THE_GUARDIAN_API_KEY');

        if(!$request->category){
            $query = 'https://content.guardianapis.com/search?api-key='.$api_key.
            '&q='.$request->keyword.
            '&page='.$request->page.
            '&page-size=100'.
            '&lang-en';

            if($request->from){
                $query = $query.'&from-date='.$request->from;
            }
    
            if($request->to){
                $query = $query.'&to-date='.$request->to;
            }
        }
        else{
            $query = 'https://content.guardianapis.com/tags?api-key='.$api_key.
            '&q='.$request->category.
            '&page='.$request->page.
            '&page-size=100';
        }
        
        $response = Http::get($query)->json();
        return $response;
    }

    // keyword, sources/category, start & end date
    // maximum 20 sources
    // with categroy(source & date doesn't work )
    private function getNews(Request $request)
    {    
        $newsapi = new NewsApi(env('NEWS_API_KEY'));
        
        if(!$request->category){
            $result = $newsapi->getEverything(
                $q=$request->keyword, 
                $sources=$request->sources,
                $domains=null, 
                $exclude_domains=null, 
                $from=$request->from, 
                $to=$request->to,
                $language='en', 
                $sort_by='popularity', 
                $pageSize=100, 
                $page=$request->page
            );
        }
        else{
            $result = $newsapi->getTopHeadlines(
                $q=$request->keyword, 
                $sources=null,
                $country=null, 
                $category=$request->category,  
                $pageSize=100, 
                $page=$request->page
            );
        }
       
        return $result;
    }

    public function getCategories(){
        $newsapi = new NewsApi(env('NEWS_API_KEY'));
        return $this->sendResponse($newsapi->getCategories(),'success');
    }

    public function getSources(){
        $newsapi = new NewsApi(env('NEWS_API_KEY'));
        $result =  $newsapi->getSources($category='general', $language='en');
        $new_result = array();
       
        foreach($result->sources as $item){
            $new_item['id'] = $item->id;
            $new_item['name'] = $item->name;
            array_push($new_result, $new_item);
        }
        
        return $this->sendResponse($new_result,'success');
    }
}