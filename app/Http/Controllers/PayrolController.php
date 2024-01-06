<?php

namespace App\Http\Controllers;

use App\Models\Payrol;
use Illuminate\Http\Request;

class PayrolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('payrol.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('payrol.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $payrol = new Payrol;
        $payrol->name = $request->get('name');
        $payrol->position = $request->get('position');

        $payrol->daily_salary = $request->get('daily_salary');
        $payrol->hash_id = hash('sha256', uniqid() . time());

        $payrol->save();

        return $this->successResponse('payrol created', $payrol);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($hash_id)
    {
        $payrol = Payrol::where('hash_id', $hash_id)->first();

        if (is_null($payrol)) {
            abort(404);
        }

        return view('payrol.show', ['payrol' => $payrol]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($hash_id)
    {
        $payrol = Payrol::where('hash_id', $hash_id)->first();

        if (is_null($payrol)) {
            abort(404);
        }

        return view('payrol.edit', ['payrol' => $payrol]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $hash_id)
    {
        $payrol = Payrol::where('hash_id', $hash_id)->first();

        if (is_null($payrol)) {
            return $this->errorResponse('payrol not found');
        }
        $payrol->name = $request->get('name');
        $payrol->position = $request->get('position');
        $payrol->daily_salary = $request->get('daily_salary');

        $payrol->save();

        return $this->successResponse('payrol updated', $payrol);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($hash_id)
    {
        $payrol = Payrol::where('hash_id', $hash_id)->first();

        if (is_null($payrol)) {
            return $this->errorResponse('payrol not found');
        }

        $payrol->delete();
        return $this->successResponse('payrol deleted');
    }

    public function search(Request $request)
    {
        $payrols = Payrol::orderBy('name', 'ASC');
        if ($request->has('search')) {
            $search = $request->get('search');
            $payrols = $payrols->where('name', 'like', '%' . $search . '%');
        }
        if ($request->has('status')) {
            $status = $request->get('status');
            $payrols = $payrols->where('status', $status);
        }
        $payrols = $payrols->orderBy('created_at', 'DESC')->paginate(env('PER_PAGE', 20));
        return $this->successResponse('fetch information', $payrols);
    }

    public function info($hash_id)
    {
        $payrol = Payrol::where('hash_id', $hash_id)->first();

        if (is_null($payrol)) {
            return $this->errorResponse('payrol not found');
        }

        return $this->successResponse('fetch information', $payrol);
    }

    public function all()
    {
        $payrols = Payrol::orderBy('name', 'ASC')->get();
        return $this->successResponse('fetch information', $payrols);
    }
}
