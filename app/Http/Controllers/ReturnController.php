<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Customer;
use App\CustomerGroup;
use App\Warehouse;
use App\Biller;
use App\Product;
use App\Unit;
use App\Tax;
use App\Product_Warehouse;
use DB;
use App\Returns;
use App\Account;
use App\ProductReturn;
use App\ProductVariant;
use App\Variant;
use App\CashRegister;
use Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Mail\UserNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ReturnController extends Controller
{
    public function index()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('returns-index')){
            $permissions = Role::findByName($role->name)->permissions;
            foreach ($permissions as $permission)
                $all_permission[] = $permission->name;
            if(empty($all_permission))
                $all_permission[] = 'dummy text';
            
            if(Auth::user()->role_id > 2 && config('staff_access') == 'own')
                $lims_return_all = Returns::with('biller', 'customer', 'warehouse', 'user')->orderBy('id', 'desc')->orderBy('id', 'desc')->where('user_id', Auth::id())->get();
            else
                $lims_return_all = Returns::with('biller', 'customer', 'warehouse', 'user')->orderBy('id', 'desc')->get();
            return view('return.index', compact('lims_return_all', 'all_permission'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function create()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('returns-add')){
            $lims_customer_list = Customer::where('is_active',true)->get();
            if(Auth::user()->role_id <= 2) {
                $lims_warehouse_list = Warehouse::where('is_active',true)->get();
                $lims_biller_list = Biller::where('is_active',true)->get();
            }
            else {
                $lims_warehouse_list = Warehouse::where('id',Auth::user()->warehouse_id)->get();
                $lims_biller_list = Biller::where('id', Auth::user()->biller_id)->get();
            }
            $lims_tax_list = Tax::where('is_active',true)->get();
            return view('return.create', compact('lims_customer_list', 'lims_warehouse_list', 'lims_biller_list', 'lims_tax_list'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function getCustomerGroup($id)
    {
         $lims_customer_data = Customer::find($id);
         $lims_customer_group_data = CustomerGroup::find($lims_customer_data->customer_group_id);
         return $lims_customer_group_data->percentage;
    }

    public function getProduct($id)
    {
        //retrieve data of product without variant
        $lims_product_warehouse_data = Product::join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')
        ->where([
            ['products.is_active', true],
            ['product_warehouse.warehouse_id', $id],
        ])->whereNull('product_warehouse.variant_id')->select('product_warehouse.*')->get();
        //retrieve data of product with variant
        $lims_product_with_variant_warehouse_data = Product::join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')
        ->where([
            ['products.is_active', true],
            ['product_warehouse.warehouse_id', $id],
        ])->whereNotNull('product_warehouse.variant_id')->select('product_warehouse.*')->get();

        $product_code = [];
        $product_name = [];
        $product_qty = [];
        $product_price = [];
        $product_data = [];
        foreach ($lims_product_warehouse_data as $product_warehouse) 
        {
            $product_qty[] = $product_warehouse->qty;
            $product_price[] = $product_warehouse->price;
            $lims_product_data = Product::select('code', 'name', 'type')->find($product_warehouse->product_id);
            $product_code[] =  $lims_product_data->code;
            $product_name[] = htmlspecialchars($lims_product_data->name);
            $product_type[] = $lims_product_data->type;
        }
        foreach ($lims_product_with_variant_warehouse_data as $product_warehouse) 
        {
            $product_qty[] = $product_warehouse->qty;
            $lims_product_data = Product::select('name', 'type')->find($product_warehouse->product_id);
            $lims_product_variant_data = ProductVariant::select('item_code')->FindExactProduct($product_warehouse->product_id, $product_warehouse->variant_id)->first();
            $product_code[] =  $lims_product_variant_data->item_code;
            $product_name[] = htmlspecialchars($lims_product_data->name);
            $product_type[] = $lims_product_data->type;
        }
        $lims_product_data = Product::select('code', 'name', 'type')->where('is_active', true)->whereNotIn('type', ['standard'])->get();
        foreach ($lims_product_data as $product) 
        {
            $product_qty[] = $product->qty;
            $product_code[] =  $product->code;
            $product_name[] = htmlspecialchars($product->name);
            $product_type[] = $product->type;
        }
        $product_data[] = $product_code;
        $product_data[] = $product_name;
        $product_data[] = $product_qty;
        $product_data[] = $product_type;
        $product_data[] = $product_price;
        return $product_data;
    }

    public function limsProductSearch(Request $request)
    {
        $todayDate = date('Y-m-d');
        $product_code = explode("(", $request['data']);
        $product_code[0] = rtrim($product_code[0], " ");
        $lims_product_data = Product::where('code', $product_code[0])->first();
        $product_variant_id = null;
        if(!$lims_product_data) {
            $lims_product_data = Product::join('product_variants', 'products.id', 'product_variants.product_id')
                ->select('products.*', 'product_variants.id as product_variant_id', 'product_variants.item_code', 'product_variants.additional_price')
                ->where('product_variants.item_code', $product_code[0])
                ->first();
            $lims_product_data->code = $lims_product_data->item_code;
            $lims_product_data->price += $lims_product_data->additional_price;
            $product_variant_id = $lims_product_data->product_variant_id;
        }
        $product[] = $lims_product_data->name;
        $product[] = $lims_product_data->code;
        if($lims_product_data->promotion && $todayDate <= $lims_product_data->last_date){
            $product[] = $lims_product_data->promotion_price;
        }
        else
            $product[] = $lims_product_data->price;
        
        if($lims_product_data->tax_id) {
            $lims_tax_data = Tax::find($lims_product_data->tax_id);
            $product[] = $lims_tax_data->rate;
            $product[] = $lims_tax_data->name;
        }
        else{
            $product[] = 0;
            $product[] = 'No Tax';
        }
        $product[] = $lims_product_data->tax_method;
        if($lims_product_data->type == 'standard'){
            $units = Unit::where("base_unit", $lims_product_data->unit_id)
                    ->orWhere('id', $lims_product_data->unit_id)
                    ->get();
            $unit_name = array();
            $unit_operator = array();
            $unit_operation_value = array();
            foreach ($units as $unit) {
                if($lims_product_data->sale_unit_id == $unit->id) {
                    array_unshift($unit_name, $unit->unit_name);
                    array_unshift($unit_operator, $unit->operator);
                    array_unshift($unit_operation_value, $unit->operation_value);
                }
                else {
                    $unit_name[]  = $unit->unit_name;
                    $unit_operator[] = $unit->operator;
                    $unit_operation_value[] = $unit->operation_value;
                }
            }
            $product[] = implode(",",$unit_name) . ',';
            $product[] = implode(",",$unit_operator) . ',';
            $product[] = implode(",",$unit_operation_value) . ',';     
        }
        
        else{
            $product[] = 'n/a'. ',';
            $product[] = 'n/a'. ',';
            $product[] = 'n/a'. ',';
        }
        $product[] = $lims_product_data->id;
        $product[] = $product_variant_id;
        $product[] = $lims_product_data->promotion;
        return $product;
    }

    public function store(Request $request)
    {
        $data = $request->except('document');
        //return dd($data);
        $data['reference_no'] = 'rr-' . date("Ymd") . '-'. date("his");
        $data['user_id'] = Auth::id();
        $cash_register_data = CashRegister::where([
            ['user_id', $data['user_id']],
            ['warehouse_id', $data['warehouse_id']],
            ['status', true]
        ])->first();
        if($cash_register_data)
            $data['cash_register_id'] = $cash_register_data->id;
        $lims_account_data = Account::where('is_default', true)->first();
        $data['account_id'] = $lims_account_data->id;
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
            $document->move('public/return/documents', $documentName);
            $data['document'] = $documentName;
        }

        $lims_return_data = Returns::create($data);
        $lims_customer_data = Customer::find($data['customer_id']);
        //collecting male data
        $mail_data['email'] = $lims_customer_data->email;
        $mail_data['reference_no'] = $lims_return_data->reference_no;
        $mail_data['total_qty'] = $lims_return_data->total_qty;
        $mail_data['total_price'] = $lims_return_data->total_price;
        $mail_data['order_tax'] = $lims_return_data->order_tax;
        $mail_data['order_tax_rate'] = $lims_return_data->order_tax_rate;
        $mail_data['grand_total'] = $lims_return_data->grand_total;

        $product_id = $data['product_id'];
        $product_code = $data['product_code'];
        $qty = $data['qty'];
        $sale_unit = $data['sale_unit'];
        $net_unit_price = $data['net_unit_price'];
        $discount = $data['discount'];
        $tax_rate = $data['tax_rate'];
        $tax = $data['tax'];
        $total = $data['subtotal'];

        foreach ($product_id as $key => $pro_id) {
            $lims_product_data = Product::find($pro_id);
            $variant_id = null;
            if($sale_unit[$key] != 'n/a'){
                $lims_sale_unit_data  = Unit::where('unit_name', $sale_unit[$key])->first();
                $sale_unit_id = $lims_sale_unit_data->id;
                if($lims_sale_unit_data->operator == '*')
                    $quantity = $qty[$key] * $lims_sale_unit_data->operation_value;
                elseif($lims_sale_unit_data->operator == '/')
                    $quantity = $qty[$key] / $lims_sale_unit_data->operation_value;

                if($lims_product_data->is_variant) {
                    $lims_product_variant_data = ProductVariant::
                        select('id', 'variant_id', 'qty')
                        ->FindExactProductWithCode($pro_id, $product_code[$key])
                        ->first();
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($pro_id, $lims_product_variant_data->variant_id, $data['warehouse_id'])->first();
                    $lims_product_variant_data->qty += $quantity;
                    $lims_product_variant_data->save();
                    $variant_data = Variant::find($lims_product_variant_data->variant_id);
                    $variant_id = $variant_data->id;
                }
                else
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($pro_id, $data['warehouse_id'])->first();

                $lims_product_data->qty +=  $quantity;
                $lims_product_warehouse_data->qty += $quantity;

                $lims_product_data->save();
                $lims_product_warehouse_data->save();
            }
            else {
                if($lims_product_data->type == 'combo'){
                    $product_list = explode(",", $lims_product_data->product_list);
                    $qty_list = explode(",", $lims_product_data->qty_list);
                    $price_list = explode(",", $lims_product_data->price_list);

                    foreach ($product_list as $index=>$child_id) {
                        $child_data = Product::find($child_id);
                        $child_warehouse_data = Product_Warehouse::where([
                            ['product_id', $child_id],
                            ['warehouse_id', $data['warehouse_id'] ],
                            ])->first();

                        $child_data->qty += $qty[$key] * $qty_list[$index];
                        $child_warehouse_data->qty += $qty[$key] * $qty_list[$index];

                        $child_data->save();
                        $child_warehouse_data->save();
                    }
                }
                $sale_unit_id = 0;
            }
            if($lims_product_data->is_variant)
                $mail_data['products'][$key] = $lims_product_data->name . ' [' . $variant_data->name . ']';
            else
                $mail_data['products'][$key] = $lims_product_data->name;
            
            if($sale_unit_id)
                $mail_data['unit'][$key] = $lims_sale_unit_data->unit_code;
            else
                $mail_data['unit'][$key] = '';

            $mail_data['qty'][$key] = $qty[$key];
            $mail_data['total'][$key] = $total[$key];
            ProductReturn::insert(
                ['return_id' => $lims_return_data->id, 'product_id' => $pro_id, 'variant_id' => $variant_id, 'qty' => $qty[$key], 'sale_unit_id' => $sale_unit_id, 'net_unit_price' => $net_unit_price[$key], 'discount' => $discount[$key], 'tax_rate' => $tax_rate[$key], 'tax' => $tax[$key], 'total' => $total[$key], 'created_at' => \Carbon\Carbon::now(),  'updated_at' => \Carbon\Carbon::now()]
            );
        }
        $message = 'Return created successfully';
        if($mail_data['email']){
            try{
                Mail::send( 'mail.return_details', $mail_data, function( $message ) use ($mail_data)
                {
                    $message->to( $mail_data['email'] )->subject( 'Return Details' );
                });
            }
            catch(\Exception $e){
                $message = 'Return created successfully. Please setup your <a href="setting/mail_setting">mail setting</a> to send mail.';
            }
        }
        return redirect('return-sale')->with('message', $message);
    }

    public function sendMail(Request $request)
    {
        $data = $request->all();
        $lims_return_data = Returns::find($data['return_id']);
        $lims_product_return_data = ProductReturn::where('return_id', $data['return_id'])->get();
        $lims_customer_data = Customer::find($lims_return_data->customer_id);
        if($lims_customer_data->email) {
            //collecting male data
            $mail_data['email'] = $lims_customer_data->email;
            $mail_data['reference_no'] = $lims_return_data->reference_no;
            $mail_data['total_qty'] = $lims_return_data->total_qty;
            $mail_data['total_price'] = $lims_return_data->total_price;
            $mail_data['order_tax'] = $lims_return_data->order_tax;
            $mail_data['order_tax_rate'] = $lims_return_data->order_tax_rate;
            $mail_data['grand_total'] = $lims_return_data->grand_total;

            foreach ($lims_product_return_data as $key => $product_return_data) {
                $lims_product_data = Product::find($product_return_data->product_id);
                if($product_return_data->variant_id){
                    $variant_data = Variant::find($product_return_data->variant_id);
                    $mail_data['products'][$key] = $lims_product_data->name . ' [' . $variant_data->name .']';
                }
                else
                    $mail_data['products'][$key] = $lims_product_data->name;

                if($product_return_data->sale_unit_id){
                    $lims_unit_data = Unit::find($product_return_data->sale_unit_id);
                    $mail_data['unit'][$key] = $lims_unit_data->unit_code;
                }
                else
                    $mail_data['unit'][$key] = '';

                $mail_data['qty'][$key] = $product_return_data->qty;
                $mail_data['total'][$key] = $product_return_data->qty;
            }

            try{
                Mail::send( 'mail.return_details', $mail_data, function( $message ) use ($mail_data)
                {
                    $message->to( $mail_data['email'] )->subject( 'Return Details' );
                });
                $message = 'Mail sent successfully';
            }
            catch(\Exception $e){
                $message = 'Please setup your <a href="setting/mail_setting">mail setting</a> to send mail.';
            }
        }
        else
            $message = 'Customer doesnt have email!';
        
        return redirect()->back()->with('message', $message);
    }

    public function productReturnData($id)
    {
        $lims_product_return_data = ProductReturn::where('return_id', $id)->get();
        foreach ($lims_product_return_data as $key => $product_return_data) {
            $product = Product::find($product_return_data->product_id);
            if($product_return_data->sale_unit_id != 0){
                $unit_data = Unit::find($product_return_data->sale_unit_id);
                $unit = $unit_data->unit_code;
            }
            else
                $unit = '';
            if($product_return_data->variant_id) {
                $lims_product_variant_data = ProductVariant::select('item_code')->FindExactProduct($product_return_data->product_id, $product_return_data->variant_id)->first();
                $product->code = $lims_product_variant_data->item_code;
            }

            $product_return[0][$key] = $product->name . ' [' . $product->code . ']';
            $product_return[1][$key] = $product_return_data->qty;
            $product_return[2][$key] = $unit;
            $product_return[3][$key] = $product_return_data->tax;
            $product_return[4][$key] = $product_return_data->tax_rate;
            $product_return[5][$key] = $product_return_data->discount;
            $product_return[6][$key] = $product_return_data->total;
        }
        return $product_return;
    }

    public function edit($id)
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('returns-edit')){
            $lims_customer_list = Customer::where('is_active',true)->get();
            $lims_warehouse_list = Warehouse::where('is_active',true)->get();
            $lims_biller_list = Biller::where('is_active',true)->get();
            $lims_tax_list = Tax::where('is_active',true)->get();
            $lims_return_data = Returns::find($id);
            $lims_product_return_data = ProductReturn::where('return_id', $id)->get();
            return view('return.edit',compact('lims_customer_list', 'lims_warehouse_list', 'lims_biller_list', 'lims_tax_list', 'lims_return_data','lims_product_return_data'));
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
            $document->move('public/return/documents', $documentName);
            $data['document'] = $documentName;
        }

        $lims_return_data = Returns::find($id);
        $lims_product_return_data = ProductReturn::where('return_id', $id)->get();

        $product_id = $data['product_id'];
        $product_code = $data['product_code'];
        $product_variant_id = $data['product_variant_id'];
        $qty = $data['qty'];
        $sale_unit = $data['sale_unit'];
        $net_unit_price = $data['net_unit_price'];
        $discount = $data['discount'];
        $tax_rate = $data['tax_rate'];
        $tax = $data['tax'];
        $total = $data['subtotal'];

        foreach ($lims_product_return_data as $key => $product_return_data) {
            $old_product_id[] = $product_return_data->product_id;
            $old_product_variant_id[] = null;
            $lims_product_data = Product::find($product_return_data->product_id);
            if($lims_product_data->type == 'combo'){
                $product_list = explode(",", $lims_product_data->product_list);
                $qty_list = explode(",", $lims_product_data->qty_list);

                foreach ($product_list as $index=>$child_id) {
                    $child_data = Product::find($child_id);
                    $child_warehouse_data = Product_Warehouse::where([
                        ['product_id', $child_id],
                        ['warehouse_id', $lims_return_data->warehouse_id ],
                        ])->first();

                    $child_data->qty -= $product_return_data->qty * $qty_list[$index];
                    $child_warehouse_data->qty -= $product_return_data->qty * $qty_list[$index];

                    $child_data->save();
                    $child_warehouse_data->save();
                }
            }
            elseif($product_return_data->sale_unit_id != 0){
                $lims_sale_unit_data = Unit::find($product_return_data->sale_unit_id);
                if ($lims_sale_unit_data->operator == '*')
                    $quantity = $product_return_data->qty * $lims_sale_unit_data->operation_value;
                elseif($lims_sale_unit_data->operator == '/')
                    $quantity = $product_return_data->qty / $lims_sale_unit_data->operation_value;

                if($product_return_data->variant_id) {
                    $lims_product_variant_data = ProductVariant::select('id', 'qty')->FindExactProduct($product_return_data->product_id, $product_return_data->variant_id)->first();
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($product_return_data->product_id, $product_return_data->variant_id, $lims_return_data->warehouse_id)
                    ->first();
                    $old_product_variant_id[$key] = $lims_product_variant_data->id;
                    $lims_product_variant_data->qty -= $quantity;
                    $lims_product_variant_data->save();
                }
                else
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($product_return_data->product_id, $lims_return_data->warehouse_id)
                    ->first();

                $lims_product_data->qty -= $quantity;
                $lims_product_warehouse_data->qty -= $quantity;
                $lims_product_data->save();
                $lims_product_warehouse_data->save();
            }
            if($product_return_data->variant_id && !(in_array($old_product_variant_id[$key], $product_variant_id)) ){
                $product_return_data->delete();
            }
            elseif( !(in_array($old_product_id[$key], $product_id)) )
                $product_return_data->delete();
        }
        foreach ($product_id as $key => $pro_id) {
            $lims_product_data = Product::find($pro_id);
            $product_return['variant_id'] = null;
            if($sale_unit[$key] != 'n/a'){
                $lims_sale_unit_data = Unit::where('unit_name', $sale_unit[$key])->first();
                $sale_unit_id = $lims_sale_unit_data->id;
                if ($lims_sale_unit_data->operator == '*')
                    $quantity = $qty[$key] * $lims_sale_unit_data->operation_value;
                elseif($lims_sale_unit_data->operator == '/')
                    $quantity = $qty[$key] / $lims_sale_unit_data->operation_value;

                if($lims_product_data->is_variant) {
                    $lims_product_variant_data = ProductVariant::select('id', 'variant_id', 'qty')->FindExactProductWithCode($pro_id, $product_code[$key])->first();
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($pro_id, $lims_product_variant_data->variant_id, $data['warehouse_id'])
                    ->first();
                    $variant_data = Variant::find($lims_product_variant_data->variant_id);

                    $product_return['variant_id'] = $lims_product_variant_data->variant_id;
                    $lims_product_variant_data->qty += $quantity;
                    $lims_product_variant_data->save();
                }
                else {
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($pro_id, $data['warehouse_id'])
                    ->first();
                }

                $lims_product_data->qty +=  $quantity;
                $lims_product_warehouse_data->qty += $quantity;

                $lims_product_data->save();
                $lims_product_warehouse_data->save();
            }
            else {
                if($lims_product_data->type == 'combo'){
                    $product_list = explode(",", $lims_product_data->product_list);
                    $qty_list = explode(",", $lims_product_data->qty_list);

                    foreach ($product_list as $index=>$child_id) {
                        $child_data = Product::find($child_id);
                        $child_warehouse_data = Product_Warehouse::where([
                            ['product_id', $child_id],
                            ['warehouse_id', $data['warehouse_id'] ],
                            ])->first();

                        $child_data->qty += $qty[$key] * $qty_list[$index];
                        $child_warehouse_data->qty += $qty[$key] * $qty_list[$index];

                        $child_data->save();
                        $child_warehouse_data->save();
                    }
                }
                $sale_unit_id = 0;
            }

            if($lims_product_data->is_variant)
                $mail_data['products'][$key] = $lims_product_data->name . ' [' . $variant_data->name .']';
            else
                $mail_data['products'][$key] = $lims_product_data->name;

            if($sale_unit_id)
                $mail_data['unit'][$key] = $lims_sale_unit_data->unit_code;
            else
                $mail_data['unit'][$key] = '';

            $mail_data['qty'][$key] = $qty[$key];
            $mail_data['total'][$key] = $total[$key];

            $product_return['return_id'] = $id ;
            $product_return['product_id'] = $pro_id;
            $product_return['qty'] = $qty[$key];
            $product_return['sale_unit_id'] = $sale_unit_id;
            $product_return['net_unit_price'] = $net_unit_price[$key];
            $product_return['discount'] = $discount[$key];
            $product_return['tax_rate'] = $tax_rate[$key];
            $product_return['tax'] = $tax[$key];
            $product_return['total'] = $total[$key];

            if($product_return['variant_id'] && in_array($product_variant_id[$key], $old_product_variant_id)) {
                ProductReturn::where([
                    ['product_id', $pro_id],
                    ['variant_id', $product_return['variant_id']],
                    ['return_id', $id]
                ])->update($product_return);
            }
            elseif( $product_return['variant_id'] === null && (in_array($pro_id, $old_product_id)) ) {
                ProductReturn::where([
                    ['return_id', $id],
                    ['product_id', $pro_id]
                    ])->update($product_return);
            }
            else
                ProductReturn::create($product_return);
        }
        $lims_return_data->update($data);
        $lims_customer_data = Customer::find($data['customer_id']);
        //collecting male data
        $mail_data['email'] = $lims_customer_data->email;
        $mail_data['reference_no'] = $lims_return_data->reference_no;
        $mail_data['total_qty'] = $lims_return_data->total_qty;
        $mail_data['total_price'] = $lims_return_data->total_price;
        $mail_data['order_tax'] = $lims_return_data->order_tax;
        $mail_data['order_tax_rate'] = $lims_return_data->order_tax_rate;
        $mail_data['grand_total'] = $lims_return_data->grand_total;
        $message = 'Return updated successfully';
        if($mail_data['email']){
            try{
                Mail::send( 'mail.return_details', $mail_data, function( $message ) use ($mail_data)
                {
                    $message->to( $mail_data['email'] )->subject( 'Return Details' );
                });
            }
            catch(\Exception $e){
                $message = 'Return updated successfully. Please setup your <a href="setting/mail_setting">mail setting</a> to send mail.';
            }
        }
        return redirect('return-sale')->with('message', $message);
    }

    public function deleteBySelection(Request $request)
    {
        $return_id = $request['returnIdArray'];
        foreach ($return_id as $id) {
            $lims_return_data = Returns::find($id);
            $lims_product_return_data = ProductReturn::where('return_id', $id)->get();

            foreach ($lims_product_return_data as $key => $product_return_data) {
                $lims_product_data = Product::find($product_return_data->product_id);
                if( $lims_product_data->type == 'combo' ){
                    $product_list = explode(",", $lims_product_data->product_list);
                    $qty_list = explode(",", $lims_product_data->qty_list);

                    foreach ($product_list as $index=>$child_id) {
                        $child_data = Product::find($child_id);
                        $child_warehouse_data = Product_Warehouse::where([
                            ['product_id', $child_id],
                            ['warehouse_id', $lims_return_data->warehouse_id ],
                            ])->first();

                        $child_data->qty -= $product_return_data->qty * $qty_list[$index];
                        $child_warehouse_data->qty -= $product_return_data->qty * $qty_list[$index];

                        $child_data->save();
                        $child_warehouse_data->save();
                    }
                }
                elseif($product_return_data->sale_unit_id != 0){
                    $lims_sale_unit_data = Unit::find($product_return_data->sale_unit_id);

                    if ($lims_sale_unit_data->operator == '*')
                        $quantity = $product_return_data->qty * $lims_sale_unit_data->operation_value;
                    elseif($lims_sale_unit_data->operator == '/')
                        $quantity = $product_return_data->qty / $lims_sale_unit_data->operation_value;
                    if($product_return_data->variant_id) {
                        $lims_product_variant_data = ProductVariant::select('id', 'qty')->FindExactProduct($product_return_data->product_id, $product_return_data->variant_id)->first();
                        $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($product_return_data->product_id, $product_return_data->variant_id, $lims_return_data->warehouse_id)->first();
                        $lims_product_variant_data->qty -= $quantity;
                        $lims_product_variant_data->save();
                    }
                    else
                        $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($product_return_data->product_id, $lims_return_data->warehouse_id)->first();

                    $lims_product_data->qty -= $quantity;
                    $lims_product_warehouse_data->qty -= $quantity;
                    $lims_product_data->save();
                    $lims_product_warehouse_data->save();
                    $product_return_data->delete();
                }
            }
            $lims_return_data->delete();
            }
        return 'Return deleted successfully!';
    }

    public function destroy($id)
    {
        $lims_return_data = Returns::find($id);
        $lims_product_return_data = ProductReturn::where('return_id', $id)->get();

        foreach ($lims_product_return_data as $key => $product_return_data) {
            $lims_product_data = Product::find($product_return_data->product_id);
            if( $lims_product_data->type == 'combo' ){
                $product_list = explode(",", $lims_product_data->product_list);
                $qty_list = explode(",", $lims_product_data->qty_list);

                foreach ($product_list as $index=>$child_id) {
                    $child_data = Product::find($child_id);
                    $child_warehouse_data = Product_Warehouse::where([
                        ['product_id', $child_id],
                        ['warehouse_id', $lims_return_data->warehouse_id ],
                        ])->first();

                    $child_data->qty -= $product_return_data->qty * $qty_list[$index];
                    $child_warehouse_data->qty -= $product_return_data->qty * $qty_list[$index];

                    $child_data->save();
                    $child_warehouse_data->save();
                }
            }
            elseif($product_return_data->sale_unit_id != 0){
                $lims_sale_unit_data = Unit::find($product_return_data->sale_unit_id);

                if ($lims_sale_unit_data->operator == '*')
                    $quantity = $product_return_data->qty * $lims_sale_unit_data->operation_value;
                elseif($lims_sale_unit_data->operator == '/')
                    $quantity = $product_return_data->qty / $lims_sale_unit_data->operation_value;
                
                if($product_return_data->variant_id) {
                    $lims_product_variant_data = ProductVariant::select('id', 'qty')->FindExactProduct($product_return_data->product_id, $product_return_data->variant_id)->first();
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($product_return_data->product_id, $product_return_data->variant_id, $lims_return_data->warehouse_id)->first();
                    $lims_product_variant_data->qty -= $quantity;
                    $lims_product_variant_data->save();
                }
                else
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($product_return_data->product_id, $lims_return_data->warehouse_id)->first();

                $lims_product_data->qty -= $quantity;
                $lims_product_warehouse_data->qty -= $quantity;
                $lims_product_data->save();
                $lims_product_warehouse_data->save();
                $product_return_data->delete();
            }
        }
        $lims_return_data->delete();
        return redirect('return-sale')->with('not_permitted', 'Data deleted successfully');;
    }
}
