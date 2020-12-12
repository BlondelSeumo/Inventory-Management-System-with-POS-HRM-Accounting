<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Warehouse;
use App\Product;
use App\Product_Warehouse;
use App\Tax;
use App\Unit;
use App\Transfer;
use App\ProductTransfer;
use App\ProductVariant;
use Auth;
use DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class TransferController extends Controller
{
    public function index()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('transfers-index')){
            $permissions = Role::findByName($role->name)->permissions;
            foreach ($permissions as $permission)
                $all_permission[] = $permission->name;
            if(empty($all_permission))
                $all_permission[] = 'dummy text';
            
            if(Auth::user()->role_id > 2 && config('staff_access') == 'own')
                $lims_transfer_all = Transfer::with('fromWarehouse', 'toWarehouse', 'user')->orderBy('id', 'desc')->where('user_id', Auth::id())->get();
            else
                $lims_transfer_all = Transfer::with('fromWarehouse', 'toWarehouse', 'user')->orderBy('id', 'desc')->get();
            return view('transfer.index', compact('lims_transfer_all', 'all_permission'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function create()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('transfers-add')){
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
            return view('transfer.create', compact('lims_warehouse_list'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function getProduct($id)
    {
        $lims_product_warehouse_data = Product::join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')
        ->where([
            ['products.is_active', true],
            ['product_warehouse.warehouse_id', $id],
            ['product_warehouse.qty', '>', 0]
        ])->whereNull('product_warehouse.variant_id')->select('product_warehouse.*')->get();

        $lims_product_with_variant_warehouse_data = Product::join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')
        ->where([
            ['products.is_active', true],
            ['product_warehouse.warehouse_id', $id],
            ['product_warehouse.qty', '>', 0]
        ])->whereNotNull('product_warehouse.variant_id')->select('product_warehouse.*')->get();

        $product_code = [];
        $product_name = [];
        $product_qty = [];
        $product_data = [];
        //product without variant
        foreach ($lims_product_warehouse_data as $product_warehouse) 
        {
            $product_qty[] = $product_warehouse->qty;
            $lims_product_data = Product::select('name', 'code')->find($product_warehouse->product_id);
            $product_code[] =  $lims_product_data->code;
            $product_name[] = $lims_product_data->name;
        }
        //product with variant
        foreach ($lims_product_with_variant_warehouse_data as $product_warehouse) 
        {
            $product_qty[] = $product_warehouse->qty;
            $lims_product_data = Product::select('name', 'code')->find($product_warehouse->product_id);
            $lims_product_variant_data = ProductVariant::select('item_code')->FindExactProduct($product_warehouse->product_id, $product_warehouse->variant_id)->first();
            $product_code[] =  $lims_product_variant_data->item_code;
            $product_name[] = $lims_product_data->name;
        }
        $product_data = [$product_code, $product_name, $product_qty];
        return $product_data;
    }

    public function limsProductSearch(Request $request)
    {
        $product_code = explode("(", $request['data']);
        $product_code[0] = rtrim($product_code[0], " ");
        $product_variant_id = null;
        $lims_product_data = Product::where('code', $product_code[0])->first();
        if(!$lims_product_data) {
            $lims_product_data = Product::join('product_variants', 'products.id', 'product_variants.product_id')
                ->select('products.*', 'product_variants.id as product_variant_id', 'product_variants.item_code')
                ->where('product_variants.item_code', $product_code[0])
                ->first();
            $product_variant_id = $lims_product_data->product_variant_id;
            $lims_product_data->code = $lims_product_data->item_code;
        }
        $product[] = $lims_product_data->name;
        $product[] = $lims_product_data->code;
        $product[] = $lims_product_data->cost;
        
        if ($lims_product_data->tax_id) {
            $lims_tax_data = Tax::find($lims_product_data->tax_id);
            $product[] = $lims_tax_data->rate;
            $product[] = $lims_tax_data->name;
        } else {
            $product[] = 0;
            $product[] = 'No Tax';
        }
        $product[] = $lims_product_data->tax_method;

        $units = Unit::where("base_unit", $lims_product_data->unit_id)
                    ->orWhere('id', $lims_product_data->unit_id)
                    ->get();
        $unit_name = array();
        $unit_operator = array();
        $unit_operation_value = array();
        foreach ($units as $unit) {
            if ($lims_product_data->purchase_unit_id == $unit->id) {
                array_unshift($unit_name, $unit->unit_name);
                array_unshift($unit_operator, $unit->operator);
                array_unshift($unit_operation_value, $unit->operation_value);
            } else {
                $unit_name[]  = $unit->unit_name;
                $unit_operator[] = $unit->operator;
                $unit_operation_value[] = $unit->operation_value;
            }
        }
        
        $product[] = implode(",", $unit_name) . ',';
        $product[] = implode(",", $unit_operator) . ',';
        $product[] = implode(",", $unit_operation_value) . ',';
        $product[] = $lims_product_data->id;
        $product[] = $product_variant_id;
        return $product;
    }

    public function store(Request $request)
    {
        $data = $request->except('document');
        //return dd($data);
        $data['user_id'] = Auth::id();
        $data['reference_no'] = 'tr-' . date("Ymd") . '-'. date("his");
        $document = $request->document;
        if ($document) {
            $v = Validator::make(
                [
                    'extension' => strtolower($request->document->getClientOriginalExtension()),
                ],
                [
                    'extension' => 'in:jpg,jpeg,png,gif,pdf,csv,docx,xlsx,txt',
                ]
            );
            if ($v->fails())
                return redirect()->back()->withErrors($v->errors());

            $documentName = $document->getClientOriginalName();
            $document->move('public/documents/transfer', $documentName);
            $data['document'] = $documentName;
        }
        Transfer::create($data);

        $lims_transfer_data = Transfer::latest()->first();
        $product_id = $data['product_id'];
        $product_code = $data['product_code'];
        $qty = $data['qty'];
        $purchase_unit = $data['purchase_unit'];
        $net_unit_cost = $data['net_unit_cost'];
        $tax_rate = $data['tax_rate'];
        $tax = $data['tax'];
        $total = $data['subtotal'];
        $product_transfer = [];

        foreach ($product_id as $i => $id) {
            $lims_purchase_unit_data  = Unit::where('unit_name', $purchase_unit[$i])->first();

            if($data['status'] != 2){
                if ($lims_purchase_unit_data->operator == '*')
                    $quantity = $qty[$i] * $lims_purchase_unit_data->operation_value;
                else 
                    $quantity = $qty[$i] / $lims_purchase_unit_data->operation_value;
            }
            else
                $quantity = 0;
            //get product data
            $lims_product_data = Product::select('is_variant')->find($id);
            if($lims_product_data->is_variant) {
                $lims_product_variant_data = ProductVariant::select('variant_id')->FindExactProductWithCode($id, $product_code[$i])->first();
                $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($id, $lims_product_variant_data->variant_id, $data['from_warehouse_id'])->first();
                $product_transfer['variant_id'] = $lims_product_variant_data->variant_id;
            }
            else {
                $lims_product_warehouse_data = Product_Warehouse::where([
                    ['product_id', $id],
                    ['warehouse_id', $data['from_warehouse_id'] ],
                    ])->first();
                $product_transfer['variant_id'] = null;
            }
            //deduct quantity from sending warehouse
            $lims_product_warehouse_data->qty -= $quantity;
            $lims_product_warehouse_data->save();
            
            if($data['status'] == 1){
                if($lims_product_data->is_variant) {
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($id, $lims_product_variant_data->variant_id, $data['to_warehouse_id'])->first();
                }
                else {
                    $lims_product_warehouse_data = Product_Warehouse::where([
                        ['product_id', $id],
                        ['warehouse_id', $data['to_warehouse_id'] ],
                    ])->first();
                }
                //add quantity to destination warehouse
                if ($lims_product_warehouse_data)
                    $lims_product_warehouse_data->qty += $quantity;
                else {
                    $lims_product_warehouse_data = new Product_Warehouse();
                    $lims_product_warehouse_data->product_id = $id;
                    $lims_product_warehouse_data->variant_id = $product_transfer['variant_id'];
                    $lims_product_warehouse_data->warehouse_id = $data['to_warehouse_id'];
                    $lims_product_warehouse_data->qty = $quantity;
                }

                $lims_product_warehouse_data->save();
            }

            $product_transfer['transfer_id'] = $lims_transfer_data->id ;
            $product_transfer['product_id'] = $id;
            $product_transfer['qty'] = $qty[$i];
            $product_transfer['purchase_unit_id'] = $lims_purchase_unit_data->id;
            $product_transfer['net_unit_cost'] = $net_unit_cost[$i];
            $product_transfer['tax_rate'] = $tax_rate[$i];
            $product_transfer['tax'] = $tax[$i];
            $product_transfer['total'] = $total[$i];
            ProductTransfer::create($product_transfer);
        }

        return redirect('transfers')->with('message', 'Transfer created successfully');
    }

    public function productTransferData($id)
    {
        $lims_product_transfer_data = ProductTransfer::where('transfer_id', $id)->get();
        foreach ($lims_product_transfer_data as $key => $product_transfer_data) {
            $product = Product::find($product_transfer_data->product_id);
            $unit = Unit::find($product_transfer_data->purchase_unit_id);
            if($product_transfer_data->variant_id) {
                $lims_product_variant_data = ProductVariant::select('item_code')->FindExactProduct($product_transfer_data->product_id, $product_transfer_data->variant_id)->first();
                $product->code = $lims_product_variant_data->item_code;
            }
            $product_transfer[0][$key] = $product->name . ' [' . $product->code. ']';
            $product_transfer[1][$key] = $product_transfer_data->qty;
            $product_transfer[2][$key] = $unit->unit_code;
            $product_transfer[3][$key] = $product_transfer_data->tax;
            $product_transfer[4][$key] = $product_transfer_data->tax_rate;
            $product_transfer[5][$key] = $product_transfer_data->total;
        }
        return $product_transfer;
    }

    public function transferByCsv()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('transfers-add')){
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
            return view('transfer.import', compact('lims_warehouse_list'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function importTransfer(Request $request)
    {
        //get the file
        $upload=$request->file('file');
        $ext = pathinfo($upload->getClientOriginalName(), PATHINFO_EXTENSION);
        //checking if this is a CSV file
        if($ext != 'csv')
            return redirect()->back()->with('message', 'Please upload a CSV file');

        $filePath=$upload->getRealPath();
        $file_handle = fopen($filePath, 'r');
        $i = 0;
        //validate the file
        while (!feof($file_handle) ) {
            $current_line = fgetcsv($file_handle);
            if($current_line && $i > 0){
                $product_data[] = Product::where('code', $current_line[0])->first();
                if(!$product_data[$i-1])
                    return redirect()->back()->with('message', 'Product does not exist!');
                $unit[] = Unit::where('unit_code', $current_line[2])->first();
                if(!$unit[$i-1])
                    return redirect()->back()->with('message', 'Purchase unit does not exist!');
                if(strtolower($current_line[4]) != "no tax"){
                    $tax[] = Tax::where('name', $current_line[4])->first();
                    if(!$tax[$i-1])
                        return redirect()->back()->with('message', 'Tax name does not exist!');
                }
                else
                    $tax[$i-1]['rate'] = 0;

                $qty[] = $current_line[1];
                $cost[] = $current_line[3];
            }
            $i++;
        }

        $data = $request->except('file');
        $data['reference_no'] = 'tr-' . date("Ymd") . '-'. date("his");
        $document = $request->document;
        if ($document) {
            $v = Validator::make(
                [
                    'extension' => strtolower($request->document->getClientOriginalExtension()),
                ],
                [
                    'extension' => 'in:jpg,jpeg,png,gif,pdf,csv,docx,xlsx,txt',
                ]
            );
            if ($v->fails())
                return redirect()->back()->withErrors($v->errors());
            
            $ext = pathinfo($document->getClientOriginalName(), PATHINFO_EXTENSION);
            $documentName = $data['reference_no'] . '.' . $ext;
            $document->move('public/documents/transfer', $documentName);
            $data['document'] = $documentName;
        }
        $item = 0;
        $grand_total = $data['shipping_cost'];
        $data['user_id'] = Auth::id();
        Transfer::create($data);
        $lims_transfer_data = Transfer::latest()->first();
        
        foreach ($product_data as $key => $product) {
            if($product['tax_method'] == 1){
                $net_unit_cost = $cost[$key];
                $product_tax = $net_unit_cost * ($tax[$key]['rate'] / 100) * $qty[$key];
                $total = ($net_unit_cost * $qty[$key]) + $product_tax;
            }
            elseif($product['tax_method'] == 2){
                $net_unit_cost = (100 / (100 + $tax[$key]['rate'])) * $cost[$key];
                $product_tax = ($cost[$key] - $net_unit_cost) * $qty[$key];
                $total = $cost[$key] * $qty[$key];
            }
            if($data['status'] == 1){
                if($unit[$key]['operator'] == '*')
                    $quantity = $qty[$key] * $unit[$key]['operation_value'];
                elseif($unit[$key]['operator'] == '/')
                    $quantity = $qty[$key] / $unit[$key]['operation_value'];
                $product_warehouse = Product_Warehouse::where([
                    ['product_id', $product['id']],
                    ['warehouse_id', $data['from_warehouse_id']]
                ])->first();
                $product_warehouse->qty -= $quantity;
                $product_warehouse->save();
                $product_warehouse = Product_Warehouse::where([
                    ['product_id', $product['id']],
                    ['warehouse_id', $data['to_warehouse_id']]
                ])->first();
                if($product_warehouse) {
                    $product_warehouse->qty += $quantity;
                    $product_warehouse->save();
                }
                else {
                    $product_warehouse = new Product_Warehouse();
                    $product_warehouse->product_id = $product['id'];
                    $product_warehouse->warehouse_id = $data['to_warehouse_id'];
                    $product_warehouse->qty = $quantity;
                    $product_warehouse->save();
                }
            }
            elseif ($data['status'] == 3) {
                if($unit[$key]['operator'] == '*')
                    $quantity = $qty[$key] * $unit[$key]['operation_value'];
                elseif($unit[$key]['operator'] == '/')
                    $quantity = $qty[$key] / $unit[$key]['operation_value'];
                $product_warehouse = Product_Warehouse::where([
                    ['product_id', $product['id']],
                    ['warehouse_id', $data['from_warehouse_id']]
                ])->first();
                $product_warehouse->qty -= $quantity;
                $product_warehouse->save();
            }
            
            $product_transfer = new ProductTransfer();
            $product_transfer->transfer_id = $lims_transfer_data->id;
            $product_transfer->product_id = $product['id'];
            $product_transfer->qty = $qty[$key];
            $product_transfer->purchase_unit_id = $unit[$key]['id'];
            $product_transfer->net_unit_cost = number_format((float)$net_unit_cost, 2, '.', '');
            $product_transfer->tax_rate = $tax[$key]['rate'];
            $product_transfer->tax = number_format((float)$product_tax, 2, '.', '');
            $product_transfer->total = number_format((float)$total, 2, '.', '');
            $product_transfer->save();
            $lims_transfer_data->total_qty += $qty[$key];
            $lims_transfer_data->total_tax += number_format((float)$product_tax, 2, '.', '');
            $lims_transfer_data->total_cost += number_format((float)$total, 2, '.', '');
        }
        $lims_transfer_data->item = $key + 1;
        $lims_transfer_data->grand_total = $lims_transfer_data->total_cost + $lims_transfer_data->shipping_cost;
        $lims_transfer_data->save();
        return redirect('transfers')->with('message', 'Transfer imported successfully');
    }

    public function edit($id)
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('transfers-edit')){
            $lims_warehouse_list = Warehouse::where('is_active',true)->get();
            $lims_transfer_data = Transfer::find($id);
            $lims_product_transfer_data = ProductTransfer::where('transfer_id', $id)->get();
            return view('transfer.edit', compact('lims_warehouse_list', 'lims_transfer_data', 'lims_product_transfer_data'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function update(Request $request, $id)
    {
        $data = $request->except('document');
        //return dd($data);
        $document = $request->document;
        if ($document) {
            $v = Validator::make(
                [
                    'extension' => strtolower($request->document->getClientOriginalExtension()),
                ],
                [
                    'extension' => 'in:jpg,jpeg,png,gif,pdf,csv,docx,xlsx,txt',
                ]
            );
            if ($v->fails())
                return redirect()->back()->withErrors($v->errors());

            $documentName = $document->getClientOriginalName();
            $document->move('public/documents/transfer', $documentName);
            $data['document'] = $documentName;
        }

        $lims_transfer_data = Transfer::find($id);
        $lims_product_transfer_data = ProductTransfer::where('transfer_id', $id)->get();
        $product_id = $data['product_id'];
        $product_variant_id = $data['product_variant_id'];
        $qty = $data['qty'];
        $purchase_unit = $data['purchase_unit'];
        $net_unit_cost = $data['net_unit_cost'];
        $tax_rate = $data['tax_rate'];
        $tax = $data['tax'];
        $total = $data['subtotal'];
        $product_transfer = [];
        foreach ($lims_product_transfer_data as $key => $product_transfer_data) {
            $old_product_id[] = $product_transfer_data->product_id;
            $old_product_variant_id[] = null;
            $lims_transfer_unit_data = Unit::find($product_transfer_data->purchase_unit_id);
            if ($lims_transfer_unit_data->operator == '*') {
                $quantity = $product_transfer_data->qty * $lims_transfer_unit_data->operation_value;
            } else {
                $quantity = $product_transfer_data->qty / $lims_transfer_unit_data->operation_value;
            }
            
            if($lims_transfer_data->status == 1){
                if($product_transfer_data->variant_id) {
                    $lims_product_variant_data = ProductVariant::select('id')->FindExactProduct($product_transfer_data->product_id, $product_transfer_data->variant_id)->first();
                    $lims_product_from_warehouse_data = Product_Warehouse::FindProductWithVariant($product_transfer_data->product_id, $product_transfer_data->variant_id, $lims_transfer_data->from_warehouse_id)->first();
                    $lims_product_to_warehouse_data = Product_Warehouse::FindProductWithVariant($product_transfer_data->product_id, $product_transfer_data->variant_id, $lims_transfer_data->to_warehouse_id)->first();
                    $old_product_variant_id[$key] = $lims_product_variant_data->id;
                }
                else {
                    $lims_product_from_warehouse_data = Product_Warehouse::FindProductWithoutVariant($product_transfer_data->product_id, $lims_transfer_data->from_warehouse_id)->first();
                    $lims_product_to_warehouse_data = Product_Warehouse::FindProductWithoutVariant($product_transfer_data->product_id, $lims_transfer_data->to_warehouse_id)->first();
                }
                    
                $lims_product_from_warehouse_data->qty += $quantity;
                $lims_product_from_warehouse_data->save();

                $lims_product_to_warehouse_data->qty -= $quantity;
                $lims_product_to_warehouse_data->save();
            }
            elseif($lims_transfer_data->status == 3){
                if($product_transfer_data->variant_id) {
                    $lims_product_variant_data = ProductVariant::select('id')->FindExactProduct($product_transfer_data->product_id, $product_transfer_data->variant_id)->first();
                    $lims_product_from_warehouse_data = Product_Warehouse::FindProductWithVariant($product_transfer_data->product_id, $product_transfer_data->variant_id, $lims_transfer_data->from_warehouse_id)->first();
                    $old_product_variant_id[$key] = $lims_product_variant_data->id;
                }
                else {
                    $lims_product_from_warehouse_data = Product_Warehouse::FindProductWithoutVariant($product_transfer_data->product_id, $lims_transfer_data->from_warehouse_id)->first();
                }
                $lims_product_from_warehouse_data->qty += $quantity;
                $lims_product_from_warehouse_data->save();
            }
            
            if($product_transfer_data->variant_id && !(in_array($old_product_variant_id[$key], $product_variant_id)) ){
                $product_transfer_data->delete();
            }
            elseif( !(in_array($old_product_id[$key], $product_id)) ){
                $product_transfer_data->delete();
            }
        }

        foreach ($product_id as $key => $pro_id) {
            $lims_product_data = Product::select('is_variant')->find($pro_id);
            $lims_transfer_unit_data = Unit::where('unit_name', $purchase_unit[$key])->first();
            $variant_id = null;
            //unit conversion
            if ($lims_transfer_unit_data->operator == '*') {
                $quantity = $qty[$key] * $lims_transfer_unit_data->operation_value;
            } else {
                $quantity = $qty[$key] / $lims_transfer_unit_data->operation_value;
            }

            if($data['status'] == 1) {
                if($lims_product_data->is_variant) {
                    $lims_product_variant_data = ProductVariant::select('variant_id')->find($product_variant_id[$key]);
                    $lims_product_from_warehouse_data = Product_Warehouse::FindProductWithVariant($pro_id, $lims_product_variant_data->variant_id, $data['from_warehouse_id'])->first();
                    $lims_product_to_warehouse_data = Product_Warehouse::FindProductWithVariant($pro_id, $lims_product_variant_data->variant_id, $data['to_warehouse_id'])->first();
                    $variant_id = $lims_product_variant_data->variant_id;
                }
                else{
                    $lims_product_from_warehouse_data = Product_Warehouse::FindProductWithoutVariant($pro_id, $data['from_warehouse_id'])->first();
                    $lims_product_to_warehouse_data = Product_Warehouse::FindProductWithoutVariant($pro_id, $data['to_warehouse_id'])->first();                    
                }

                $lims_product_from_warehouse_data->qty -= $quantity;
                $lims_product_from_warehouse_data->save();

                if($lims_product_to_warehouse_data){
                    $lims_product_to_warehouse_data->qty += $quantity;
                    $lims_product_to_warehouse_data->save();
                }
                else{
                    $lims_product_warehouse_data = new Product_Warehouse();
                    $lims_product_warehouse_data->product_id = $pro_id;
                    $lims_product_warehouse_data->variant_id = $variant_id;
                    $lims_product_warehouse_data->warehouse_id = $data['to_warehouse_id'];
                    $lims_product_warehouse_data->qty = $quantity;
                    $lims_product_warehouse_data->save();
                }
                
            }
            elseif($data['status'] == 3) {
                if($lims_product_data->is_variant) {
                    $lims_product_variant_data = ProductVariant::select('variant_id')->find($product_variant_id[$key]);
                    $lims_product_from_warehouse_data = Product_Warehouse::FindProductWithVariant($pro_id, $lims_product_variant_data->variant_id, $data['from_warehouse_id'])->first();
                    $variant_id = $lims_product_variant_data->variant_id;
                }
                else{
                    $lims_product_from_warehouse_data = Product_Warehouse::FindProductWithoutVariant($pro_id, $data['from_warehouse_id'])->first();
                }

                $lims_product_from_warehouse_data->qty -= $quantity;
                $lims_product_from_warehouse_data->save();
            }

            $product_transfer['product_id'] = $pro_id;
            $product_transfer['variant_id'] = $variant_id;
            $product_transfer['transfer_id'] = $id;
            $product_transfer['qty'] = $qty[$key];
            $product_transfer['purchase_unit_id'] = $lims_transfer_unit_data->id;
            $product_transfer['net_unit_cost'] = $net_unit_cost[$key];
            $product_transfer['tax_rate'] = $tax_rate[$key];
            $product_transfer['tax'] = $tax[$key];
            $product_transfer['total'] = $total[$key];
            
            if($lims_product_data->is_variant && in_array($product_variant_id[$key], $old_product_variant_id) ) {
                ProductTransfer::where([
                    ['transfer_id', $id],
                    ['product_id', $pro_id],
                    ['variant_id', $variant_id]
                ])->update($product_transfer);
            }
            elseif($variant_id == null && in_array($pro_id, $old_product_id) ){
                ProductTransfer::where([
                    ['transfer_id', $id],
                    ['product_id', $pro_id]
                ])->update($product_transfer);
            }
            else
                ProductTransfer::create($product_transfer);
        }

        $lims_transfer_data->update($data);
        return redirect('transfers')->with('message', 'Transfer updated successfully');
    }

    public function deleteBySelection(Request $request)
    {
        $transfer_id = $request['transferIdArray'];
        foreach ($transfer_id as $id) {
            $lims_transfer_data =Transfer::find($id);
            $lims_product_transfer_data = ProductTransfer::where('transfer_id', $id)->get();
            foreach ($lims_product_transfer_data as $product_transfer_data) {
                $lims_transfer_unit_data = Unit::find($product_transfer_data->purchase_unit_id);
                if ($lims_transfer_unit_data->operator == '*') {
                    $quantity = $product_transfer_data->qty * $lims_transfer_unit_data->operation_value;
                } else {
                    $quantity = $product_transfer_data / $lims_transfer_unit_data->operation_value;
                }

                if($lims_transfer_data->status == 1) {
                    //add quantity for from warehouse
                    if($product_transfer_data->variant_id)
                        $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($product_transfer_data->product_id, $product_transfer_data->variant_id, $lims_transfer_data->from_warehouse_id)->first();
                    else
                        $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($product_transfer_data->product_id, $lims_transfer_data->from_warehouse_id)->first();
                    $lims_product_warehouse_data->qty += $quantity;
                    $lims_product_warehouse_data->save();
                    //deduct quantity for to warehouse
                    if($product_transfer_data->variant_id)
                        $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($product_transfer_data->product_id, $product_transfer_data->variant_id, $lims_transfer_data->to_warehouse_id)->first();
                    else
                        $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($product_transfer_data->product_id, $lims_transfer_data->to_warehouse_id)->first();

                    $lims_product_warehouse_data->qty -= $quantity;
                    $lims_product_warehouse_data->save();
                }
                elseif($lims_transfer_data->status == 3) {
                    //add quantity for from warehouse
                    if($product_transfer_data->variant_id)
                        $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($product_transfer_data->product_id, $product_transfer_data->variant_id, $lims_transfer_data->from_warehouse_id)->first();
                    else
                        $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($product_transfer_data->product_id, $lims_transfer_data->from_warehouse_id)->first();

                    $lims_product_warehouse_data->qty += $quantity;
                    $lims_product_warehouse_data->save();
                }
                $product_transfer_data->delete();
            }
            $lims_transfer_data->delete();
        }
        return 'Transfer deleted successfully!';
    }

    public function destroy($id)
    {
        $lims_transfer_data =Transfer::find($id);
        $lims_product_transfer_data = ProductTransfer::where('transfer_id', $id)->get();
        foreach ($lims_product_transfer_data as $product_transfer_data) {
            $lims_transfer_unit_data = Unit::find($product_transfer_data->purchase_unit_id);
            if ($lims_transfer_unit_data->operator == '*') {
                $quantity = $product_transfer_data->qty * $lims_transfer_unit_data->operation_value;
            } else {
                $quantity = $product_transfer_data / $lims_transfer_unit_data->operation_value;
            }

            if($lims_transfer_data->status == 1) {
                //add quantity for from warehouse
                if($product_transfer_data->variant_id)
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($product_transfer_data->product_id, $product_transfer_data->variant_id, $lims_transfer_data->from_warehouse_id)->first();
                else
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($product_transfer_data->product_id, $lims_transfer_data->from_warehouse_id)->first();
                $lims_product_warehouse_data->qty += $quantity;
                $lims_product_warehouse_data->save();
                //deduct quantity for to warehouse
                if($product_transfer_data->variant_id)
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($product_transfer_data->product_id, $product_transfer_data->variant_id, $lims_transfer_data->to_warehouse_id)->first();
                else
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($product_transfer_data->product_id, $lims_transfer_data->to_warehouse_id)->first();

                $lims_product_warehouse_data->qty -= $quantity;
                $lims_product_warehouse_data->save();
            }
            elseif($lims_transfer_data->status == 3) {
                //add quantity for from warehouse
                if($product_transfer_data->variant_id)
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($product_transfer_data->product_id, $product_transfer_data->variant_id, $lims_transfer_data->from_warehouse_id)->first();
                else
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($product_transfer_data->product_id, $lims_transfer_data->from_warehouse_id)->first();

                $lims_product_warehouse_data->qty += $quantity;
                $lims_product_warehouse_data->save();
            }
            $product_transfer_data->delete();
        }
        $lims_transfer_data->delete();
        return redirect('transfers')->with('not_permitted', 'Transfer deleted successfully');
    }
}
