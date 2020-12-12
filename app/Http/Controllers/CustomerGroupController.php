<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CustomerGroup;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Auth;

class CustomerGroupController extends Controller
{
    public function index()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('customer_group')) {
            $lims_customer_group_all = CustomerGroup::where('is_active', true)->get();
            return view('customer_group.create',compact('lims_customer_group_all'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => [
                'max:255',
                    Rule::unique('customer_groups')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
        ]);
        $lims_customer_group_data = $request->all();
        $lims_customer_group_data['is_active'] = true;
        CustomerGroup::create($lims_customer_group_data);
        return redirect('customer_group')->with('message', 'Data inserted successfully');
    }

    public function edit($id)
    {
        $lims_customer_group_data = CustomerGroup::find($id);
        return $lims_customer_group_data;
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => [
                'max:255',
                    Rule::unique('customer_groups')->ignore($request->customer_group_id)->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
        ]);

        $input = $request->all();
        $lims_customer_group_data = CustomerGroup::find($input['customer_group_id']);

        $lims_customer_group_data->update($input);
        return redirect('customer_group')->with('message', 'Data updated successfully');
    }

    public function importCustomerGroup(Request $request)
    {
        //get file
        $upload=$request->file('file');
        $ext = pathinfo($upload->getClientOriginalName(), PATHINFO_EXTENSION);
        if($ext != 'csv')
            return redirect()->back()->with('not_permitted', 'Please upload a CSV file');
        $filename =  $upload->getClientOriginalName();
        $upload=$request->file('file');
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
        //looping through othe columns
        while($columns=fgetcsv($file))
        {
            if($columns[0]=="")
                continue;
            foreach ($columns as $key => $value) {
                $value=preg_replace('/\D/','',$value);
            }
           $data= array_combine($escapedHeader, $columns);

           $customer_group = CustomerGroup::firstOrNew([ 'name'=>$data['name'], 'is_active'=>true ]);
           $customer_group->name = $data['name'];
           $customer_group->percentage = $data['percentage'];
           $customer_group->is_active = true;
           $customer_group->save();
        }
        return redirect('customer_group')->with('message', 'Customer Group imported successfully');
        
    }

    public function exportCustomerGroup(Request $request)
    {
        $lims_customer_group_data = $request['customer_groupArray'];
        $csvData=array('name, percentage');
        foreach ($lims_customer_group_data as $customer_group) {
            if($customer_group > 0) {
                $data = CustomerGroup::where('id', $customer_group)->first();
                $csvData[]=$data->name. ',' . $data->percentage;
            }   
        }        
        $filename="customer_group- " .date('d-m-Y').".csv";
        $file_path=public_path().'/downloads/'.$filename;
        $file_url=url('/').'/downloads/'.$filename;   
        $file = fopen($file_path,"w+");
        foreach ($csvData as $exp_data){
          fputcsv($file,explode(',',$exp_data));
        }   
        fclose($file);
        return $file_url;
    }

    public function deleteBySelection(Request $request)
    {
        $customer_group_id = $request['customer_groupIdArray'];
        foreach ($customer_group_id as $id) {
            $lims_customer_group_data = CustomerGroup::find($id);
            $lims_customer_group_data->is_active = false;
            $lims_customer_group_data->save();
        }
        return 'Customer Group deleted successfully!';
    }

    public function destroy($id)
    {
        $lims_customer_group_data = CustomerGroup::find($id);
        $lims_customer_group_data->is_active = false;
        $lims_customer_group_data->save();
        return redirect('customer_group')->with('not_permitted', 'Data deleted successfully');
    }
}
