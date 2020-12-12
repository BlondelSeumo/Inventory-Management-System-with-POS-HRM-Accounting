<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Keygen;
use App\Brand;
use App\Category;
use App\Unit;
use App\Tax;
use App\Warehouse;
use App\Supplier;
use App\Product;
use App\Product_Warehouse;
use App\Product_Supplier;
use Auth;
use DNS1D;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;
use DB;
use App\Variant;
use App\ProductVariant;

class ProductController extends Controller
{
    public function index()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('products-index')){            
            $permissions = Role::findByName($role->name)->permissions;
            foreach ($permissions as $permission)
                $all_permission[] = $permission->name;
            if(empty($all_permission))
                $all_permission[] = 'dummy text';
            return view('product.index', compact('all_permission'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function productData(Request $request)
    {
        $columns = array( 
            2 => 'name', 
            3 => 'code',
            4 => 'brand_id',
            5 => 'category_id',
            6 => 'qty',
            7 => 'unit_id',
            8 => 'price' 
        );
        
        $totalData = Product::where('is_active', true)->count();
        $totalFiltered = $totalData; 

        if($request->input('length') != -1)
            $limit = $request->input('length');
        else
            $limit = $totalData;
        $start = $request->input('start');
        $order = 'products.'.$columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        if(empty($request->input('search.value'))){
            $products = Product::with('category', 'brand', 'unit')->offset($start)
                        ->where('is_active', true)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
        }
        else
        {
            $search = $request->input('search.value'); 
            $products =  Product::select('products.*')
                        ->with('category', 'brand', 'unit')
                        ->join('categories', 'products.category_id', '=', 'categories.id')
                        ->leftjoin('brands', 'products.brand_id', '=', 'brands.id')
                        ->where([
                            ['products.name', 'LIKE', "%{$search}%"],
                            ['products.is_active', true]
                        ])
                        ->orWhere([
                            ['products.code', 'LIKE', "%{$search}%"],
                            ['products.is_active', true]
                        ])
                        ->orWhere([
                            ['categories.name', 'LIKE', "%{$search}%"],
                            ['categories.is_active', true],
                            ['products.is_active', true]
                        ])
                        ->orWhere([
                            ['brands.title', 'LIKE', "%{$search}%"],
                            ['brands.is_active', true],
                            ['products.is_active', true]
                        ])
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)->get();

            $totalFiltered = Product::
                            join('categories', 'products.category_id', '=', 'categories.id')
                            ->leftjoin('brands', 'products.brand_id', '=', 'brands.id')
                            ->where([
                                ['products.name','LIKE',"%{$search}%"],
                                ['products.is_active', true]
                            ])
                            ->orWhere([
                                ['products.code', 'LIKE', "%{$search}%"],
                                ['products.is_active', true]
                            ])
                            ->orWhere([
                                ['categories.name', 'LIKE', "%{$search}%"],
                                ['categories.is_active', true],
                                ['products.is_active', true]
                            ])
                            ->orWhere([
                                ['brands.title', 'LIKE', "%{$search}%"],
                                ['brands.is_active', true],
                                ['products.is_active', true]
                            ])
                            ->count();
        }
        $data = array();
        if(!empty($products))
        {
            foreach ($products as $key=>$product)
            {
                $nestedData['id'] = $product->id;
                $nestedData['key'] = $key;
                $product_image = explode(",", $product->image);
                $product_image = htmlspecialchars($product_image[0]);
                $nestedData['image'] = '<img src="'.url('public/images/product', $product_image).'" height="80" width="80">';
                $nestedData['name'] = $product->name;
                $nestedData['code'] = $product->code;
                if($product->brand_id)
                    $nestedData['brand'] = $product->brand->title;
                else
                    $nestedData['brand'] = "N/A";
                $nestedData['category'] = $product->category->name;
                $nestedData['qty'] = $product->qty;
                if($product->unit_id)
                    $nestedData['unit'] = $product->unit->unit_name;
                else
                    $nestedData['unit'] = 'N/A';
                
                $nestedData['price'] = $product->price;
                $nestedData['options'] = '<div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.trans("file.action").'
                              <span class="caret"></span>
                              <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                            <li>
                                <button="type" class="btn btn-link view"><i class="fa fa-eye"></i> '.trans('file.View').'</button>
                            </li>';
                if(in_array("products-edit", $request['all_permission']))
                    $nestedData['options'] .= '<li>
                            <a href="'.route('products.edit', $product->id).'" class="btn btn-link"><i class="fa fa-edit"></i> '.trans('file.edit').'</a>
                        </li>';
                if(in_array("products-delete", $request['all_permission']))
                    $nestedData['options'] .= \Form::open(["route" => ["products.destroy", $product->id], "method" => "DELETE"] ).'
                            <li>
                              <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="fa fa-trash"></i> '.trans("file.delete").'</button> 
                            </li>'.\Form::close().'
                        </ul>
                    </div>';
                // data for product details by one click
                if($product->tax_id)
                    $tax = Tax::find($product->tax_id)->name;
                else
                    $tax = "N/A";

                if($product->tax_method == 1)
                    $tax_method = trans('file.Exclusive');
                else
                    $tax_method = trans('file.Inclusive');

                $nestedData['product'] = array( '[ "'.$product->type.'"', ' "'.$product->name.'"', ' "'.$product->code.'"', ' "'.$nestedData['brand'].'"', ' "'.$nestedData['category'].'"', ' "'.$nestedData['unit'].'"', ' "'.$product->cost.'"', ' "'.$product->price.'"', ' "'.$tax.'"', ' "'.$tax_method.'"', ' "'.$product->alert_quantity.'"', ' "'.preg_replace('/\s+/S', " ", $product->product_details).'"', ' "'.$product->id.'"', ' "'.$product->product_list.'"', ' "'.$product->qty_list.'"', ' "'.$product->price_list.'"', ' "'.$product->qty.'"', ' "'.$product->image.'"]'
                );
                //$nestedData['imagedata'] = DNS1D::getBarcodePNG($product->code, $product->barcode_symbology);
                $data[] = $nestedData;
            }
        }
        $json_data = array(
            "draw"            => intval($request->input('draw')),  
            "recordsTotal"    => intval($totalData),  
            "recordsFiltered" => intval($totalFiltered), 
            "data"            => $data   
        );
            
        echo json_encode($json_data);
    }
    
    public function create()
    {
        $role = Role::firstOrCreate(['id' => Auth::user()->role_id]);
        if ($role->hasPermissionTo('products-add')){
            $lims_product_list = Product::where([ ['is_active', true], ['type', 'standard'] ])->get();
            $lims_brand_list = Brand::where('is_active', true)->get();
            $lims_category_list = Category::where('is_active', true)->get();
            $lims_unit_list = Unit::where('is_active', true)->get();
            $lims_tax_list = Tax::where('is_active', true)->get();
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
            return view('product.create',compact('lims_product_list', 'lims_brand_list', 'lims_category_list', 'lims_unit_list', 'lims_tax_list', 'lims_warehouse_list'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'code' => [
                'max:255',
                    Rule::unique('products')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
            'name' => [
                'max:255',
                    Rule::unique('products')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ]
        ]);
        $data = $request->except('image', 'file');
        $data['name'] = htmlspecialchars($data['name']);
        if($data['type'] == 'combo'){
            $data['product_list'] = implode(",", $data['product_id']);
            $data['qty_list'] = implode(",", $data['product_qty']);
            $data['price_list'] = implode(",", $data['unit_price']);
            $data['cost'] = $data['unit_id'] = $data['purchase_unit_id'] = $data['sale_unit_id'] = 0;
        }
        elseif($data['type'] == 'digital')
            $data['cost'] = $data['unit_id'] = $data['purchase_unit_id'] = $data['sale_unit_id'] = 0;

        $data['product_details'] = str_replace('"', '@', $data['product_details']);

        if($data['starting_date'])
            $data['starting_date'] = date('Y-m-d', strtotime($data['starting_date']));
        if($data['last_date'])
            $data['last_date'] = date('Y-m-d', strtotime($data['last_date']));
        $data['is_active'] = true;
        $images = $request->image;
        $image_names = [];
        if($images) {            
            foreach ($images as $key => $image) {
                $imageName = $image->getClientOriginalName();
                $image->move('public/images/product', $imageName);
                $image_names[] = $imageName;
            }
            $data['image'] = implode(",", $image_names);
        }
        else {
            $data['image'] = 'zummXD2dvAtI.png';
        }
        $file = $request->file;
        if ($file) {
            $ext = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
            $fileName = strtotime(date('Y-m-d H:i:s'));
            $fileName = $fileName . '.' . $ext;
            $file->move('public/product/files', $fileName);
            $data['file'] = $fileName;
        }
        $lims_product_data = Product::create($data);
        //dealing with product variant
        if(isset($data['is_variant'])) {
            foreach ($data['variant_name'] as $key => $variant_name) {
                $lims_variant_data = Variant::firstOrCreate(['name' => $data['variant_name'][$key]]);
                $lims_variant_data->name = $data['variant_name'][$key];
                $lims_variant_data->save();
                $lims_product_variant_data = new ProductVariant;             
                $lims_product_variant_data->product_id = $lims_product_data->id;
                $lims_product_variant_data->variant_id = $lims_variant_data->id;
                $lims_product_variant_data->position = $key + 1;
                $lims_product_variant_data->item_code = $data['item_code'][$key];
                $lims_product_variant_data->additional_price = $data['additional_price'][$key];
                $lims_product_variant_data->qty = 0;
                $lims_product_variant_data->save();
            }
        }
        if(isset($data['is_diffPrice'])) {
            foreach ($data['diff_price'] as $key => $diff_price) {
                if($diff_price) {
                    Product_Warehouse::create([
                        "product_id" => $lims_product_data->id,
                        "warehouse_id" => $data["warehouse_id"][$key],
                        "qty" => 0,
                        "price" => $diff_price
                    ]);
                }
            }
        }
        \Session::flash('create_message', 'Product created successfully');
    }

    public function edit($id)
    {
        $role = Role::firstOrCreate(['id' => Auth::user()->role_id]);
        if ($role->hasPermissionTo('products-edit')) {
            $lims_product_list = Product::where([ ['is_active', true], ['type', 'standard'] ])->get();
            $lims_brand_list = Brand::where('is_active', true)->get();
            $lims_category_list = Category::where('is_active', true)->get();
            $lims_unit_list = Unit::where('is_active', true)->get();
            $lims_tax_list = Tax::where('is_active', true)->get();
            $lims_product_data = Product::where('id', $id)->first();
            $lims_product_variant_data = $lims_product_data->variant()->orderBy('position')->get();
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();

            return view('product.edit',compact('lims_product_list', 'lims_brand_list', 'lims_category_list', 'lims_unit_list', 'lims_tax_list', 'lims_product_data', 'lims_product_variant_data', 'lims_warehouse_list'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function updateProduct(Request $request)
    {
        if(!env('USER_VERIFIED')) {
            \Session::flash('not_permitted', 'This feature is disable for demo!');
        }
        else {
            $this->validate($request, [
                'name' => [
                    'max:255',
                    Rule::unique('products')->ignore($request->input('id'))->where(function ($query) {
                        return $query->where('is_active', 1);
                    }),
                ],

                'code' => [
                    'max:255',
                    Rule::unique('products')->ignore($request->input('id'))->where(function ($query) {
                        return $query->where('is_active', 1);
                    }),
                ]
            ]);
            
            $lims_product_data = Product::findOrFail($request->input('id'));
            $data = $request->except('image', 'file', 'prev_img');
            $data['name'] = htmlspecialchars($data['name']);

            if($data['type'] == 'combo') {
                $data['product_list'] = implode(",", $data['product_id']);
                $data['qty_list'] = implode(",", $data['product_qty']);
                $data['price_list'] = implode(",", $data['unit_price']);
                $data['cost'] = $data['unit_id'] = $data['purchase_unit_id'] = $data['sale_unit_id'] = 0;
            }
            elseif($data['type'] == 'digital')
                $data['cost'] = $data['unit_id'] = $data['purchase_unit_id'] = $data['sale_unit_id'] = 0;

            if(!isset($data['featured']))
                $data['featured'] = 0;

            if(!isset($data['promotion']))
                $data['promotion'] = null;

            $data['product_details'] = str_replace('"', '@', $data['product_details']);
            $data['product_details'] = $data['product_details'];
            if($data['starting_date'])
                $data['starting_date'] = date('Y-m-d', strtotime($data['starting_date']));
            if($data['last_date'])
                $data['last_date'] = date('Y-m-d', strtotime($data['last_date']));

            //dealing with previous images
            if($request->prev_img) {
                $lims_product_data->image = implode(",", $request->prev_img);
                $lims_product_data->save();
            }
            //dealing with new images
            $images = $request->image;
            $image_names = [];
            if($images) {            
                foreach ($images as $key => $image) {
                    $imageName = $image->getClientOriginalName();
                    $image->move('public/images/product', $imageName);
                    $image_names[] = $imageName;
                }
                if($lims_product_data->image != 'zummXD2dvAtI.png') {
                    $data['image'] = $lims_product_data->image.','.implode(",", $image_names);
                }
                else{
                    $data['image'] = implode(",", $image_names);
                }
            }
            else {
                $data['image'] = $lims_product_data->image;
            }

            $file = $request->file;
            if ($file) {
                $ext = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                $fileName = strtotime(date('Y-m-d H:i:s'));
                $fileName = $fileName . '.' . $ext;
                $file->move('public/product/files', $fileName);
                $data['file'] = $fileName;
            }

            $lims_product_variant_data = ProductVariant::where('product_id', $request->input('id'))->select('id', 'variant_id')->get();
            foreach ($lims_product_variant_data as $key => $value) {
                if (!in_array($value->variant_id, $data['variant_id'])) {
                    ProductVariant::find($value->id)->delete();
                }
            }
            //dealing with product variant
            if(isset($data['is_variant'])) {
                foreach ($data['variant_name'] as $key => $variant_name) {
                    if($data['product_variant_id'][$key] == 0) {
                        $lims_variant_data = Variant::firstOrCreate(['name' => $data['variant_name'][$key]]);
                        $lims_product_variant_data = new ProductVariant();

                        $lims_product_variant_data->product_id = $lims_product_data->id;
                        $lims_product_variant_data->variant_id = $lims_variant_data->id;

                        $lims_product_variant_data->position = $key + 1;
                        $lims_product_variant_data->item_code = $data['item_code'][$key];
                        $lims_product_variant_data->additional_price = $data['additional_price'][$key];
                        $lims_product_variant_data->qty = 0;
                        $lims_product_variant_data->save();
                    }
                    else {
                        Variant::find($data['variant_id'][$key])->update(['name' => $variant_name]);
                        ProductVariant::find($data['product_variant_id'][$key])->update([
                            'position' => $key+1,
                            'item_code' => $data['item_code'][$key],
                            'additional_price' => $data['additional_price'][$key]
                        ]);
                    }
                }
            }
            if(isset($data['is_diffPrice'])) {
                foreach ($data['diff_price'] as $key => $diff_price) {
                    if($diff_price) {
                        $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($lims_product_data->id, $data['warehouse_id'][$key])->first();
                        if($lims_product_warehouse_data) {
                            $lims_product_warehouse_data->price = $diff_price;
                            $lims_product_warehouse_data->save();
                        }
                        else {
                            Product_Warehouse::create([
                                "product_id" => $lims_product_data->id,
                                "warehouse_id" => $data["warehouse_id"][$key],
                                "qty" => 0,
                                "price" => $diff_price
                            ]);
                        }
                    }
                }
            }
            else {
                $data['is_diffPrice'] = false;
                foreach ($data['warehouse_id'] as $key => $warehouse_id) {
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($lims_product_data->id, $warehouse_id)->first();
                    if($lims_product_warehouse_data) {
                        $lims_product_warehouse_data->price = null;
                        $lims_product_warehouse_data->save();
                    }
                }
            }
            $lims_product_data->update($data);
            \Session::flash('edit_message', 'Product updated successfully');
        }
    }

    public function generateCode()
    {
        $id = Keygen::numeric(8)->generate();
        return $id;
    }

    public function search(Request $request)
    {
        $product_code = explode(" ", $request['data']);
        $lims_product_data = Product::where('code', $product_code[0])->first();

        $product[] = $lims_product_data->name;
        $product[] = $lims_product_data->code;
        $product[] = $lims_product_data->qty;
        $product[] = $lims_product_data->price;
        $product[] = $lims_product_data->id;
        return $product;
    }

    public function saleUnit($id)
    {
        $unit = Unit::where("base_unit", $id)->orWhere('id', $id)->pluck('unit_name','id');
        return json_encode($unit);
    }

    public function getData($id)
    {
        $data = Product::select('name', 'code')->where('id', $id)->get();
        return $data[0];
    }

    public function productWarehouseData($id)
    {
        $warehouse = [];
        $qty = [];
        $warehouse_name = [];
        $variant_name = [];
        $variant_qty = [];
        $product_warehouse = [];
        $product_variant_warehouse = [];
        $lims_product_data = Product::select('id', 'is_variant')->find($id);
        if($lims_product_data->is_variant) {
            $lims_product_variant_warehouse_data = Product_Warehouse::where('product_id', $lims_product_data->id)->orderBy('warehouse_id')->get();
            $lims_product_warehouse_data = Product_Warehouse::select('warehouse_id', DB::raw('sum(qty) as qty'))->where('product_id', $id)->groupBy('warehouse_id')->get();
            foreach ($lims_product_variant_warehouse_data as $key => $product_variant_warehouse_data) {
                $lims_warehouse_data = Warehouse::find($product_variant_warehouse_data->warehouse_id);
                $lims_variant_data = Variant::find($product_variant_warehouse_data->variant_id);
                $warehouse_name[] = $lims_warehouse_data->name;
                $variant_name[] = $lims_variant_data->name;
                $variant_qty[] = $product_variant_warehouse_data->qty;
            }
        }
        else{
            $lims_product_warehouse_data = Product_Warehouse::where('product_id', $id)->get();
        }
        foreach ($lims_product_warehouse_data as $key => $product_warehouse_data) {
            $lims_warehouse_data = Warehouse::find($product_warehouse_data->warehouse_id);
            $warehouse[] = $lims_warehouse_data->name;
            $qty[] = $product_warehouse_data->qty;
        }

        $product_warehouse = [$warehouse, $qty];
        $product_variant_warehouse = [$warehouse_name, $variant_name, $variant_qty];
        return ['product_warehouse' => $product_warehouse, 'product_variant_warehouse' => $product_variant_warehouse];
    }

    public function printBarcode()
    {
        $lims_product_list = Product::where('is_active', true)->get();
        return view('product.print_barcode', compact('lims_product_list'));
    }

    public function limsProductSearch(Request $request)
    {
        $todayDate = date('Y-m-d');
        $product_code = explode(" ", $request['data']);

        $lims_product_data = Product::where('code', $product_code[0])->first();
        $product[] = $lims_product_data->name;
        $product[] = $lims_product_data->code;
        $product[] = $lims_product_data->price;
        $product[] = DNS1D::getBarcodePNG($lims_product_data->code, $lims_product_data->barcode_symbology);
        $product[] = $lims_product_data->promotion_price;
        $product[] = config('currency');
        $product[] = config('currency_position');
        return $product;
    }

    /*public function getBarcode()
    {
        return DNS1D::getBarcodePNG('72782608', 'C128');
    }*/

    public function importProduct(Request $request)
    {   
        //get file
        $upload=$request->file('file');
        $ext = pathinfo($upload->getClientOriginalName(), PATHINFO_EXTENSION);
        if($ext != 'csv')
            return redirect()->back()->with('message', 'Please upload a CSV file');

        $filePath=$upload->getRealPath();
        //open and read
        $file=fopen($filePath, 'r');
        $header= fgetcsv($file);
        $escapedHeader=[];
        //validate
        foreach ($header as $key => $value) {
            $lheader=strtolower($value);
            $escapedItem=preg_replace('/[^a-z]/', '', $lheader);
            array_push($escapedHeader, $escapedItem);
        }
        //looping through other columns
        while($columns=fgetcsv($file))
        {
            foreach ($columns as $key => $value) {
                $value=preg_replace('/\D/','',$value);
            }
           $data= array_combine($escapedHeader, $columns);
           
           if($data['brand'] != 'N/A' && $data['brand'] != ''){
                $lims_brand_data = Brand::firstOrCreate(['title' => $data['brand'], 'is_active' => true]);
                $brand_id = $lims_brand_data->id;
           }
            else
                $brand_id = null;

           $lims_category_data = Category::firstOrCreate(['name' => $data['category'], 'is_active' => true]);

           $lims_unit_data = Unit::where('unit_code', $data['unitcode'])->first();
           if(!$lims_unit_data)
                return redirect()->back()->with('not_permitted', 'Unit code does not exist in the database.');

           $product = Product::firstOrNew([ 'name'=>$data['name'], 'is_active'=>true ]);
            if($data['image'])
                $product->image = $data['image'];
            else
                $product->image = 'zummXD2dvAtI.png';

           $product->name = $data['name'];
           $product->code = $data['code'];
           $product->type = strtolower($data['type']);
           $product->barcode_symbology = 'C128';
           $product->brand_id = $brand_id;
           $product->category_id = $lims_category_data->id;
           $product->unit_id = $lims_unit_data->id;
           $product->purchase_unit_id = $lims_unit_data->id;
           $product->sale_unit_id = $lims_unit_data->id;
           $product->cost = $data['cost'];
           $product->price = $data['price'];
           $product->tax_method = 1;
           $product->qty = 0;
           $product->product_details = $data['productdetails'];
           $product->is_active = true;
           $product->save();

            if($data['variantname']) {
                //dealing with variants
                $variant_names = explode(",", $data['variantname']);
                $item_codes = explode(",", $data['itemcode']);
                $additional_prices = explode(",", $data['additionalprice']);
                foreach ($variant_names as $key => $variant_name) {
                    $variant = Variant::firstOrCreate(['name' => $variant_name]);
                    if($data['itemcode'])
                        $item_code = $item_codes[$key];
                    else
                        $item_code = $variant_name . '-' . $data['code'];
                    
                    if($data['additionalprice'])
                        $additional_price = $additional_prices[$key];
                    else
                        $additional_price = 0;

                    ProductVariant::create([
                        'product_id' => $product->id,
                        'variant_id' => $variant->id,
                        'position' => $key + 1,
                        'item_code' => $item_code,
                        'additional_price' => $additional_price,
                        'qty' => 0
                    ]);
                }
                $product->is_variant = true;
                $product->save();
            }
         }
         return redirect('products')->with('import_message', 'Product imported successfully');
    }

    public function deleteBySelection(Request $request)
    {
        $product_id = $request['productIdArray'];
        foreach ($product_id as $id) {
            $lims_product_data = Product::findOrFail($id);
            $lims_product_data->is_active = false;
            $lims_product_data->save();
        }
        return 'Product deleted successfully!';
    }

    public function destroy($id)
    {
        $lims_product_data = Product::findOrFail($id);
        $lims_product_data->is_active = false;
        /*if($lims_product_data->image != 'zummXD2dvAtI.png') {
            $images = explode(",", $lims_product_data->image);
            foreach ($images as $key => $image) {
                unlink('public/images/product/'.$image);
            }
        }*/
        $lims_product_data->save();
        return redirect('products')->with('message', 'Product deleted successfully');
    }
}
