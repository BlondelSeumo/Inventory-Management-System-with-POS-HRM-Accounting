<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Biller;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use App\Mail\UserNotification;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Auth;

class BillerController extends Controller
{
    public function index()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('billers-index')) {
            $permissions = Role::findByName($role->name)->permissions;
            foreach ($permissions as $permission)
                $all_permission[] = $permission->name;
            if(empty($all_permission))
                $all_permission[] = 'dummy text';
            $lims_biller_all = biller::where('is_active', true)->get();
            return view('biller.index',compact('lims_biller_all', 'all_permission'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function create()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('billers-add'))
            return view('biller.create');
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function store(Request $request)
    {
    	$this->validate($request, [
            'company_name' => [
                'max:255',
                    Rule::unique('billers')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
            'email' => [
                'email',
                'max:255',
                    Rule::unique('billers')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
            'image' => 'image|mimes:jpg,jpeg,png,gif|max:10000',
        ]);

        $lims_biller_data = $request->except('image');
        $lims_biller_data['is_active'] = true;
        $image = $request->image;
        if ($image) {
            $ext = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
            $imageName = preg_replace('/[^a-zA-Z0-9]/', '', $request['company_name']);
            /*Image::make($image)
                ->resize(250, null, function ($constraints) {
                    $constraints->aspectRatio();
                })->save('public/images/biller/' . $imageName.'-resize.'.$ext);*/
            $imageName = $imageName . '.' . $ext;
            $image->move('public/images/biller', $imageName);
            
            $lims_biller_data['image'] = $imageName;
        }
        Biller::create($lims_biller_data);
        $message = 'Data inserted successfully';
        try{
            Mail::send( 'mail.biller_create', $lims_biller_data, function( $message ) use ($lims_biller_data)
            {
                $message->to( $lims_biller_data['email'] )->subject( 'New Biller' );
            });
        }
        catch(\Exception $e){
            $message = 'Data inserted successfully. Please setup your <a href="setting/mail_setting">mail setting</a> to send mail.';
        }  
        return redirect('biller')->with('message', $message);
    }

    public function edit($id)
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('billers-edit')) {
            $lims_biller_data = Biller::where('id',$id)->first();
            return view('biller.edit',compact('lims_biller_data'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'company_name' => [
                'max:255',
                    Rule::unique('billers')->ignore($id)->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
            'email' => [
                'email',
                'max:255',
                    Rule::unique('billers')->ignore($id)->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],

            'image' => 'image|mimes:jpg,jpeg,png,gif|max:100000',
        ]);

        $input = $request->except('image');
        $image = $request->image;
        if ($image) {
            $ext = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
            $imageName = preg_replace('/[^a-zA-Z0-9]/', '', $request['company_name']);
            $imageName = $imageName . '.' . $ext;
            $image->move('public/images/biller', $imageName);
            $input['image'] = $imageName;
        }

        $lims_biller_data = Biller::findOrFail($id);
        $lims_biller_data->update($input);
        return redirect('biller')->with('message','Data updated successfully');
    }

    public function importBiller(Request $request)
    {
        $upload=$request->file('file');
        $ext = pathinfo($upload->getClientOriginalName(), PATHINFO_EXTENSION);
        if($ext != 'csv')
            return redirect()->back()->with('not_permitted', 'Please upload a CSV file');
        $filename =  $upload->getClientOriginalName();
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

           $biller = Biller::firstOrNew(['company_name'=>$data['companyname']]);
           $biller->name = $data['name'];
           $biller->image = $data['image'];
           $biller->vat_number = $data['vatnumber'];
           $biller->email = $data['email'];
           $biller->phone_number = $data['phonenumber'];
           $biller->address = $data['address'];
           $biller->city = $data['city'];
           $biller->state = $data['state'];
           $biller->postal_code = $data['postalcode'];
           $biller->country = $data['country'];
           $biller->is_active = true;
           $biller->save();
           $message = 'Biller Imported successfully';
           if($data['email']){
                try{
                    Mail::send( 'mail.biller_create', $data, function( $message ) use ($data)
                    {
                        $message->to( $data['email'] )->subject( 'New Biller' );
                    });
                }
                catch(\Exception $e){
                    $message = 'Biller Imported successfully. Please setup your <a href="setting/mail_setting">mail setting</a> to send mail.';
                }
            }
        }
        return redirect('biller')->with('message', $message);
        
    }

    public function deleteBySelection(Request $request)
    {
        $biller_id = $request['billerIdArray'];
        foreach ($biller_id as $id) {
            $lims_biller_data = Biller::find($id);
            $lims_biller_data->is_active = false;
            $lims_biller_data->save();
        }
        return 'Biller deleted successfully!';
    }

    public function destroy($id)
    {
        $lims_biller_data = Biller::find($id);
        $lims_biller_data->is_active = false;
        $lims_biller_data->save();
        return redirect('biller')->with('not_permitted','Data deleted successfully');
    }
}
