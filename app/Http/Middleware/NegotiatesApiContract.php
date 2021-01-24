<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use App\ValueObjects\ApiContract;
use Closure;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use InvalidArgumentException;

class NegotiatesApiContract
{
    private Repository $repository;
    private ResponseFactory $responseFactory;

    public function __construct(Repository $repository, ResponseFactory $responseFactory)
    {
        $this->repository = $repository;
        $this->responseFactory = $responseFactory;
    }

    public function handle(Request $request, Closure $next)
    {
        try {
            $apiContract = ApiContract::fromRequestAcceptHeader($request->header('accept'));
            $this->repository->set('app.api_version', $apiContract->toVersionConstraint()->getValue());
            $this->repository->set('app.api_content_type', $apiContract->toContentType()->getValue());
        } catch (InvalidArgumentException $e) {
            return $this->responseFactory->json(['error' => [
                'type' => 'ApiVersionException',
                'message' => $e->getMessage(),
            ]], 400);
        }

        return $next($request);
    }
}
