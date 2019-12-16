<?php

namespace App\Providers;

use App\Projects;
use App\SiteGrid;
use App\SiteGridHistory;
use Illuminate\Support\ServiceProvider;

class AlertServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }
    public static function show()
    {
        $models = SiteGrid:: with(array( 'history'=>function($query)
        {
            $query
                ->orderBy('created_at','ASC');
        }))->get()->toArray();
        $alerts = array();
        foreach ($models as $model){
            if(isset($model['history']) && isset($model['history'][0]) && isset($model['history'][1] )){
                if($model['history'][0]['code'] !== $model['history'][1]['code']){
                    $alerts[$model['url']][] = [$model['project_id']=>'код изменился'];
                }
                if($model['history'][0]['robots'] !== $model['history'][1]['robots']){
                    $alerts[$model['url']][] = [$model['project_id']=>'robots изменился'];
                }
                if($model['history'][0]['title'] !== $model['history'][1]['title']){
                    $alerts[$model['url']][] = [$model['project_id']=>'title изменился'];
                }
                $delta_index = $model['history'][0]['indexing'] - $model['history'][1]['indexing'];
                if(!empty($delta_index) && !empty($model['history'][0]['indexing'])){
                    $delta_index = ((int)$delta_index/(int)$model['history'][0]['indexing'])*100;
                    $project = Projects::find($model['project_id']);
                    $allowable_index = $project->index_change;
                    if((int)$delta_index > (int)$allowable_index){
                        $alerts[$model['url']][] = [$model['project_id']=>'Индекс изменился'];
                    }
                }
            }


        }
        return $alerts;
    }
}
