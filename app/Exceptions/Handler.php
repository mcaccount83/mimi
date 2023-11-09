<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   // public function render($request, Throwable $exception)
  //  {
   //     if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
   //         if ($exception->getStatusCode() == 404) {
   //             return redirect()->to('/login');
   //         }

    //        if ($exception->getStatusCode() == 500) {
   //             return redirect()->to('/login');
   //         }
  //      } else {
   //         return parent::render($request, $exception);
   //     }
  //  }
}
