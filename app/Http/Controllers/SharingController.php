<?php

namespace App\Http\Controllers;

use App\Models\InvestorDetail;
use App\Models\Sale;
use App\Models\Shared;
use App\Models\Sharing;
use App\Models\SharingExpense;
use App\Models\SharingItem;
use App\Models\User;
use Illuminate\Http\Request;

class SharingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('shares.list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function report()
    {
        return view('shares.report');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('shares.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $share = new Sharing;
        $share->name = $request->get('name');

        $share->total_kg = $request->get('total_kg', 0);
        $share->total_amount = $request->get('total_amount', 0);
        $share->total_net_amount = $request->get('total_net_amount', 0);
        $share->total_expences = $request->get('total_expences', 0);

        $share->hash_id = hash('sha256', uniqid() . time());

        $share->save();

        $this->saveItem($request->get('items'), $share);
        $this->saveExpense($request->get('expenses'), $share);
        $this->computeShared($share);
        //\App\Jobs\SendInvoicEmailJob::dispatch($share);
        //dd($share, $request->all());
        return $this->successResponse('share created', $share);
    }

    private function saveItem($items, $share)
    {

        $share->items()->delete();
        foreach ($items as $i) {
            $item = new SharingItem;
            $item->hash_id = hash('sha256', uniqid() . time());

            $item->sale_id = $i['id'];
            $item->total_amount = $i['total_amount'];
            $item->total_kg = $i['total_kg'];
            $item->sharing_id = $share->id;
            $item->save();
            $sale = Sale::where('id', $i['id'])->first();
            $sale->status = 'Shared';
            $sale->save();
        }
        return true;
    }

    private function saveExpense($items, $share)
    {

        $share->expenses()->delete();
        foreach ($items as $i) {
            $item = new SharingExpense;
            $item->hash_id = hash('sha256', uniqid() . time());
            $item->name = $i['name'];
            $item->total_amount = $i['total_amount'];

            $item->sharing_id = $share->id;
            $item->save();
        }
        return true;
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($hash_id)
    {
        $share = Sharing::where('hash_id', $hash_id)->first();

        if (is_null($share)) {
            abort(404);
        }
        //dd($share);
        return view('shares.show', ['share' => $share]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($hash_id)
    {
        $share = Sharing::where('hash_id', $hash_id)->first();

        if (is_null($share)) {
            abort(404);
        }

        return view('shares.edit', ['share' => $share]);
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
        $share = Sharing::where('hash_id', $hash_id)->first();

        if (is_null($share)) {
            return $this->errorResponse('share not found');
        }

        $share->name = $request->get('name');

        $share->share_price = $request->get('share_price', 0);
        $share->invoice_price = $request->get('invoice_price', 0);
        $share->total_kg = $request->get('total_kg', 0);
        $share->total_amount = $request->get('total_amount', 0);
        $share->total_net_amount = $request->get('total_net_amount', 0);
        $share->total_expences = $request->get('total_expences', 0);

        $share->save();

        $this->saveItem($request->get('items'), $share);
        $this->saveExpense($request->get('expenses'), $share);
        return $this->successResponse('share updated', $share);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($hash_id)
    {
        $share = Sharing::where('hash_id', $hash_id)->first();

        if (is_null($share)) {
            return $this->errorResponse('share not found');
        }
        $items = $share->items;

        $share->items()->delete();
        $share->delete();
        foreach ($items as $item) {
            $sale = Sale::where('id', $item['sale_id'])->first();
            $sale->status = 'Unshared';
            $sale->save();
        }
        return $this->successResponse('share deleted');
    }

    public function search(Request $request)
    {
        $shares = Sharing::with('items', 'expenses')->orderBy('created_at', 'DESC');

        if ($request->has('search')) {
            $search = $request->get('search');
            $shares = $shares->where('name', 'like', '%' . $search . '%');
        }

        $shares = $shares->orderBy('created_at', 'DESC')->paginate(env('PER_PAGE', 20));
        return $this->successResponse('fetch information', $shares);
    }

    public function all()
    {
        $shares = Sharing::get();
        return $this->successResponse('fetch information', $shares);
    }

    public function info($hash_id)
    {
        $share = Sharing::with('items.sale', 'expenses', 'shares.user')->where('hash_id', $hash_id)->first();

        return $this->successResponse('fetch information', $share);
    }

    public function computeShared($sharing)
    {
        $users = User::where('owner', true)->get();

        $per_percentage = 100 / count($users);
        $date = date('Y-m-d 00:00:00');
        $investors = InvestorDetail::where('share_start', '<=', $date)->get();

        $totalAmountInvestor = 0;

        foreach ($investors as $investor) {
            $inv_total_share = 0;
            switch ($investor->share_type) {
                case 'ton':
                    $inv_total_share = $sharing->total_kg * $investor->share_value;

                    $totalAmountInvestor += $inv_total_share;
                    break;
                case 'percentage':
                    $inv_total_share = $sharing->total_amount * ($investor->share_value / 100);

                    $totalAmountInvestor += $inv_total_share;
                    break;
                default:
                    // code...
                    break;
            }
            $shared = new Shared;
            $shared->hash_id = hash('sha256', uniqid());
            $shared->share_type = $investor->share_type;
            $shared->share_value = $investor->share_value;
            $shared->amount = $inv_total_share;
            $shared->user_id = $investor->user_id;
            $shared->sharing_id = $sharing->id;
            $shared->save();
        }

        $remainingAmount = $sharing->total_net_amount - $totalAmountInvestor;

        $ownerAmountPershare = $remainingAmount * ($per_percentage / 100);

        foreach ($users as $user) {
            $shared = new Shared;
            $shared->hash_id = hash('sha256', uniqid());
            $shared->share_type = 'percentage';
            $shared->share_value = $per_percentage;
            $shared->amount = $ownerAmountPershare;
            $shared->user_id = $user->id;
            $shared->sharing_id = $sharing->id;
            $shared->save();
        }

    }

    public function changeStatus($hash_id)
    {
        $share = Sharing::where('hash_id', $hash_id)->first();
        $share->status = 'Paid';
        $share->save();

        $shares = $share->shares;

        foreach ($shares as $sh) {
            $sh->status = 'Paid';
            $sh->save();
        }
        return $this->successResponse('fetch information', $share);
    }

    public function changeStatusShared($hash_id)
    {
        $share = Shared::where('hash_id', $hash_id)->first();
        $share->status = 'Paid';
        $share->save();

        return $this->successResponse('fetch information', $share);
    }
}
