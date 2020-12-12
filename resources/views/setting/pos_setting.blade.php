@extends('layout.main') @section('content')

@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div> 
@endif

@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div> 
@endif
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{trans('file.POS Setting')}}</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                        {!! Form::open(['route' => 'setting.posStore', 'method' => 'post']) !!}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{trans('file.Default Customer')}} *</label>
                                        @if($lims_pos_setting_data)
                                        <input type="hidden" name="customer_id_hidden" value="{{$lims_pos_setting_data->customer_id}}">
                                        @endif
                                        <select required name="customer_id" id="customer_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select customer...">
                                            @foreach($lims_customer_list as $customer)
                                            <option value="{{$customer->id}}">{{$customer->name . ' (' . $customer->phone_number . ')'}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>{{trans('file.Default Biller')}} *</label>
                                        @if($lims_pos_setting_data)
                                        <input type="hidden" name="biller_id_hidden" value="{{$lims_pos_setting_data->biller_id}}">
                                        @endif
                                        <select required name="biller_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select Biller...">
                                            @foreach($lims_biller_list as $biller)
                                            <option value="{{$biller->id}}">{{$biller->name . ' (' . $biller->company_name . ')'}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Stripe Publishable key</label>
                                        <input type="text" name="stripe_public_key" class="form-control" value="@if($lims_pos_setting_data){{$lims_pos_setting_data->stripe_public_key}}@endif" required />
                                    </div>
                                    <div class="form-group">
                                        <label>Paypal Pro API Username</label>
                                        <input type="text" name="paypal_username" class="form-control" value="{{env('PAYPAL_SANDBOX_API_USERNAME')}}" />
                                    </div>
                                    <div class="form-group">
                                        <label>Paypal Pro API Signature</label>
                                        <input type="text" name="paypal_signature" class="form-control" value="{{env('PAYPAL_SANDBOX_API_SECRET')}}" />
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{trans('file.Default Warehouse')}} *</label>
                                        @if($lims_pos_setting_data)
                                        <input type="hidden" name="warehouse_id_hidden" value="{{$lims_pos_setting_data->warehouse_id}}">
                                        @endif
                                        <select required name="warehouse_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select warehouse...">
                                            @foreach($lims_warehouse_list as $warehouse)
                                            <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>{{trans('file.Displayed Number of Product Row')}} *</label>
                                        <input type="number" name="product_number" class="form-control" value="@if($lims_pos_setting_data){{$lims_pos_setting_data->product_number}}@endif" required />
                                    </div>
                                    <div class="form-group">
                                        <label>Stripe Secret key *</label>
                                        <input type="text" name="stripe_secret_key" class="form-control" value="@if($lims_pos_setting_data){{$lims_pos_setting_data->stripe_secret_key}}@endif"required />
                                    </div>
                                    <div class="form-group">
                                        <label>Paypal Pro API Password</label>
                                        <input type="password" name="paypal_password" class="form-control" value="{{env('PAYPAL_SANDBOX_API_PASSWORD')}}" />
                                    </div>
                                    <div class="form-group">
                                        @if($lims_pos_setting_data && $lims_pos_setting_data->keybord_active)
                                        <input class="mt-2" type="checkbox" name="keybord_active" value="1" checked>
                                        @else
                                        <input class="mt-2" type="checkbox" name="keybord_active" value="1">
                                        @endif
                                        <label class="mt-2"><strong>{{trans('file.Touchscreen keybord')}}</label>
                                    </div>
                                </div>                              
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">

    $("ul#setting").siblings('a').attr('aria-expanded','true');
    $("ul#setting").addClass("show");
    $("ul#setting #pos-setting-menu").addClass("active");

    

    $('select[name="customer_id"]').val($("input[name='customer_id_hidden']").val());
    $('select[name="biller_id"]').val($("input[name='biller_id_hidden']").val());
    $('select[name="warehouse_id"]').val($("input[name='warehouse_id_hidden']").val());
    $('.selectpicker').selectpicker('refresh');

</script>
@endsection