@extends('layout.main') @section('content')
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div> 
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{!! session()->get('not_permitted') !!}</div> 
@endif
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{trans('file.Create SMS')}}</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.<strong>{{trans('file.Add mobile numbers by selecting the customers')}}</strong></small></p>
                        {!! Form::open(['route' => 'setting.sendSms', 'method' => 'post']) !!}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="lims_customerSearch" id="lims_customerSearch" placeholder="Please type customer name or mobile no and select..." />
                                    </div>
                                    <div class="form-group twilio">
                                        <label>{{trans('file.Mobile')}} *</label>
                                        <input type="text" name="mobile" id="mobile" class="form-control" placeholder="example : +8801*********,+8801*********" required />
                                    </div>
                                    <div class="form-group twilio">
                                        <label>{{trans('file.Message')}} *</label>
                                        <textarea name="message" class="form-control" rows="3" required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-paper-plane"></i> {{trans('file.Send SMS')}}</button> 
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
    $("ul#setting #create-sms-menu").addClass("active");

    <?php $customerArray = []; ?>
    var customer = [ @foreach($lims_customer_list as $customer)
        <?php
            $customerArray[] = $customer->name . ' [' . $customer->phone_number . ']';
        ?>
         @endforeach
            <?php
            echo  '"'.implode('","', $customerArray).'"';
            ?> ];

    var lims_customerSearch = $('#lims_customerSearch');

    lims_customerSearch.autocomplete({
        source: function(request, response) {
            var matcher = new RegExp(".?" + $.ui.autocomplete.escapeRegex(request.term), "i");
            response($.grep(customer, function(item) {
                return matcher.test(item);
            }));
        },
        response: function(event, ui) {
            if (ui.content.length == 1) {
                var data = ui.content[0].value;
                $(this).autocomplete( "close" );
                getNumber(data);
            };
        },
        select: function(event, ui) {
            var data = ui.item.value;
            event.preventDefault();
            getNumber(data);
        }
    });

    function getNumber(data) { 
        mobile_no = data.substring(data.indexOf("[")+1, data.indexOf("]") );
        if( !$('#mobile').val().includes(mobile_no) ){
            if($('#mobile').val() == '')
                $('#mobile').val(mobile_no);
            else
                $('#mobile').val( $('#mobile').val()+','+mobile_no );
        }
        $('#lims_customerSearch').val('');
    }

</script>
@endsection