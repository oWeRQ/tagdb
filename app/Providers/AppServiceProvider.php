<?php

namespace App\Providers;

use App\Models\v1\Project;
use App\Models\v1\Token;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Request::macro('project', function() {
            /** @var Request */
            $request = $this;

            static $project;

            if ($project === null && $request->bearerToken()) {
                $token = Token::findByApiKey($request->bearerToken());
                if (!$token) {
                    abort(401);
                }
                return $project = $token->project;
            }

            if ($project === null && $request->hasHeader('X-Project-Id')) {
                $requestProject = Project::find($request->header('X-Project-Id'));
                if ($request->user()->belongsToProject($requestProject)) {
                    $project = $requestProject;
                }
            }

            if ($project === null) {
                $project = $request->user()->currentProject;
            }

            if ($project === null) {
                $project = new Project;
            }

            return $project;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (env('DB_LOG', false)) {
            $this->enableDBLog();
        }
    }

    public function enableDBLog()
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
