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
                        <h4>{{trans('file.Mail Setting')}}</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                        {!! Form::open(['route' => 'setting.mailStore', 'files' => true, 'method' => 'post']) !!}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{trans('file.Mail Host')}} *</label>
                                        <input type="text" name="mail_host" class="form-control" value="{{env('MAIL_HOST')}}" required />
                                    </div>
                                    <div class="form-group">
                                        <label>{{trans('file.Mail Address')}} *</label>
                                        <input type="text" name="mail_address" class="form-control" value="{{env('MAIL_FROM_ADDRESS')}}" required />
                                    </div>
                                    <div class="form-group">
                                        <label>{{trans('file.Mail From Name')}} *</label>
                                        <input type="text" name="mail_name" class="form-control" value="{{env('MAIL_FROM_NAME')}}" required />
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{trans('file.Mail Port')}} *</label>
                                        <input type="text" name="port" class="form-control" value="{{env('MAIL_PORT')}}" required />
                                    </div>
                                    <div class="form-group">
                                        <label>{{trans('file.Password')}} *</label>
                                        <input type="password" name="password" class="form-control" value="{{env('MAIL_PASSWORD')}}" required />
                                    </div>
                                    <div class="form-group">
                                        <label>{{trans('file.Encryption')}} *</label>
                                        <input type="text" name="encryption" class="form-control" value="{{env('MAIL_ENCRYPTION')}}" required />
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
    $("ul#setting #mail-setting-menu").addClass("active");

    

</script>
@endsection