<?php

namespace App\Http\Controllers;

use App\Models\Worker;
use App\Models\WorkerAttendance;
use Illuminate\Http\Request;

class WorkerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('worker.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('worker.create');
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
        $worker = new Worker;
        $worker->name = $request->get('name');
        $worker->position = $request->get('position');

        $worker->daily_salary = $request->get('daily_salary');
        $worker->hash_id = hash('sha256', uniqid() . time());

        $worker->save();

        return $this->successResponse('worker created', $worker);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($hash_id)
    {
        $worker = Worker::where('hash_id', $hash_id)->first();

        if (is_null($worker)) {
            abort(404);
        }

        return view('worker.show', ['worker' => $worker]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($hash_id)
    {
        $worker = Worker::where('hash_id', $hash_id)->first();

        if (is_null($worker)) {
            abort(404);
        }

        return view('worker.edit', ['worker' => $worker]);
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
        $worker = Worker::where('hash_id', $hash_id)->first();

        if (is_null($worker)) {
            return $this->errorResponse('worker not found');
        }
        $worker->name = $request->get('name');
        $worker->position = $request->get('position');
        $worker->daily_salary = $request->get('daily_salary');

        $worker->save();

        return $this->successResponse('worker updated', $worker);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($hash_id)
    {
        $worker = Worker::where('hash_id', $hash_id)->first();

        if (is_null($worker)) {
            return $this->errorResponse('worker not found');
        }

        $worker->delete();
        return $this->successResponse('worker deleted');
    }

    public function search(Request $request)
    {
        $workers = Worker::orderBy('name', 'ASC');
        if ($request->has('search')) {
            $search = $request->get('search');
            $workers = $workers->where('name', 'like', '%' . $search . '%');
        }
        if ($request->has('status')) {
            $status = $request->get('status');
            $workers = $workers->where('status', $status);
        }
        $workers = $workers->orderBy('created_at', 'DESC')->paginate(env('PER_PAGE', 20));
        return $this->successResponse('fetch information', $workers);
    }

    public function info($hash_id)
    {
        $worker = Worker::where('hash_id', $hash_id)->first();

        if (is_null($worker)) {
            return $this->errorResponse('worker not found');
        }

        return $this->successResponse('fetch information', $worker);
    }

    public function all()
    {
        $workers = Worker::orderBy('name', 'ASC')->get();
        return $this->successResponse('fetch information', $workers);
    }

    public function createUpdateWorkerAttendance(Request $request)
    {
        $date = $request->get('date') . ' 00:00:00';

        foreach ($request->get('workers') as $i => $w) {
            $w_attendance = WorkerAttendance::where('date')->where('worker_id', $w['id'])->first();

            if (is_null($w_attendance)) {
                $w_attendance = new WorkerAttendance;
                $w_attendance->hash_id = hash('sha256', uniqid() . time());
                $w_attendance->worker_id = $w['id'];
                $w_attendance->date = $date;
            }

            $w_attendance->status = $w['status'];
            $w_attendance->save();
        }
    }

}
