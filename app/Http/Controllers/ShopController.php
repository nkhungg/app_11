<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $order = $request->query('order') ? $request->query('order') : -1;
        $o_column = "";
        $o_order = "";
        switch($order)
        {
            case 1:
                $o_column='name';
                $o_order='DESC';
                break;
            case 2:
                $o_column='regular_price';
                $o_order='ASC';
                break;
            case 3:
                $o_column='regular_price';
                $o_order='DESC';
                break;
            default:
                $o_column='name';
                $o_order='ASC';
                break;
        }
        $products = Product::orderBy($o_column, $o_order)->paginate(12);
        return view('shop', compact('products', 'order'));
    }

    public function product_details($product_slug)
    {
        $product = Product::where('slug', $product_slug)->first();
        $related_products = Product::where('slug', '<>', $product_slug)->get()->take(8);
        return view('details', compact('product', 'related_products'));
    }
}
