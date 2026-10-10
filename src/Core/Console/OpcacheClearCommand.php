<?php

namespace Themosis\Core\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use RuntimeException;

class OpcacheClearCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'opcache:clear
        {--fcgi= : PHP-FPM FastCGI address (host:port or unix socket path). Defaults to the OPCACHE_FCGI environment variable or 127.0.0.1:9000}
        {--timeout=5 : Connection timeout in seconds}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the PHP-FPM OPcache';

    /**
     * FastCGI record types.
     */
    protected const FCGI_BEGIN_REQUEST = 1;

    protected const FCGI_END_REQUEST = 3;

    protected const FCGI_PARAMS = 4;

    protected const FCGI_STDIN = 5;

    protected const FCGI_STDOUT = 6;

    protected const FCGI_STDERR = 7;

    /**
     * Execute the console command.
     *
     * The CLI has its own OPcache: the PHP-FPM one can only be reset from a
     * PHP-FPM worker. A one-off script is written to the storage directory
     * and executed through a FastCGI request. As the storage directory may be
     * inside the web root, the script only acts when the FastCGI request
     * carries a random token (a non HTTP_ parameter, which web clients cannot
     * set), and it deletes itself.
     */
    public function handle(Filesystem $files): int
    {
        $address = $this->option('fcgi') ?: env('OPCACHE_FCGI', '127.0.0.1:9000');
        $script = $this->laravel->storagePath('framework' . DIRECTORY_SEPARATOR . 'opcache-reset-' . Str::random(32) . '.php');
        $token = Str::random(64);

        $files->put($script, $this->script($token));

        try {
            $output = $this->request($address, [
                'GATEWAY_INTERFACE' => 'FastCGI/1.0',
                'REQUEST_METHOD' => 'GET',
                'SCRIPT_FILENAME' => $script,
                'SCRIPT_NAME' => '/' . basename($script),
                'REQUEST_URI' => '/' . basename($script),
                'QUERY_STRING' => '',
                'SERVER_PROTOCOL' => 'HTTP/1.1',
                'SERVER_SOFTWARE' => 'themosis/opcache-clear',
                'REMOTE_ADDR' => '127.0.0.1',
                'CONTENT_LENGTH' => '0',
                'THEMOSIS_OPCACHE_TOKEN' => $token,
            ]);
        } catch (RuntimeException $e) {
            $this->components->error("Unable to reach PHP-FPM at [{$address}]: {$e->getMessage()}");

            return self::FAILURE;
        } finally {
            $files->delete($script);
        }

        $body = str_contains($output, "\r\n\r\n") ? Str::after($output, "\r\n\r\n") : $output;
        $result = json_decode(trim($body), true);

        if (! is_array($result)) {
            $this->components->error('Unexpected PHP-FPM response: ' . Str::limit(trim($body), 200));

            return self::FAILURE;
        }

        if (! ($result['loaded'] ?? false) || ! ($result['enabled'] ?? false)) {
            $this->components->info('OPcache is not enabled in PHP-FPM, nothing to reset.');

            return self::SUCCESS;
        }

        if (! ($result['reset'] ?? false)) {
            $this->components->error('PHP-FPM did not reset OPcache (check opcache.restrict_api).');

            return self::FAILURE;
        }

        $this->components->info('PHP-FPM OPcache reset successfully.');

        return self::SUCCESS;
    }

    /**
     * Get the one-off OPcache reset script source.
     *
     * @param string $token
     *
     * @return string
     */
    protected function script(string $token): string
    {
        return '<?php' . PHP_EOL
            . '@unlink(__FILE__);' . PHP_EOL
            . 'if (PHP_SAPI !== "fpm-fcgi" || ! hash_equals(' . var_export($token, true) . ', (string) ($_SERVER["THEMOSIS_OPCACHE_TOKEN"] ?? ""))) {' . PHP_EOL
            . '    http_response_code(404);' . PHP_EOL
            . '    exit;' . PHP_EOL
            . '}' . PHP_EOL
            . '$loaded = function_exists("opcache_reset");' . PHP_EOL
            . 'echo json_encode([' . PHP_EOL
            . '    "loaded" => $loaded,' . PHP_EOL
            . '    "enabled" => $loaded && filter_var(ini_get("opcache.enable"), FILTER_VALIDATE_BOOLEAN),' . PHP_EOL
            . '    "reset" => $loaded && @opcache_reset(),' . PHP_EOL
            . ']);' . PHP_EOL;
    }

    /**
     * Send a FastCGI request and return the STDOUT stream.
     *
     * @param string $address
     * @param array  $params
     *
     * @throws RuntimeException
     *
     * @return string
     */
    protected function request(string $address, array $params): string
    {
        $remote = str_starts_with($address, '/') || str_starts_with($address, 'unix:')
            ? 'unix://' . preg_replace('#^unix:(//)?#', '', $address)
            : 'tcp://' . $address;

        $timeout = (float) $this->option('timeout');
        $socket = @stream_socket_client($remote, $errno, $errstr, $timeout);

        if (! $socket) {
            throw new RuntimeException($errstr ?: 'connection failed');
        }

        stream_set_timeout($socket, (int) ceil($timeout));

        $requestId = 1;
        $payload = $this->record(self::FCGI_BEGIN_REQUEST, $requestId, pack('nCx5', 1, 0));

        $encoded = '';
        foreach ($params as $name => $value) {
            $encoded .= $this->length(strlen($name)) . $this->length(strlen($value)) . $name . $value;
        }

        $payload .= $this->record(self::FCGI_PARAMS, $requestId, $encoded);
        $payload .= $this->record(self::FCGI_PARAMS, $requestId, '');
        $payload .= $this->record(self::FCGI_STDIN, $requestId, '');

        fwrite($socket, $payload);

        $stdout = '';
        $stderr = '';

        while (! feof($socket)) {
            $header = fread($socket, 8);

            if ($header === false || strlen($header) < 8) {
                break;
            }

            $record = unpack('Cversion/Ctype/nrequestId/ncontentLength/CpaddingLength/Creserved', $header);
            $content = $record['contentLength'] > 0 ? $this->read($socket, $record['contentLength']) : '';

            if ($record['paddingLength'] > 0) {
                $this->read($socket, $record['paddingLength']);
            }

            if ($record['type'] === self::FCGI_STDOUT) {
                $stdout .= $content;
            } elseif ($record['type'] === self::FCGI_STDERR) {
                $stderr .= $content;
            } elseif ($record['type'] === self::FCGI_END_REQUEST) {
                break;
            }
        }

        $timedOut = stream_get_meta_data($socket)['timed_out'] ?? false;
        fclose($socket);

        if ($timedOut) {
            throw new RuntimeException('timed out waiting for a response');
        }

        if ($stdout === '' && $stderr !== '') {
            throw new RuntimeException(trim($stderr));
        }

        return $stdout;
    }

    /**
     * Build a FastCGI record.
     */
    protected function record(int $type, int $requestId, string $content): string
    {
        return pack('CCnnCx', 1, $type, $requestId, strlen($content), 0) . $content;
    }

    /**
     * Encode a FastCGI name/value length.
     */
    protected function length(int $length): string
    {
        return $length < 128 ? chr($length) : pack('N', $length | 0x80000000);
    }

    /**
     * Read exactly the given number of bytes from the socket.
     *
     * @param resource $socket
     */
    protected function read($socket, int $length): string
    {
        $data = '';

        while (strlen($data) < $length && ! feof($socket)) {
            $chunk = fread($socket, $length - strlen($data));

            if ($chunk === false || $chunk === '') {
                break;
            }

            $data .= $chunk;
        }

        return $data;
    }
}
