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
                        <h4>{{trans('file.SMS Setting')}}</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                        {!! Form::open(['route' => 'setting.smsStore', 'method' => 'post']) !!}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="hidden" name="gateway_hidden" value="{{env('SMS_GATEWAY')}}">
                                        <label>{{trans('file.Gateway')}} *</label>
                                        <select class="form-control" name="gateway">
                                            <option selected disabled>{{trans('file.Select SMS gateway...')}}</option>
                                            <option value="twilio">Twilio</option>
                                            <option value="clickatell">Clickatell</option>
                                        </select>
                                    </div>
                                    <div class="form-group twilio">
                                        <label>ACCOUNT SID *</label>
                                        <input type="text" name="account_sid" class="form-control twilio-option" value="{{env('ACCOUNT_SID')}}" />
                                    </div>
                                    <div class="form-group twilio">
                                        <label>AUTH TOKEN *</label>
                                        <input type="text" name="auth_token" class="form-control twilio-option" value="{{env('AUTH_TOKEN')}}" />
                                    </div>
                                    <div class="form-group twilio">
                                        <label>Twilio Number *</label>
                                        <input type="text" name="twilio_number" class="form-control twilio-option" value="{{env('Twilio_Number')}}" />
                                    </div>
                                    <div class="form-group clickatell">
                                        <label>API Key *</label>
                                        <input type="text" name="api_key" class="form-control clickatell-option" value="{{env('CLICKATELL_API_KEY')}}" />
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
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
    $("ul#setting #sms-setting-menu").addClass("active");

    if( $('input[name="gateway_hidden"]').val() == 'twilio' ){
        $('select[name="gateway"]').val('twilio');
        $('.clickatell').hide();
    }
    else if( $('input[name="gateway_hidden"]').val() == 'clickatell' ){
        $('select[name="gateway"]').val('clickatell');
        $('.twilio').hide();
    }
    else{
        $('.clickatell').hide();
        $('.twilio').hide();
    }

    $('select[name="gateway"]').on('change', function(){
        if( $(this).val() == 'twilio' ){
            $('.clickatell').hide();
            $('.twilio').show(500);
            $('.twilio-option').prop('required',true);
            $('.clickatell-option').prop('required',false);
        }
        else if( $(this).val() == 'clickatell' ){
            $('.twilio').hide();
            $('.clickatell').show(500);
            $('.twilio-option').prop('required',false);
            $('.clickatell-option').prop('required',true);
        }
    });

</script>
@endsection