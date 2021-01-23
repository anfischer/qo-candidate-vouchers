<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use App\ValueObjects\ApiContract;
use Closure;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;

class NegotiatesApiContract
{
    private Repository $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(Request $request, Closure $next)
    {
        $apiContract = ApiContract::fromRequestAcceptHeader($request->header('accept'));
        $this->repository->set('app.api_version', $apiContract->toVersionConstraint()->getValue());
        $this->repository->set('app.api_content_type', $apiContract->toContentType()->getValue());

        return $next($request);
    }
}
