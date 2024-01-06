<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $user;
    protected $company;
    protected $role = 'admin';
    protected $isAdmin = false;
    protected $isAdviser = false;
    protected $isTeam = false;
    protected $adviser = null;
    protected $team = null;
    protected $is_assistant = false;
    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $this->user = auth()->user();

            if (!is_null($this->user)) {
                $this->company = $this->user->company;

                $this->isAdmin = $this->user->roles()->where('name', 'admin')->count() > 0;
            }

            return $next($request);
        });
    }
    /**
     * Success response for api calls
     */
    public function successResponse($message, $data = array())
    {
        return response()->json(['data' => $data, 'message' => $message, 'errors' => []], 200, [], JSON_PRETTY_PRINT);
    }

    /**
     * Failed response for api calls
     */
    public function errorResponse($message, $errors = array())
    {
        return response()->json(['data' => [], 'message' => $message, 'errors' => $errors], 404, [], JSON_PRETTY_PRINT);
    }

}
