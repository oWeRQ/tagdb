<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Events\QueryExecuted;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $url = request()->fullUrl();
        $count = 0;
        $totalTime = 0;
        DB::listen(function(QueryExecuted $query) use(&$count, &$totalTime, $url) {
            $count++;
            $totalTime += $query->time;

            $sql = $query->sql;
            foreach ($query->bindings as $value) {
                $sql = preg_replace('/\?/', "'{$value}'", $sql, 1);
            }

            $message = "\n\turl: $url\n\tcount: $count\n\tsql: $sql\n\ttime: {$query->time}ms\n\ttotal: {$totalTime}ms";
            Log::channel('query')->debug($message);
        });
    }
}
