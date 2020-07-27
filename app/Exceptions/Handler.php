<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use Flugg\Responder\Exceptions\ConvertsExceptions;
use Flugg\Responder\Exceptions\Http\HttpException as ApiHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    use ConvertsExceptions;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        if (app()->bound('sentry') && $this->shouldReport($exception)) {
            app('sentry')->captureException($exception);
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof Exception) {
            $this->convertDefaultException($exception);
        }

        if ($exception instanceof ApiHttpException) {
            return $this->renderResponse($exception);
        }

        if ($this->isDebugMode()) {
            $data['debug'] = [
                'message'   => $exception->getMessage(),
                'exception' => get_class($exception),
                'code'      => $exception->getCode(),
                'file'      => $exception->getFile(),
                'line'      => $exception->getLine(),
            ];

            //clean trace
            foreach ($exception->getTrace() as $item) {
                if (isset($item['args']) && is_array($item['args'])) {
                    $item['args'] = $this->cleanTraceArgs($item['args']);
                }
                $data['debug']['trace'][] = $item;
            }

            return response()->json($data, 500);
        }

        return responder()->error('code_exception', get_class($exception) . '::' . $exception->getMessage())->respond();
    }

    /**
     * Determine if the application is in debug mode.
     *
     * @return Boolean
     */
    public function isDebugMode()
    {
        return (bool) env('APP_DEBUG');
    }

    /**
     * @param array $args
     * @param int   $level
     * @param int   $count
     *
     * @return array|string
     */
    private function cleanTraceArgs(array $args, $level = 0, &$count = 0)
    {
        $result = [];
        foreach ($args as $key => $value) {
            if (++$count > 1e4) {
                return '*SKIPPED over 10000 entries*';
            }
            if (is_object($value)) {
                $result[$key] = get_class($value);
            } elseif (is_array($value)) {
                if ($level > 10) {
                    $result[$key] = '*DEEP NESTED ARRAY*';
                } else {
                    $result[$key] = $this->cleanTraceArgs($value, $level + 1, $count);
                }
            } elseif (is_null($value)) {
                $result[$key] = null;
            } elseif (is_bool($value)) {
                $result[$key] = $value;
            } elseif (is_int($value)) {
                $result[$key] = $value;
            } elseif (is_resource($value)) {
                $result[$key] = get_resource_type($value);
            } elseif ($value instanceof \__PHP_Incomplete_Class) {
                $array = new \ArrayObject($value);
                $result[$key] = $array['__PHP_Incomplete_Class_Name'];
            } elseif (is_string($value) && mb_detect_encoding($value) === false) {
                $result[$key] = 'REMOVED-BINARY-BLOB';
            } else {
                $result[$key] = (string) $value;
            }
        }
        return $result;
    }
}
