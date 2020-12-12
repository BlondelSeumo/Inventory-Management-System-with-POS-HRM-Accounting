<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Customer;
use App\User;
use App\GiftCard;
use App\GiftCardRecharge;
use Keygen;
use Auth;
use Illuminate\Validation\Rule;
use App\Mail\UserNotification;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class GiftCardController extends Controller
{
    public function index()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('unit')) {
            $lims_customer_list = Customer::where('is_active', true)->get();
            $lims_user_list = User::where('is_active', true)->get();
            $lims_gift_card_all = GiftCard::where('is_active', true)->orderBy('id', 'desc')->get();

            return view('gift_card.index', compact('lims_customer_list', 'lims_user_list', 'lims_gift_card_all'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function create()
    {
        //
    }

    public function generateCode()
    {
        $id = Keygen::numeric(16)->generate();
        return $id;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'card_no' => [
                'max:255',
                    Rule::unique('gift_cards')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ]
        ]);

        $data = $request->all();

        if($request->input('user'))
            $data['customer_id'] = null;
        else
            $data['user_id'] = null;

        $data['is_active'] = true;
        $data['created_by'] = Auth::id();
        $data['expense'] = 0;
        GiftCard::create($data);
        $message = 'GiftCard created successfully';
        if($data['user_id']){
            $lims_user_data = User::find($data['user_id']);
            $data['email'] = $lims_user_data->email;
            $data['name'] = $lims_user_data->name;
            try{
                Mail::send( 'mail.gift_card_create', $data, function( $message ) use ($data)
                {
                    $message->to( $data['email'] )->subject( 'GiftCard' );
                });
            }
            catch(\Exception $e){
                $message = 'GiftCard created successfully. Please setup your <a href="setting/mail_setting">mail setting</a> to send mail.';
            }
        }
        else{
            $lims_customer_data = Customer::find($data['customer_id']);
            if($lims_customer_data->email){
                $data['email'] = $lims_customer_data->email;
                $data['name'] = $lims_customer_data->name;
                try{
                    Mail::send( 'mail.gift_card_create', $data, function( $message ) use ($data)
                    {
                        $message->to( $data['email'] )->subject( 'GiftCard' );
                    });
                }
                catch(\Exception $e){
                    $message = 'GiftCard created successfully. Please setup your <a href="setting/mail_setting">mail setting</a> to send mail.';
                }
            }
        }
        return redirect('gift_cards')->with('message', $message);
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $lims_gift_card_data = GiftCard::find($id);
        return $lims_gift_card_data;
    }

    public function update(Request $request, $id)
    {
        $request['card_no'] = $request['card_no_edit'];
        $this->validate($request, [
            'card_no' => [
                'max:255',
                Rule::unique('gift_cards')->ignore($request['gift_card_id'])->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ]
        ]);

        $data = $request->all();
        $lims_gift_card_data = GiftCard::find($data['gift_card_id']);
        $lims_gift_card_data->card_no = $data['card_no_edit'];
        $lims_gift_card_data->amount = $data['amount_edit'];
        if($request->input('user_edit')){
            $lims_gift_card_data->user_id = $data['user_id_edit'];
            $lims_gift_card_data->customer_id = null;
        }
        else{
            $lims_gift_card_data->user_id = null;
            $lims_gift_card_data->customer_id = $data['customer_id_edit'];
        }
        $lims_gift_card_data->expired_date = $data['expired_date_edit'];
        $lims_gift_card_data->save();
        return redirect('gift_cards')->with('message', 'GiftCard updated successfully');
    }

    public function recharge(Request $request, $id)
    {
        $data = $request->all();
        $data['user_id'] = Auth::id();
        $lims_gift_card_data = GiftCard::find($data['gift_card_id']);
        if($lims_gift_card_data->customer_id)
            $lims_customer_data = Customer::find($lims_gift_card_data->customer_id);
        else
            $lims_customer_data = User::find($lims_gift_card_data->user_id);
        
        $lims_gift_card_data->amount += $data['amount'];
        $lims_gift_card_data->save();
        GiftCardRecharge::create($data);
        $message = 'GiftCard recharged successfully';
        if($lims_customer_data->email){
            $data['email'] = $lims_customer_data->email;
            $data['name'] = $lims_customer_data->name;
            $data['card_no'] = $lims_gift_card_data->card_no;
            $data['balance'] = $lims_gift_card_data->amount - $lims_gift_card_data->expense;
            try{
                Mail::send( 'mail.gift_card_recharge', $data, function( $message ) use ($data)
                {
                    $message->to( $data['email'] )->subject( 'GiftCard Recharge Info' );
                });
            }
            catch(\Exception $e){
                $message = 'GiftCard recharged successfully. Please setup your <a href="setting/mail_setting">mail setting</a> to send mail.';
            }  
        }
        return redirect('gift_cards')->with('message', $message);
    }

    public function deleteBySelection(Request $request)
    {
        $gift_card_id = $request['gift_cardIdArray'];
        foreach ($gift_card_id as $id) {
            $lims_gift_card_data = GiftCard::find($id);
            $lims_gift_card_data->is_active = false;
            $lims_gift_card_data->save();
        }
        return 'Gift Card deleted successfully!';
    }

    public function destroy($id)
    {
        $lims_gift_card_data = GiftCard::find($id);
        $lims_gift_card_data->is_active = false;
        $lims_gift_card_data->save();
        return redirect('gift_cards')->with('not_permitted', 'Data deleted successfully');
    }
}
