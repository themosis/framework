<?php

namespace Themosis\Core\Http\Middleware;

use Illuminate\Contracts\Encryption\Encrypter;
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
     * CSRF token name.
     *
     * @var string
     */
    protected $token = '_token';

    /**
     * @var string
     */
    protected $csrfHeader = 'X-CSRF-TOKEN';

    /**
     * @var string
     */
    protected $xsrfHeader = 'X-XSRF-TOKEN';

    /**
     * @var string
     */
    protected static $encryptToken = 'XSRF-TOKEN';

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
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @throws TokenMismatchException
     *
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        if ($this->isReading($request)
        || $this->isRunningUnitTests()
        || $this->inExceptArray($request)
        || $this->tokensMatch($request)) {
            return tap($next($request), function ($response) use ($request) {
                if ($this->addHttpCookie) {
                    $this->addCookieToResponse($request, $response);
                }
            });
        }

        throw new TokenMismatchException();
    }

    /**
     * Determine if the HTTP request uses a "read" verb.
     *
     * @param \Illuminate\Http\Request $request
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
    protected function isRunningUnitTests()
    {
        return $this->app->runningInConsole() && $this->app->runningUnitTests();
    }

    /**
     * Check if the request has a URI that should pass through CSRF verification.
     *
     * @param Request $request
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
     * @param Request $request
     *
     * @return bool
     */
    protected function tokensMatch(Request $request)
    {
        $token = $this->getTokenFromRequest($request);

        return is_string($request->session()->token())
            && is_string($token)
            && hash_equals($request->session()->token(), $token);
    }

    /**
     * Return the CSRF token from request.
     *
     * @param Request $request
     *
     * @return string
     */
    protected function getTokenFromRequest(Request $request)
    {
        $token = $request->input($this->token) ?: $request->header($this->csrfHeader);

        if (! $token && $header = $request->header($this->xsrfHeader)) {
            $token = $this->encrypter->decrypt($header, static::serialized());
        }

        return $token;
    }

    /**
     * Add the CSRF token to the response cookies.
     *
     * @param Request  $request
     * @param Response $response
     *
     * @throws \Illuminate\Container\EntryNotFoundException
     *
     * @return Response
     */
    protected function addCookieToResponse(Request $request, Response $response)
    {
        $config = config('session');

        $response->headers->setCookie(
            new Cookie(
                static::$encryptToken,
                $request->session()->token(),
                $this->availableAt(60 * $config['lifetime']),
                $config['path'],
                $config['domain'],
                $config['secure'],
                false,
                false,
                $config['same_site'] ?? null
            )
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
        return EncryptCookies::serialized(static::$encryptToken);
    }
}
