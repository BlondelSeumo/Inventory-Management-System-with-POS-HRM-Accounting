@extends('layout.main') @section('content')
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div> 
@endif
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{!! session()->get('message') !!}</div> 
@endif
<section>
    <div class="container-fluid">
        @if(in_array("suppliers-add", $all_permission))
        <a href="{{route('supplier.create')}}" class="btn btn-info"><i class="dripicons-plus"></i> {{trans('file.Add Supplier')}}</a>
        <a href="#" data-toggle="modal" data-target="#importSupplier" class="btn btn-primary"><i class="dripicons-copy"></i> {{trans('file.Import Supplier')}}</a>
        @endif
    </div>
    <div class="table-responsive">
        <table id="supplier-table" class="table">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{trans('file.Image')}}</th>
                    <th>{{trans('file.name')}}</th>
                    <th>{{trans('file.Company Name')}}</th>
                    <th>{{trans('file.VAT Number')}}</th>
                    <th>{{trans('file.Email')}}</th>
                    <th>{{trans('file.Phone Number')}}</th>
                    <th>{{trans('file.Address')}}</th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lims_supplier_all as $key=>$supplier)
                <tr data-id="{{$supplier->id}}">
                    <td>{{$key}}</td>
                    @if($supplier->image)
                    <td> <img src="{{url('public/images/supplier',$supplier->image)}}" height="80" width="80">
                    </td>
                    @else
                    <td>No Image</td>
                    @endif
                    <td>{{ $supplier->name }}</td>
                    <td>{{ $supplier->company_name}}</td>
                    <td>{{ $supplier->vat_number}}</td>
                    <td>{{ $supplier->email}}</td>
                    <td>{{ $supplier->phone_number}}</td>
                    <td>{{ $supplier->address}}
                            @if($supplier->city){{ ', '.$supplier->city}}@endif
                            @if($supplier->state){{ ', '.$supplier->state}}@endif
                            @if($supplier->postal_code){{ ', '.$supplier->postal_code}}@endif
                            @if($supplier->country){{ ', '.$supplier->country}}@endif</td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{trans('file.action')}}
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                @if(in_array("suppliers-edit", $all_permission))
                                <li>
                                	<a href="{{ route('supplier.edit', $supplier->id) }}" class="btn btn-link"><i class="dripicons-document-edit"></i> {{trans('file.edit')}}</a>
                                </li>
                                @endif
                                <li class="divider"></li>
                                @if(in_array("suppliers-delete", $all_permission))
                                {{ Form::open(['route' => ['supplier.destroy', $supplier->id], 'method' => 'DELETE'] ) }}
                                <li>
                                    <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> {{trans('file.delete')}}</button>
                                </li>
                                {{ Form::close() }}
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

<div id="importSupplier" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
	<div role="document" class="modal-dialog">
	  <div class="modal-content">
	  	{!! Form::open(['route' => 'supplier.import', 'method' => 'post', 'files' => true]) !!}
	    <div class="modal-header">
	      <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Import Supplier')}}</h5>
	      <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
	    </div>
	    <div class="modal-body">
	      <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
	       <p>{{trans('file.The correct column order is')}} (name*, image, company_name*, vat_number, email*, phone_number*, address*, city*,state, postal_code, country) {{trans('file.and you must follow this')}}.</p>
           <p>{{trans('file.To display Image it must be stored in')}} public/images/supplier {{trans('file.directory')}}</p>
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
                        <a href="public/sample_file/sample_supplier.csv" class="btn btn-info btn-block btn-md"><i class="dripicons-download"></i> {{trans('file.Download')}}</a>
                    </div>
                </div>
            </div>
	        <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary" id="submit-button">
		</div>
		{!! Form::close() !!}
	  </div>
	</div>
</div>

<script type="text/javascript">

    $("ul#people").siblings('a').attr('aria-expanded','true');
    $("ul#people").addClass("show");
    $("ul#people #supplier-list-menu").addClass("active");

    var all_permission = <?php echo json_encode($all_permission) ?>;
    var supplier_id = [];
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

    $('#supplier-table').DataTable( {
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
                'targets': [0, 1, 8]
            },
            {
                'checkboxes': {
                   'selectRow': true
                },
                'targets': 0
            }
        ],
        'select': { style: 'multi',  selector: 'td:first-child'},
        'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: '<"row"lfB>rtip',
        buttons: [
            {
                extend: 'pdf',
                text: '{{trans("file.PDF")}}',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible',
                    stripHtml: false
                },
                customize: function(doc) {
                    for (var i = 1; i < doc.content[1].table.body.length; i++) {
                        if (doc.content[1].table.body[i][0].text.indexOf('<img src=') !== -1) {
                            var imagehtml = doc.content[1].table.body[i][0].text;
                            var regex = /<img.*?src=['"](.*?)['"]/;
                            var src = regex.exec(imagehtml)[1];
                            var tempImage = new Image();
                            tempImage.src = src;
                            var canvas = document.createElement("canvas");
                            canvas.width = tempImage.width;
                            canvas.height = tempImage.height;
                            var ctx = canvas.getContext("2d");
                            ctx.drawImage(tempImage, 0, 0);
                            var imagedata = canvas.toDataURL("image/png");
                            delete doc.content[1].table.body[i][0].text;
                            doc.content[1].table.body[i][0].image = imagedata;
                            doc.content[1].table.body[i][0].fit = [30, 30];
                        }
                    }
                },
            },
            {
                extend: 'csv',
                text: '{{trans("file.CSV")}}',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible',
                    format: {
                        body: function ( data, row, column, node ) {
                            if (column === 0 && (data.indexOf('<img src=') !== -1)) {
                                var regex = /<img.*?src=['"](.*?)['"]/;
                                data = regex.exec(data)[1];                 
                            }
                            return data;
                        }
                    }
                },
            },
            {
                extend: 'print',
                text: '{{trans("file.Print")}}',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible',
                    stripHtml: false
                },
            },
            {
                text: '{{trans("file.delete")}}',
                className: 'buttons-delete',
                action: function ( e, dt, node, config ) {
                    if(user_verified == '1') {
                        supplier_id.length = 0;
                        $(':checkbox:checked').each(function(i){
                            if(i){
                                supplier_id[i-1] = $(this).closest('tr').data('id');
                            }
                        });
                        if(supplier_id.length && confirm("Are you sure want to delete?")) {
                            $.ajax({
                                type:'POST',
                                url:'supplier/deletebyselection',
                                data:{
                                    supplierIdArray: supplier_id
                                },
                                success:function(data){
                                    alert(data);
                                }
                            });
                            dt.rows({ page: 'current', selected: true }).remove().draw(false);
                        }
                        else if(!supplier_id.length)
                            alert('No supplier is selected!');
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

    if(all_permission.indexOf("suppliers-delete") == -1)
        $('.buttons-delete').addClass('d-none');

</script>
@endsection