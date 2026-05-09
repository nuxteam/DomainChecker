<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Domain;

class DomainCheckService
{
    public function check(Domain $domain): array
    {
        $start = microtime(true);

        try {
            $http = Http::timeout($domain->timeout);

            $response = $domain->method === 'HEAD'
                ? $http->head($domain->url)
                : $http->get($domain->url);

            $status = $response->status();

            return [
                'ok' => $status < 500,  
                'code' => $status,
                'time' => microtime(true) - $start,
                'error' => null,
            ];

        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'code' => null,
                'time' => microtime(true) - $start,
                'error' => $e->getMessage(),
            ];
        }
    }
}