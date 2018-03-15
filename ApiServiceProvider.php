<?php

namespace App\Http\Api;

use App\Http\Api\ApiRequest;
use App\Http\Api\Query\Transformer;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Request;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->resolving(ApiRequest::class, function (ApiRequest $request, $app) {
            $this->initializeRequest($request, $app['request']);
        });
    }

    /**
     * Initialize the ApiRequest by transforming the query data.
     *
     * @param  \App\Http\Api\ApiRequest  $request
     * @param  \Symfony\Component\HttpFoundation\Request  $current
     * @return void
     */
    protected function initializeRequest(ApiRequest $request, Request $current)
    {
        $transform = app(Transformer::class);
        $request->fields = $transform->fields($current->fields);
        $request->filters = $transform->filters($current->filter);
        $request->sorting = $transform->sort($current->sort);
        $request->includes = $transform->includes($current->include);
        $request->limit = (int) ($current->limit ?? config('api.pagination.default_per_page', 200));
        $request->page = (int) ($current->page ?? 1);
    }
}
