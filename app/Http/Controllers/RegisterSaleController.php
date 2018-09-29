<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

use \App\Models\Admin\Sale;

class RegisterSaleController extends Controller
{
    public function index(Request $request)
    {
        return view('registerSale.index');
    }

    public function register_sale(Request $request)
    {
        if (Auth::check()) {

            $sale_data = $request->all();
            $sale = new Sale();
            $sale->fill($sale_data['personalInfo']);
            $sale->age = $sale_data['age'];
            $sale->seller_id = Auth::user()->id;
            $sale->packageType = $sale_data['packageType'];
            $sale->agePrice = $sale_data['agePrice'];

            if($sale->save())
                return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 401);
    }
}
