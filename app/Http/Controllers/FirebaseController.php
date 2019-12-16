<?php
/**
 * Created by PhpStorm.
 * User: delux
 * Date: 12.04.18
 * Time: 10:26
 */

namespace App\Http\Controllers;

use Google\Cloud\BigQuery\BigQueryClient;
use Google\Cloud\BigQuery\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Class FirebaseController
 * Queriyng bigdata with firebase analytics, show generated in datastudio reports
 * @package App\Http\Controllers
 */
class FirebaseController extends Controller
{
    private  $mobileAccess;
    private  $iframe;
    private  $config;
    private  $queryConfig;


    /**
     * FirebaseController constructor.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next)
        {
            $this->mobileAccess = [
//                'fixinglist'=>[
//                    'android'=>'com_oxus_fixinglist_ANDROID',
//                    'ios'=>'com_oxus_fixinglist_IOS',
//                ],
//                'ustabor'=>[
//                    'android'=>'su_keysoft_ustabor_ANDROID',
//                    'ios'=>'com_rideauction_Ustabor_IOS',
//                ],
                'fixinglist'=>'analytics_154392674',
                'ustabor'=>'analytics_152310373',
            ];


            $this->iframe = [
                'fixinglist'=>'https://datastudio.google.com/embed/reporting/1DO7kIQHhSZQfrrGEd1R93QoL-ddrXpvY/page/Gg3',
                'ustabor'=>'https://datastudio.google.com/embed/reporting/1aWQ5JkC0Mrdc6CEaSwQAVo1NPxzIvw9y/page/Gg3',
            ];
            $this->config = [
                'fixinglist'=>[
                    'projectId' => 'fixing-list-155821',
                    'keyFilePath' => storage_path(env('GOOGLE_SERVICE_CLIENT_FIREBASE_JSON_FIXING')),
                ],
                'ustabor'=>[
                    'projectId' => 'ustabor-1330',
                    'keyFilePath' => storage_path(env('GOOGLE_SERVICE_CLIENT_FIREBASE_JSON')),
                ],

            ];
            $this->queryConfig = array(
                'configuration'=>[
                    'query' => [
                        'useCachedResults'=> true,
                        'useLegacySql'=> false
                    ]
                ]
            );

            return $next($request);
        });
    }

    /**
     * Custom sql query to google biguery data, connected to firebase analytics
     * @param bool $query
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|string
     * @throws \Google\Cloud\BigQuery\JobException
     * @throws \Google\Cloud\Core\Exception\GoogleException
     */
    public function indexCustom($query = false,Request $request){

        $routePrefix = str_replace('/','',$request->route()->getPrefix());
            if(!array_key_exists($routePrefix,$this->mobileAccess )){
                $routePrefix = 'ustabor';
            }
            $project_id = $this->mobileAccess[$routePrefix];

        if(!empty($project_id)){

            $bigQuery = new BigQueryClient($this->config[$routePrefix]);
            $dataset = $bigQuery->dataset($project_id);

            $yesterdayDate = date('Ymd',strtotime('-1 days'));
            $sevenDaysAgoDate = date('Ymd', strtotime('-7 days'));
            if($request->ajax()){
                $sevenDaysAgoDate = $request->get('date1');
                $yesterdayDate = $request->get('date2');
            }
            $sql = 'SELECT
                  date,
                  SUM(CASE
                      WHEN period = 1 THEN users END) AS days_01,  SUM(CASE
                      WHEN period = 7 THEN users END) AS days_07,
                  SUM(CASE
                      WHEN period = 30 THEN users END) AS days_30
                FROM (
                  SELECT
                    dates.date AS date,
                    periods.period AS period,
                    APPROX_COUNT_DISTINCT(user_dim.app_info.app_instance_id) AS users
                  FROM
                    `'.$dataset->id().'.app_events_*`,
                    UNNEST(event_dim) AS activity
                  CROSS JOIN (
                    SELECT
                      e.date
                    FROM
                      `'.$dataset->id().'.app_events_*`,
                      UNNEST(event_dim) AS e
                    WHERE
                      e.name = \'user_engagement\'
                    GROUP BY
                      date) AS dates
                  CROSS JOIN (
                    SELECT
                      period
                    FROM (
                      SELECT
                        1 AS period) UNION ALL (
                      SELECT
                        7 AS period)
                        UNION ALL (
                      SELECT
                        30 AS period)
                        ) AS periods
                        
                  CROSS JOIN
                  UNNEST(event_dim) AS t0_event_dim
                WHERE
                  t0_event_dim.name = \'user_engagement\'      
                  and
                    dates.date >= activity.date
                    AND SAFE_CAST(FLOOR(DATE_DIFF(PARSE_DATE("%Y%m%d",
                            dates.date),
                          PARSE_DATE("%Y%m%d",
                            activity.date),
                          DAY)/periods.period) AS INT64) = 0
                  GROUP BY
                    1,
                    2 )
                WHERE date BETWEEN \''.$sevenDaysAgoDate.'\' AND \''.$yesterdayDate.'\'    
                GROUP BY
                  date
                ORDER BY
                  date';

            $sql = 'SELECT
                  date,
                  SUM(CASE
                      WHEN period = 1 THEN users END) AS days_01,  SUM(CASE
                      WHEN period = 7 THEN users END) AS days_07,
                  SUM(CASE
                      WHEN period = 30 THEN users END) AS days_30
                FROM (
                  SELECT
                    dates.date AS date,
                    periods.period AS period,
                    APPROX_COUNT_DISTINCT(user_pseudo_id) AS users
                  FROM
            `'.$dataset->id().'.events_*`
                   
                  CROSS JOIN (
                    SELECT
                      event_date as date
                    FROM
                      `'.$dataset->id().'.events_*`
                    WHERE
                      event_name = \'user_engagement\'
                    GROUP BY
                      date) AS dates
                  CROSS JOIN (
                    SELECT
                      period
                    FROM (
                      SELECT
                        1 AS period) UNION ALL (
                      SELECT
                        7 AS period)
                        UNION ALL (
                      SELECT
                        30 AS period)
                        ) AS periods

                  
                WHERE
                  event_name = \'user_engagement\'      
              and
                    dates.date >= event_date
                    AND SAFE_CAST(FLOOR(DATE_DIFF(PARSE_DATE("%Y%m%d",
                            dates.date),
                          PARSE_DATE("%Y%m%d",
                            event_date),
                          DAY)/periods.period) AS INT64) = 0
                  GROUP BY
                    1,
                    2 )
                WHERE date BETWEEN \''.$sevenDaysAgoDate.'\' AND \''.$yesterdayDate.'\'    
                GROUP BY
                  date
                ORDER BY
                  date';
            $queryJobConfig = $bigQuery->query($sql, $this->queryConfig);
            $queryResults = $bigQuery->runQuery($queryJobConfig);
//            var_dump($queryResults->isComplete());
//            dd($queryResults->info());
            if(!empty($queryResults) && $queryResults->isComplete() && $queryResults->info() && $queryResults->info()['totalRows']>0){
                $result = array();
                foreach ($queryResults->rows() as $item){
                    $result[] = $item;
                }
                if($request->ajax()){
                    return json_encode($result);
                }
                return view('firebase.index')->with([
                    'events'=>$result
                ]);
            }else{
                return view('firebase.index')->with([
                    'events'=>''
                ]);
            }
        }else{
            return redirect('ustabor/mobile-download/'.$query);
        }

    }


    /**
     * Shows generated data studio reports
     * @param bool $query
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function index(Request $request){

        $routePrefix = str_replace('/','',$request->route()->getPrefix());

            if(!array_key_exists($routePrefix,$this->iframe )){
                $routePrefix = 'ustabor';
            }
            $project_id = $this->iframe[$routePrefix];


        if(!empty($project_id)){
            return view('firebase.index-frame')->with([
                'iframe'=>$project_id
            ]);
        }else{
            return redirect('ustabor/mobile-download/');
        }

    }

}

