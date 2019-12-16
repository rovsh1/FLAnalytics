<?php

namespace App\Http\Controllers;

use App\UrlList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Class AnalyticsController
 * @package App\Http\Controllers
 */
class AnalyticsController extends Controller
{
    private $token;
    private $access;
    private $sites;
    private $prefix;

    /**
     * AnalyticsController constructor.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next)
        {
            $this->token = $this->getAccessToken();
            $this->access = [
                'fixinglist'=>'168329090',
                'ustabor'=>'168341712',
            ];
            $this->mobileAccess = [
                'fixinglist'=>[
                    'android'=>'',
                    'ios'=>'',
                ],
                'ustabor'=>[
                    'android'=>'',
                    'ios'=>'',
                ],
            ];

            $this->sites = [
                'fixinglist'=>[
                    'www.fixinglist.com',
                    'auto.fixinglist.com',
                    'tech.fixinglist.com',
                    'home.fixinglist.com',
                ],
                'ustabor'=>[
                    'www.ustabor.uz',
                    'auto.ustabor.uz',
                    'tech.ustabor.uz',
                    'home.ustabor.uz',
                ],
            ];
            $this->blogs = [
                'fixinglist'=>[
                    'main'=>[
                        'www.fixinglist.com/kz/blog/',
                        'www.fixinglist.com/blog/',
                    ],
                    'auto'=>[
                        'auto.fixinglist.com/kz/blog/',
                        'auto.fixinglist.com/blog/'
                    ],
                    'tech'=>[
                        'tech.fixinglist.com/kz/blog/',
                        'tech.fixinglist.com/blog/'
                    ],
                    'home'=>[
                        'home.fixinglist.com/kz/blog/',
                        'home.fixinglist.com/blog/'
                    ],
                ],
                'ustabor'=>[
                    'main'=>[
                        'www.ustabor.uz/uz/blog/',
                        'www.ustabor.uz/blog/',
                    ],
                    'auto'=>[
                        'auto.ustabor.uz/uz/blog/',
                        'auto.ustabor.uz/blog/',
                    ],
                    'tech'=>[
                        'tech.ustabor.uz/uz/blog/',
                        'tech.ustabor.uz/blog/',
                    ],
                    'home'=>[
                        'home.ustabor.uz/uz/blog/',
                        'home.ustabor.uz/blog/',
                    ],
                ],
            ];

            $this->prefix = [
                'tech',
                'auto',
                'home',
            ];
            return $next($request);
        });
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request){

        if( !Auth::user()->hasRole('admin')){
            $roles = Auth::user()->roles()->get();
            if(!isset($roles[0])){
               abort(404);
            }
            $permissions = $roles[0]->permissions()->get();
            if(!isset($permissions[0])){
                abort(404);
            };
            $route = $permissions[0]->slug;
            $route_arr = explode('.', $route);

            if(!empty($route_arr[2])){
                if($route_arr[2] == 'main'){
                    unset ($route_arr[2]);
                    $route = implode('.',$route_arr);
                    return redirect()->route($route);
                }
                return redirect()->route($route, ['query' => $route_arr[2]]);
            }
            return redirect()->route($route);
        }else{
            return redirect()->route('fixinglist');
        }
//        return view('analytics.index')->with('token', $this->token['access_token']);
    }



    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function home(Request $request){

        $routeName = str_replace('/','',$request->route()->getName());

        if(!Auth::user()->canDo($routeName)){
            $roles = Auth::user()->roles()->get();
            if(!isset($roles[0])){
                return redirect('/');
            }
            $permissions = $roles[0]->permissions()->where('slug', 'like', '%' . $routeName . '%')->get();

            if(!isset($permissions[0])){
                return redirect('/');
            };

            $route = $permissions[0]->slug;
            $route_arr = explode('.', $route);
            if(!empty($route_arr[2])){
                if($route_arr[2] == 'main'){
                    unset ($route_arr[2]);
                    $route = implode('.',$route_arr);
                    return redirect()->route($route);
                }
                return redirect()->route($route, ['query' => $route_arr[2]]);
            }
            return redirect()->route($route);
        }

        if(array_key_exists($routeName,$this->access )){
            $id = $this->access[$routeName];
            $sites = $this->sites[$routeName];
        }else{
            $routeName = 'fixinglist';
            $id = $this->access[$routeName];
        }
        return view('analytics.home')->with([
            'token'=>$this->token['access_token'],
            'id'=>$id,
            'sites'=>$sites,
        ]);
    }


    /**
     * @param bool $query
     * @param Request $request
     * @return $this
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function categories($query = false,Request $request){
        $routeName = $request->route()->getName();
        $routePrefix = str_replace('/','',$request->route()->getPrefix());
        if(!empty($query)){
            if(in_array($query, $this->prefix)){
                $routeCan = str_replace('query', $query, $routeName);
                if(!Auth::user()->canDo($routeCan)){
                    return redirect('/');
                }
                if(!array_key_exists($routePrefix,$this->access )){
                    $routePrefix = 'fixinglist';
                }
                $url_list = UrlList::where('url', 'like', '%' . $routePrefix . '%')
                    ->where('url', 'like', '%' . $query . '%')
                    ->get();
                $id = $this->access[$routePrefix];
            }else{
                abort(404);
            }
        }else{
            $routeCan = $routeName.'.main';
            if(!Auth::user()->canDo($routeCan)){
                return redirect('/');
            }
            if(!array_key_exists($routePrefix,$this->access )){
                $routePrefix = 'fixinglist';
            }
            $url_list = UrlList::where('url', 'like', '%' . $routePrefix . '%')
                ->where('url', 'not like', '%' . $this->prefix[0] . '%')
                ->where('url', 'not like', '%' . $this->prefix[1] . '%')
                ->where('url', 'not like', '%' . $this->prefix[2] . '%')
                ->get();
            $id = $this->access[$routePrefix];
        }
        $segments = $this->getSegments($query,$this->sites[$routePrefix][0],$routePrefix);

        return view('analytics.categories')->with([
            'token'=>$this->token['access_token'],
            'id'=>$id,
            'segments'=>$segments,
            'url_list'=>$url_list,
        ]);
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function filter(Request $request){
        $query = $request->get('query');
        $routeName = str_replace('/','',$request->route()->getPrefix());
        if(
            !Auth::user()->canDo($routeName.'.categories.main') &&
            !Auth::user()->canDo($routeName.'.categories.tech') &&
            !Auth::user()->canDo($routeName.'.categories.auto')
        ){
            return redirect('/');
        }
        if(array_key_exists($routeName,$this->access )){
            $id = $this->access[$routeName];
        }else{
            $routeName = 'fixinglist';
            $id = $this->access[$routeName];
        }
        return view('analytics.filter')->with([
            'token'=>$this->token['access_token'],
            'id'=>$id,
            'query'=>$query,
        ]);
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function users(Request $request){

        $routeName = str_replace('/','',$request->route()->getPrefix());
        if(array_key_exists($routeName,$this->access )){
            $id = $this->access[$routeName];
        }else{
            $routeName = 'fixinglist';
            $id = $this->access[$routeName];
        }
        return view('analytics.users')->with([
            'token'=>$this->token['access_token'],
            'id'=>$id,
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function clicks(Request $request){


        $routeName = str_replace('/','',$request->route()->getPrefix());
        if(array_key_exists($routeName,$this->access )){
            $id = $this->access[$routeName];
        }else{
            $routeName = 'fixinglist';
            $id = $this->access[$routeName];
        }

        return view('analytics.clicks-'.$routeName)->with([
            'token'=>$this->token['access_token'],
            'id'=>$id,
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function bounces(Request $request){


        $routeName = str_replace('/','',$request->route()->getPrefix());
        if(array_key_exists($routeName,$this->access )){
            $id = $this->access[$routeName];
        }else{
            $routeName = 'fixinglist';
            $id = $this->access[$routeName];
        }
        return view('analytics.bounces')->with([
            'token'=>$this->token['access_token'],
            'id'=>$id,
        ]);
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function channels($query=false, Request $request){

        $filter = $request->get('query');

        $routeName = $request->route()->getName();
        $routePrefix = str_replace('/','',$request->route()->getPrefix());
        if(!empty($query) && !in_array($query, $this->prefix)){
            abort(404);
        }
        if(!empty($query)){
            $routeCan = str_replace('query', $query, $routeName);
        }else{
            $routeCan = $routeName.'.main';
        }
        if(!Auth::user()->canDo($routeCan)){
            return redirect('/');
        }
        $routeName = str_replace('/','',$request->route()->getPrefix());
        if(array_key_exists($routeName,$this->access )){
            $id = $this->access[$routeName];
        }else{
            $routeName = 'fixinglist';
            $id = $this->access[$routeName];
        }

        if(empty($query)){
            $current_route = $this->sites[$routePrefix][0];
        }else{
            $current_route = request()->route()->getPrefix();
        }

        $segments = $this->getSegments($query,$current_route, $routePrefix);

        if(!empty($filter)){
            $dimension = $request->get('dimension');
            return view('analytics.channels-filter')->with([
                'token'=>$this->token['access_token'],
                'id'=>$id,
                'query'=>$query,
                'current_route'=>$current_route,
                'prefix'=>$this->prefix,
                'filter'=>$filter,
                'segments'=>$segments,
                'dimension'=>$dimension,
            ]);
        }else{
            return view('analytics.channels')->with([
                'token'=>$this->token['access_token'],
                'id'=>$id,
                'query'=>$query,
                'segments'=>$segments,
                'current_route'=>$current_route,
                'prefix'=>$this->prefix,
            ]);
        }

    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function blog($query=false, Request $request){

        if(!empty($query) && !in_array($query, $this->prefix)){
            abort(404);
        }
        $routePrefix = str_replace('/','',$request->route()->getPrefix());
        $routeName = $request->route()->getName();
        if(array_key_exists($routePrefix,$this->access )){
            $id = $this->access[$routePrefix];
        }else{
            $routeName = 'fixinglist';
            $id = $this->access[$routePrefix];
        }
        $blogs = $this->blogs[$routePrefix];
        if(!empty($query) && !in_array($query, $this->prefix)){
            abort(404);
        }elseif(!empty($query)){
            $blogs = $blogs[$query];
        }else{
            $blogs = $blogs['main'];
        }
        if(!empty($query)){
            $routeCan = str_replace('query', $query, $routeName);
        }else{
            $routeCan = $routeName.'.main';
        }
        if(!Auth::user()->canDo($routeCan)){
            return redirect('/');
        }

        $segments = $this->getSegments($query,$this->sites[$routePrefix][0],$routePrefix);
        return view('analytics.blog')->with([
            'token'=>$this->token['access_token'],
            'id'=>$id,
            'query'=>$query,
            'blogs'=>$blogs,
            'segments'=>$segments,
            'prefix'=>$this->prefix,
        ]);
    }



    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function blogFilter(Request $request){
        $query = $request->get('query');
        $routeName = str_replace('/','',$request->route()->getPrefix());
        if(
            !Auth::user()->canDo($routeName.'.blog.main') &&
            !Auth::user()->canDo($routeName.'.blog.tech') &&
            !Auth::user()->canDo($routeName.'.blog.auto')
        ){
            return redirect('/');
        }
        if(array_key_exists($routeName,$this->access )){
            $id = $this->access[$routeName];
        }else{
            $routeName = 'fixinglist';
            $id = $this->access[$routeName];
        }
        $filter = true;
        if(strpos($query, 'auto')!==false || strpos($query, 'tech')!==false){
            $filter = false;
        }
        return view('analytics.blog-filter')->with([
            'token'=>$this->token['access_token'],
            'id'=>$id,
            'query'=>$query,
            'filter_query'=>$filter,
            'prefix'=>$this->prefix,
        ]);
    }


    /**
     * @param bool $query
     * @param Request $request
     * @return $this
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function catalog($query = false,Request $request){

        $routeName = $request->route()->getName();
        $routePrefix = str_replace('/','',$request->route()->getPrefix());
        if(!empty($query) && !in_array($query, $this->prefix)){
            abort(404);
        }
        if(array_key_exists($routePrefix,$this->access )){
            $id = $this->access[$routePrefix];
        }else{
            $routeName = 'fixinglist';
            $id = $this->access[$routePrefix];
        }
        if(!empty($query)){
            $routeCan = str_replace('query', $query, $routeName);
        }else{
            $routeCan = $routeName.'.main';
        }
        if(!Auth::user()->canDo($routeCan)){
            return redirect('/');
        }

        if(!empty($query) && in_array($query, $this->prefix)){
            $site_key = array_search($query.'.'.str_replace('www.','',$this->sites[$routePrefix][0]), $this->sites[$routePrefix]);
            $site = $this->sites[$routePrefix][$site_key];
        }else{
            $site = $this->sites[$routePrefix][0];
        }


        $segments = $this->getSegments($query ,$site,$routePrefix);

        return view('analytics.catalog')->with([
            'token'=>$this->token['access_token'],
            'id'=>$id,
            'query'=>$site,
            'segments'=>$segments,
            'routeName'=>str_replace('.','',str_replace('query','',str_replace($routePrefix,'',$routeName)))
        ]);
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function catalogFilter(Request $request){
        $query = $request->get('query');
        $routeName = str_replace('/','',$request->route()->getPrefix());
        if(
            !Auth::user()->canDo($routeName.'.catalog.main') &&
            !Auth::user()->canDo($routeName.'.catalog.tech') &&
            !Auth::user()->canDo($routeName.'.catalog.auto')
        ){
            return redirect('/');
        }
        if(array_key_exists($routeName,$this->access )){
            $id = $this->access[$routeName];
        }else{
            $routeName = 'fixinglist';
            $id = $this->access[$routeName];
        }
        return view('analytics.catalog-filter')->with([
            'token'=>$this->token['access_token'],
            'id'=>$id,
            'query'=>$query,
        ]);
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function mobile($query=false, Request $request){

        if(!empty($query) && !in_array($query, $this->prefix)){
            abort(404);
        }
        $routePrefix = str_replace('/','',$request->route()->getPrefix());
        if(array_key_exists($routePrefix,$this->access )){
            $id = $this->access[$routePrefix];
        }else{
            $id = $this->access[$routePrefix];
        }

        if(!empty($query) && in_array($query, $this->prefix)){
            $site_key = array_search($query.'.'.str_replace('www.','',$this->sites[$routePrefix][0]), $this->sites[$routePrefix]);
            $site = $this->sites[$routePrefix][$site_key];
        }elseif(empty($query)){
            $site = $this->sites[$routePrefix][0];
        }


        $segments = $this->getSegments($query,$this->sites[$routePrefix][0],$routePrefix);
        return view('analytics.mobile')->with([
            'token'=>$this->token['access_token'],
            'id'=>$id,
            'query'=>$query,
            'site'=>$site,
            'segments'=>$segments,
            'prefix'=>$this->prefix,
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function mobileDownload($query=false, Request $request){

        if(!empty($query) && !in_array($query, $this->prefix)){
            abort(404);
        }
        $routePrefix = str_replace('/','',$request->route()->getPrefix());
        if(array_key_exists($routePrefix,$this->access )){
            $id = $this->access[$routePrefix];
        }else{
            $id = $this->access[$routePrefix];
        }

        return view('analytics.mobile-download')->with([
            'token'=>$this->token['access_token'],
            'id'=>$id,
            'query'=>$query,
            'prefix'=>$this->prefix,
        ]);
    }

    /**
     * @return array
     */
    private function getAccessToken(){

        $token = Session::get('google_analytics') ?? null ;
        $client = new \Google_Client();
        if(empty($token)){
            $token = $this->generateAccessToken($client);
            if(!empty($token)){
                session(['google_analytics' => $token]);
                Session::save();
            }
        }else{
            $client->setAccessToken($token);
            if($client->isAccessTokenExpired()){
                $token = $this->generateAccessToken($client);
            }
        }
        return $token;
    }

