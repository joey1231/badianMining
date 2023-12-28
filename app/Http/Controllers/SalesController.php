<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleExpense;
use App\Models\SaleItem;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('sales.list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function report()
    {
        return view('sales.report');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('sales.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $sale = new Sale;
        $sale->name = $request->get('name');

        $sale->sale_price = $request->get('sale_price', 0);

        $sale->total_kg = $request->get('total_kg', 0);
        $sale->total_amount = $request->get('total_amount', 0);
        $sale->total_net_amount = $request->get('total_net_amount', 0);
        $sale->total_expences = $request->get('total_expences', 0);

        $sale->hash_id = hash('sha256', uniqid() . time());

        $sale->save();

        $this->saveItem($request->get('items'), $sale);
        $this->saveExpense($request->get('expenses'), $sale);
        //\App\Jobs\SendInvoicEmailJob::dispatch($sale);
        //dd($sale, $request->all());
        return $this->successResponse('sale created', $sale);
    }

    private function saveItem($items, $sale)
    {

        $sale->items()->delete();
        foreach ($items as $i) {
            $item = new SaleItem;
            $item->hash_id = hash('sha256', uniqid() . time());

            $item->item_date = $i['item_date'];
            $item->total_kg = $i['total_kg'];
            $item->sale_id = $sale->id;
            $item->save();
        }
        return true;
    }

    private function saveExpense($items, $sale)
    {

        $sale->expenses()->delete();
        foreach ($items as $i) {
            $item = new SaleExpense;
            $item->hash_id = hash('sha256', uniqid() . time());
            $item->name = $i['name'];
            $item->total_amount = $i['total_amount'];

            $item->sale_id = $sale->id;
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
        $sale = Sale::where('hash_id', $hash_id)->first();

        if (is_null($sale)) {
            abort(404);
        }
        //dd($sale);
        return view('sales.show', ['sale' => $sale]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($hash_id)
    {
        $sale = Sale::where('hash_id', $hash_id)->first();

        if (is_null($sale)) {
            abort(404);
        }

        return view('sales.edit', ['sale' => $sale]);
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
        $sale = Sale::where('hash_id', $hash_id)->first();

        if (is_null($sale)) {
            return $this->errorResponse('sale not found');
        }

        $sale->name = $request->get('name');

        $sale->sale_price = $request->get('sale_price', 0);

        $sale->total_kg = $request->get('total_kg', 0);
        $sale->total_amount = $request->get('total_amount', 0);
        $sale->total_net_amount = $request->get('total_net_amount', 0);
        $sale->total_expences = $request->get('total_expences', 0);

        $sale->total_invoice_price_e_vat = $request->get('total_invoice_price_e_vat', 0);
        $sale->total_invoice_price_w_vat = $request->get('total_invoice_price_w_vat', 0);
        $sale->total_invoice_vat = $request->get('total_invoice_vat', 0);
        $sale->total_invoice_wh_tax = $request->get('total_invoice_wh_tax', 0);
        $sale->total_invoice_amount_due_w_wh_tax = $request->get('total_invoice_amount_due_w_wh_tax', 0);
        $sale->total_with_devided_1_12 = $request->get('total_with_devided_1_12', 0);
        $sale->total_invoice_amount = $request->get('total_invoice_amount', 0);
        $sale->save();

        $this->saveItem($request->get('items'), $sale);
        $this->saveExpense($request->get('expenses'), $sale);
        return $this->successResponse('sale updated', $sale);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($hash_id)
    {
        $sale = Sale::where('hash_id', $hash_id)->first();

        if (is_null($sale)) {
            return $this->errorResponse('sale not found');
        }

        $sale->delete();
        return $this->successResponse('sale deleted');
    }

    public function search(Request $request)
    {
        $sales = Sale::with('items', 'expenses')->orderBy('created_at', 'DESC');

        if ($request->has('search')) {
            $search = $request->get('search');
            $sales = $sales->where('name', 'like', '%' . $search . '%');
        }

        $sales = $sales->orderBy('created_at', 'DESC')->paginate(env('PER_PAGE', 20));
        return $this->successResponse('fetch information', $sales);
    }

    public function all()
    {
        $sales = Sale::get();
        return $this->successResponse('fetch information', $sales);
    }

    public function info($hash_id)
    {
        $sale = Sale::with('items', 'expenses')->where('hash_id', $hash_id)->first();

        return $this->successResponse('fetch information', $sale);
    }

    public function downloadSale($hash_id)
    {
        $sale = Sale::where('hash_id', $hash_id)->first();

        //return view('downloads.pdf.sale', ['sale' => $sale]);
        $pdf = \App\Utils\Util::downloadPdfFileSale($sale, 'downloads.pdf.sale', 'sale-' . $sale->name . '.pdf');
        return $pdf->stream('sale-' . $sale->name . '.pdf');
    }

    public function resend($hash_id)
    {
        $sale = Sale::where('hash_id', $hash_id)->first();
        $sale->queue_status = 'queued';
        $sale->save();
        \App\Jobs\SendInvoicEmailJob::dispatch($sale);
        return $this->successResponse('fetch information', $sale);
    }

    public function getSalesbyStatus($status)
    {
        $sales = Sale::orderBy('created_at', 'DESC');

        $sales = $sales->where('status', $status);

        $sales = $sales->get();
        return $this->successResponse('fetch information', $sales);
    }
}
