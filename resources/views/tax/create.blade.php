@extends('layout.main') @section('content')

@if($errors->has('name'))
<div class="alert alert-danger alert-dismissible text-center">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ $errors->first('name') }}</div>
@endif
@if($errors->has('rate'))
<div class="alert alert-danger alert-dismissible text-center">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ $errors->first('rate') }}</div>
@endif
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div> 
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div> 
@endif

<section>
    <div class="container-fluid">
        <a href="#" data-toggle="modal" data-target="#createModal" class="btn btn-info"><i class="dripicons-plus"></i> {{trans('file.Add Tax')}}</a>
        <a href="#" data-toggle="modal" data-target="#importTax" class="btn btn-primary"><i class="dripicons-copy"></i> {{trans('file.Import Tax')}}</a>
    </div>
    <div class="table-responsive">
        <table id="tax-table" class="table">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{trans('file.name')}}</th>
                    <th>{{trans('file.Rate')}}(%)</th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lims_tax_all as $key=>$tax)
                <tr data-id="{{$tax->id}}">
                    <td>{{$key}}</td>
                    <td>{{ $tax->name }}</td>
                    <td>{{ $tax->rate }}</td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{trans('file.action')}}
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                <li>
                                    <button type="button" data-id="{{$tax->id}}" class="open-EdittaxDialog btn btn-link" data-toggle="modal" data-target="#editModal"><i class="dripicons-document-edit"></i> {{trans('file.edit')}}
                                </button>
                                </li>
                                <li class="divider"></li>
                                {{ Form::open(['route' => ['tax.destroy', $tax->id], 'method' => 'DELETE'] ) }}
                                <li>
                                    <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> {{trans('file.delete')}}</button>
                                </li>
                                {{ Form::close() }}
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

<div id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            {!! Form::open(['route' => 'tax.store', 'method' => 'post']) !!}
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Add Tax')}}</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
            </div>
            <div class="modal-body">
                <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                <form>
                    <div class="form-group">
                    <label>{{trans('file.Tax Name')}} *</label>
                    {{Form::text('name',null,array('required' => 'required', 'class' => 'form-control'))}}
                    </div>
                    <div class="form-group">
                        <label>{{trans('file.Rate')}}(%) *</label>
                        {{Form::number('rate',null,array('required' => 'required', 'class' => 'form-control', 'step' => 'any'))}}
                    </div>
                    <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
            	</form>
        	</div>
        {{ Form::close() }}
    	</div>
	</div>
</div>

<div id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
	<div role="document" class="modal-dialog">
		  <div class="modal-content">
		    {!! Form::open(['route' => ['tax.update',1], 'method' => 'put']) !!}
		    <div class="modal-header">
		      <h5 id="exampleModalLabel" class="modal-title"> {{trans('file.Update Tax')}}</h5>
		      <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
		    </div>
		    <div class="modal-body">
		      <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
		        <form>
		            <input type="hidden" name="tax_id">
		            <div class="form-group">
		                <label>{{trans('file.Tax Name')}} *</label>
		                {{Form::text('name',null,array('required' => 'required', 'class' => 'form-control'))}}
		            </div>
		            <div class="form-group">
		                <label>{{trans('file.Rate')}}(%) *</label>
		                {{Form::number('rate',null,array('required' => 'required', 'class' => 'form-control', 'step' => 'any'))}}
		            </div>
		            <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
		        </form>
		    </div>
		    {{ Form::close() }}
		  </div>
	</div>
</div>

<div id="importTax" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        {!! Form::open(['route' => 'tax.import', 'method' => 'post', 'files' => true]) !!}
        <div class="modal-header">
          <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Import Tax')}}</h5>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div>
        <div class="modal-body">
            <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
           <p>{{trans('file.The correct column order is')}} (name*, rate*) {{trans('file.and you must follow this')}}.</p>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{trans('file.Upload CSV File')}} *</label>
                        {{Form::file('file', array('class' => 'form-control','required'))}}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label> {{trans('file.Sample File')}}</label>
                        <a href="public/sample_file/sample_tax.csv" class="btn btn-info btn-block btn-md"><i class="dripicons-download"></i>  {{trans('file.Download')}}</a>
                    </div>
                </div>
            </div>
            <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
        </div>
        {{ Form::close() }}
      </div>
    </div>
</div>

<script type="text/javascript">
    $("ul#setting").siblings('a').attr('aria-expanded','true');
    $("ul#setting").addClass("show");
    $("ul#setting #tax-menu").addClass("active");

    var tax_id = [];
    var user_verified = <?php echo json_encode(env('USER_VERIFIED')) ?>;
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

	 function confirmDelete() {
        if (confirm("Are you sure want to delete?")) {
            return true;
        }
        return false;
    }

    $(document).ready(function() {
    $('.open-EdittaxDialog').on('click', function() {
        var url = "tax/"
        var id = $(this).data('id').toString();
        url = url.concat(id).concat("/edit");

        $.get(url, function(data) {
            $("#editModal input[name='name']").val(data['name']);
            $("#editModal input[name='rate']").val(data['rate']);
            $("#editModal input[name='tax_id']").val(data['id']);
        });
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#tax-table').DataTable( {
        "order": [],
        'language': {
            'lengthMenu': '_MENU_ {{trans("file.records per page")}}',
             "info":      '<small>{{trans("file.Showing")}} _START_ - _END_ (_TOTAL_)</small>',
            "search":  '{{trans("file.Search")}}',
            'paginate': {
                    'previous': '<i class="dripicons-chevron-left"></i>',
                    'next': '<i class="dripicons-chevron-right"></i>'
            }
        },
        'columnDefs': [
            {
                "orderable": false,
                'targets': [0, 3]
            },
            {
                'render': function(data, type, row, meta){
                    if(type === 'display'){
                        data = '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
                    }

                   return data;
                },
                'checkboxes': {
                   'selectRow': true,
                   'selectAllRender': '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
                },
                'targets': [0]
            }
        ],
        'select': {
            'style': 'multi'
        },
        'select': { style: 'multi',  selector: 'td:first-child'},
        dom: '<"row"lfB>rtip',
        buttons: [
            {
                extend: 'pdf',
                text: '{{trans("file.PDF")}}',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
            },
            {
                extend: 'csv',
                text: '{{trans("file.CSV")}}',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
            },
            {
                extend: 'print',
                text: '{{trans("file.Print")}}',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
            },
            {
                text: '{{trans("file.delete")}}',
                className: 'buttons-delete',
                action: function ( e, dt, node, config ) {
                    if(user_verified == '1') {
                        tax_id.length = 0;
                        $(':checkbox:checked').each(function(i){
                            if(i){
                                tax_id[i-1] = $(this).closest('tr').data('id');
                            }
                        });
                        if(tax_id.length && confirm("Are you sure want to delete?")) {
                            $.ajax({
                                type:'POST',
                                url:'tax/deletebyselection',
                                data:{
                                    taxIdArray: tax_id
                                },
                                success:function(data){
                                    alert(data);
                                }
                            });
                            dt.rows({ page: 'current', selected: true }).remove().draw(false);
                        }
                        else if(!tax_id.length)
                            alert('No tax is selected!');
                    }
                    else
                        alert('This feature is disable for demo!');
                }
            },
            {
                extend: 'colvis',
                text: '{{trans("file.Column visibility")}}',
                columns: ':gt(0)'
            },
        ],
    } );

});
</script>

@endsection