    /**
     * @param $client
     * @return mixed
     */
    private function generateAccessToken($client){
        $client->setApplicationName("Google Analytics");
        $client->setAuthConfig(storage_path(env('GOOGLE_SERVICE_CLIENT_JSON')));
        $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);

        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();
        return $token;
    }

    protected function getSegments(string $query, string $site, string $routePrefix){
        if(!empty($query)){
            $segments = 'sessions::condition::ga:pagePath=~^'.$query.'\;condition::!ga:pagePath=~^www..*';
        }else{
            $segments = 'sessions::condition::ga:pagePath=~^'.$site.'/..*\,ga:pagePath=~^'.str_replace('www.','',$site).'/..*';
            $segments.='\;condition::!';
            foreach ($this->sites[$routePrefix] as $key =>$domain){
                if($domain !== $site){
                    if($key+1==count($this->sites[$routePrefix])){
                        $segments.='ga:pagePath=~^'.$domain.'..*';
                    }else{
                        $segments.='ga:pagePath=~^'.$domain.'..*\,';
                    }
                }
            }
        }
        return $segments;
    }


    /**
     * @param bool $query
     * @param Request $request
     * @return $this
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function adwords($query = false,Request $request){

        $routeName = $request->route()->getName();
        $filters = $request->get('filters');
        $metrics = $request->get('metrics');
        $dimensions = $request->get('dimensions');
        $week = $request->get('week');
        $routePrefix = str_replace('/','',$request->route()->getPrefix());
        if(!empty($query) && !in_array($query, $this->prefix)){
            abort(404);
        }
        if(array_key_exists($routePrefix,$this->access )){
            $id = $this->access[$routePrefix];
        }else{
            $routeName = 'fixinglist';
            $id = $this->access[$routePrefix];
        }
        if(!empty($query)){
            $routeCan = str_replace('query', $query, $routeName);
        }else{
            $routeCan = $routeName.'.main';
        }
        if(!Auth::user()->canDo($routeCan)){
            return redirect('/');
        }

        if(!empty($query) && in_array($query, $this->prefix)){
            $site_key = array_search($query.'.'.str_replace('www.','',$this->sites[$routePrefix][0]), $this->sites[$routePrefix]);
            $site = $this->sites[$routePrefix][$site_key];
        }else{
            $site = $this->sites[$routePrefix][0];
        }


        $segments = $this->getSegments($query ,$site,$routePrefix);
        $view = ($request->get('type') && $request->get('type')=='detail') ? 'analytics.adwords-detail' : 'analytics.adwords';
        if(!empty($request->get('type')) && $request->get('type') =='detail'){
            $dimensions .= ',ga:campaign';
        }

        return view($view)->with([
            'token'=>$this->token['access_token'],
            'id'=>$id,
            'query'=>$site,
            'segments'=>$segments,
            'filters'=>$filters,
            'metrics'=>$metrics,
            'dimensions'=>$dimensions,
            'week'=>$week,
            'routeName'=>str_replace('.','',str_replace('query','',str_replace($routePrefix,'',$routeName)))
        ]);
    }
}
