<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Warehouse;
use App\Brand;
use App\Category;
use App\Product;
use DB;
use App\StockCount;
use Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class StockCountController extends Controller
{
    public function index()
    {
        $role = Role::find(Auth::user()->role_id);
        if( $role->hasPermissionTo('stock_count') ) {
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
            $lims_brand_list = Brand::where('is_active', true)->get();
            $lims_category_list = Category::where('is_active', true)->get();
            $general_setting = DB::table('general_settings')->latest()->first();
            if(Auth::user()->role_id > 2 && $general_setting->staff_access == 'own')
                $lims_stock_count_all = StockCount::orderBy('id', 'desc')->where('user_id', Auth::id())->get();
            else
                $lims_stock_count_all = StockCount::orderBy('id', 'desc')->get();

            return view('stock_count.index', compact('lims_warehouse_list', 'lims_brand_list', 'lims_category_list', 'lims_stock_count_all'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        if( isset($data['brand_id']) && isset($data['category_id']) ){
            $lims_product_list = DB::table('products')->join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')->whereIn('products.category_id', $data['category_id'] )->whereIn('products.brand_id', $data['brand_id'] )->where([ ['products.is_active', true], ['product_warehouse.warehouse_id', $data['warehouse_id']] ])->select('products.name', 'products.code', 'product_warehouse.qty')->get();

            $data['category_id'] = implode(",", $data['category_id']);
            $data['brand_id'] = implode(",", $data['brand_id']);
        }
        elseif( isset($data['category_id']) ){
            $lims_product_list = DB::table('products')->join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')->whereIn('products.category_id', $data['category_id'])->where([ ['products.is_active', true], ['product_warehouse.warehouse_id', $data['warehouse_id']] ])->select('products.name', 'products.code', 'product_warehouse.qty')->get();

            $data['category_id'] = implode(",", $data['category_id']);
        }
        elseif( isset($data['brand_id']) ){
            $lims_product_list = DB::table('products')->join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')->whereIn('products.brand_id', $data['brand_id'])->where([ ['products.is_active', true], ['product_warehouse.warehouse_id', $data['warehouse_id']] ])->select('products.name', 'products.code', 'product_warehouse.qty')->get();

            $data['brand_id'] = implode(",", $data['brand_id']);
        }
        else{
            $lims_product_list = DB::table('products')->join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')->where([ ['products.is_active', true], ['product_warehouse.warehouse_id', $data['warehouse_id']] ])->select('products.name', 'products.code', 'product_warehouse.qty')->get();
        }
        if( count($lims_product_list) ){
            $csvData=array('Product Name, Product Code, Expected, Counted');
            foreach ($lims_product_list as $product) {
                $csvData[]=$product->name.','.$product->code.','.$product->qty.','.'';
            }
            $filename= date('Ymd').'-'.date('his'). ".csv";
            $file_path= public_path().'/stock_count/'.$filename;
            $file = fopen($file_path, "w+");
            foreach ($csvData as $cellData){
              fputcsv($file, explode(',', $cellData));
            }
            fclose($file);

            $data['user_id'] = Auth::id();
            $data['reference_no'] = 'scr-' . date("Ymd") . '-'. date("his");
            $data['initial_file'] = $filename;
            $data['is_adjusted'] = false;
            StockCount::create($data);
            return redirect()->back()->with('message', 'Stock Count created successfully! Please download the initial file to complete it.');
        }
        else
            return redirect()->back()->with('not_permitted', 'No product found!');
    }

    public function finalize(Request $request)
    {
        $ext = pathinfo($request->final_file->getClientOriginalName(), PATHINFO_EXTENSION);
        //checking if this is a CSV file
        if($ext != 'csv')
            return redirect()->back()->with('not_permitted', 'Please upload a CSV file');

        $data = $request->all();
        $document = $request->final_file;
        $documentName = date('Ymd').'-'.date('his'). ".csv";
        $document->move('public/stock_count/', $documentName);
        $data['final_file'] = $documentName;
        $lims_stock_count_data = StockCount::find($data['stock_count_id']);
        $lims_stock_count_data->update($data);
        return redirect()->back()->with('message', 'Stock Count finalized successfully!');
    }

    public function stockDif($id)
    {
        $lims_stock_count_data = StockCount::find($id);
        $file_handle = fopen('public/stock_count/'.$lims_stock_count_data->final_file, 'r');
        $i = 0;
        $temp_dif = -1000000;
        $data = [];
        $product = [];
        while( !feof($file_handle) ) {
            $current_line = fgetcsv($file_handle);
            if( $current_line && $i > 0 && ($current_line[2] != $current_line[3]) ){
                $product[] = $current_line[0].' ['.$current_line[1].']';
                $expected[] = $current_line[2];
                $product_data = Product::where('code', $current_line[1])->first();
                
                if($current_line[3]){
                    $difference[] = $temp_dif = $current_line[3] - $current_line[2];
                    $counted[] = $current_line[3];
                }
                else{
                    $difference[] = $temp_dif = $current_line[2] * (-1);
                    $counted[] = 0;
                }
                $cost[] = $product_data->cost * $temp_dif;
            }
            $i++;
        }
        if($temp_dif == -1000000){
            $lims_stock_count_data->is_adjusted = true;
            $lims_stock_count_data->save();
        }
        if( count($product) ) {
            $data[] = $product;
            $data[] = $expected;
            $data[] = $counted;
            $data[] = $difference;
            $data[] = $cost;
            $data[] = $lims_stock_count_data->is_adjusted;
        }
        return $data;
    }

    public function qtyAdjustment($id)
    {
        $lims_warehouse_list = Warehouse::where('is_active', true)->get();
        $lims_stock_count_data = StockCount::find($id);
        $warehouse_id = $lims_stock_count_data->warehouse_id;
        $file_handle = fopen('public/stock_count/'.$lims_stock_count_data->final_file, 'r');
        $i = 0;
        $product_id = [];
        while( !feof($file_handle) ) {
            $current_line = fgetcsv($file_handle);
            if( $current_line && $i > 0 && ($current_line[2] != $current_line[3]) ){
                $product_data = Product::where('code', $current_line[1])->first();
                $product_id[] = $product_data->id;
                $names[] = $current_line[0];
                $code[] = $current_line[1];

                if($current_line[3])
                    $temp_qty = $current_line[3] - $current_line[2];
                else
                    $temp_qty = $current_line[2] * (-1);

                if($temp_qty < 0){
                    $qty[] = $temp_qty * (-1);
                    $action[] = '-';  
                }
                else{
                    $qty[] = $temp_qty;
                    $action[] = '+';
                }
            }
            $i++;
        }
        return view('stock_count.qty_adjustment', compact('lims_warehouse_list', 'warehouse_id', 'id', 'product_id', 'names', 'code', 'qty', 'action'));
    }
    public function destroy($id)
    {
        //
    }
}
