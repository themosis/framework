<?php

namespace Themosis\Core\Http\Middleware;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\InteractsWithTime;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Themosis\Core\Application;

class VerifyCsrfToken
{
    use InteractsWithTime;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Encrypter
     */
    protected $encrypter;

    /**
     * URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [];

    /**
     * Indicates wheter the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    public function __construct(Application $application, Encrypter $encrypter)
    {
        $this->app = $application;
        $this->encrypter = $encrypter;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     *
     * @throws TokenMismatchException
     */
    public function handle($request, \Closure $next)
    {
        if ($this->isReading($request) ||
            $this->runningUnitTests() ||
            $this->inExceptArray($request) ||
            $this->tokensMatch($request)
        ) {
            return tap($next($request), function ($response) use ($request) {
                if ($this->shouldAddXsrfTokenCookie()) {
                    $this->addCookieToResponse($request, $response);
                }
            });
        }

        throw new TokenMismatchException('CSRF token mismatch');
    }

    /**
     * Determine if the HTTP request uses a "read" verb.
     *
     *
     * @return bool
     */
    protected function isReading(Request $request)
    {
        return in_array($request->method(), ['HEAD', 'GET', 'OPTIONS']);
    }

    /**
     * Determine if the application is running unit tests.
     *
     * @return bool
     */
    protected function runningUnitTests()
    {
        return $this->app->runningInConsole() && $this->app->runningUnitTests();
    }

    /**
     * Check if the request has a URI that should pass through CSRF verification.
     *
     *
     * @return bool
     */
    protected function inExceptArray(Request $request)
    {
        foreach ($this->except as $except) {
            if ('/' !== $except) {
                $except = trim($except, '/');
            }

            if ($request->fullUrlIs($except) || $request->is($except)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the session and input CSRF tokens match.
     *
     *
     * @return bool
     */
    protected function tokensMatch(Request $request)
    {
        $token = $this->getTokenFromRequest($request);

        return is_string($request->session()->token()) &&
            is_string($token) &&
            hash_equals($request->session()->token(), $token);
    }

    /**
     * Return the CSRF token from request.
     *
     *
     * @return string
     */
    protected function getTokenFromRequest(Request $request)
    {
        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');

        if (! $token && $header = $request->header('X-CSRF-TOKEN')) {
            try {
                $token = CookieValuePrefix::remove($this->encrypter->decrypt($header, static::serialized()));
            } catch (DecryptException $exception) {
                $token = '';
            }
        }

        return $token;
    }

    /**
     * Determine if the cookie should be added to the response.
     *
     * @return bool
     */
    public function shouldAddXsrfTokenCookie()
    {
        return $this->addHttpCookie;
    }

    /**
     * Add the CSRF token to the response cookies.
     *
     *
     * @return Response
     *
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    protected function addCookieToResponse(Request $request, Response $response)
    {
        $config = config('session');

        if ($response instanceof Responsable) {
            $response = $response->toResponse($request);
        }

        $response->headers->setCookie(
            new Cookie(
                'XSRF-TOKEN',
                $request->session()->token(),
                $this->availableAt(60 * $config['lifetime']),
                $config['path'],
                $config['domain'],
                $config['secure'],
                false,
                false,
                $config['same_site'] ?? null,
            ),
        );

        return $response;
    }

    /**
     * Determine if the cookie contents should be serialized.
     *
     * @return bool
     */
    public static function serialized()
    {
        return EncryptCookies::serialized('XSRF-TOKEN');
    }
}
