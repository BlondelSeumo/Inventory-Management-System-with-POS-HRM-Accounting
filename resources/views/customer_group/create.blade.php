@extends('layout.main')
@section('content')
@if($errors->has('name'))
<div class="alert alert-danger alert-dismissible text-center">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ $errors->first('name') }}</div>
@endif
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div> 
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div> 
@endif

<section>
    <div class="container-fluid">
        <a href="#" data-toggle="modal" data-target="#createModal" class="btn btn-info"><i class="dripicons-plus"></i> {{trans('file.Add Customer Group')}}</a>
        <a href="#" data-toggle="modal" data-target="#importcustomer_group" class="btn btn-primary"><i class="dripicons-copy"></i> {{trans('file.Import Customer Group')}}</a>
    </div>
    <div class="table-responsive">
        <table id="customer-grp-table" class="table">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{trans('file.name')}}</th>
                    <th>{{trans('file.Percentage')}}</th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lims_customer_group_all as $key=>$customer_group)
                <tr data-id="{{$customer_group->id}}">
                    <td>{{$key}}</td>
                    <td>{{ $customer_group->name }}</td>
                    <td>{{ $customer_group->percentage}}</td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{trans('file.action')}}
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                <li>
                                    <button type="button" data-id="{{$customer_group->id}}" class="open-EditCustomerGroupDialog btn btn-link" data-toggle="modal" data-target="#editModal"><i class="dripicons-document-edit"></i> {{trans('file.edit')}}
                                    </button>
                                </li>
                                <li class="divider"></li>
                                {{ Form::open(['route' => ['customer_group.destroy', $customer_group->id], 'method' => 'DELETE'] ) }}
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
    {!! Form::open(['route' => 'customer_group.store', 'method' => 'post']) !!}
    <div class="modal-header">
      <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Add Customer Group')}}</h5>
      <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
    </div>
    <div class="modal-body">
      <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
      <form>
        <div class="form-group">
          <label>{{trans('file.name')}} *</label>
          <input type="text" name="name" required="required" class="form-control">
        </div>
        <div class="form-group">       
          <label>{{trans('file.Percentage')}}(%) *</label>
          <input type="text" name="percentage" required="required" class="form-control">
        </div>                
        <div class="form-group">       
          <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
        </div>
      </form>
    </div>

    {{ Form::close() }}
  </div>
</div>
</div>

<div id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
<div role="document" class="modal-dialog">
  <div class="modal-content">
    {!! Form::open(['route' => ['customer_group.update',1], 'method' => 'put']) !!}
    <div class="modal-header">
      <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Update Customer Group')}}</h5>
      <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
    </div>
    <div class="modal-body">
      <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
        <div class="form-group">
            <input type="hidden" name="customer_group_id">
          <label>{{trans('file.name')}} *</label>
          <input type="text" name="name" required="required" class="form-control">
        </div>
        <div class="form-group">       
          <label>{{trans('file.Percentage')}}(%) *</label>
          <input type="text" name="percentage" required="required" class="form-control">
        </div>                
        <div class="form-group">       
          <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
        </div>
    </div>
    {{ Form::close() }}
  </div>
</div>
</div>

<div id="importcustomer_group" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
<div role="document" class="modal-dialog">
  <div class="modal-content">
    {!! Form::open(['route' => 'customer_group.import', 'method' => 'post', 'files' => true]) !!}
    <div class="modal-header">
      <h5 id="exampleModalLabel" class="modal-title"> {{trans('file.Import Customer Group')}}</h5>
      <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
    </div>
    <div class="modal-body">
        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
       <p>{{trans('file.The correct column order is')}} (name*, percentage*) {{trans('file.and you must follow this')}}.</p>
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
                    <a href="public/sample_file/sample_customer_group.csv" class="btn btn-info btn-block btn-md"><i class="dripicons-download"></i>  {{trans('file.Download')}}</a>
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
    $("ul#setting #customer-group-menu").addClass("active");

    var customer_group_id = [];
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
        
        $('.open-EditCustomerGroupDialog').on('click', function() {
            var url = "customer_group/"
            var id = $(this).data('id').toString();
            url = url.concat(id).concat("/edit");

            $.get(url, function(data) {
                $("input[name='name']").val(data['name']);
                $("input[name='percentage']").val(data['percentage']);
                $("input[name='customer_group_id']").val(data['id']);
            });
        });
    });

    $('#customer-grp-table').DataTable( {
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
        'select': { style: 'multi',  selector: 'td:first-child'},
        'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
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
                        customer_group_id.length = 0;
                        $(':checkbox:checked').each(function(i){
                            if(i){
                                customer_group_id[i-1] = $(this).closest('tr').data('id');
                            }
                        });
                        if(customer_group_id.length && confirm("Are you sure want to delete?")) {
                            $.ajax({
                                type:'POST',
                                url:'customer_group/deletebyselection',
                                data:{
                                    customer_groupIdArray: customer_group_id
                                },
                                success:function(data){
                                    alert(data);
                                }
                            });
                            dt.rows({ page: 'current', selected: true }).remove().draw(false);
                        }
                        else if(!customer_group_id.length)
                            alert('No customer group is selected!');
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

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $( "#select_all" ).on( "change", function() {
        if ($(this).is(':checked')) {
            $("tbody input[type='checkbox']").prop('checked', true);
        } 
        else {
            $("tbody input[type='checkbox']").prop('checked', false);
        }
    });

    $("#export").on("click", function(e){
    e.preventDefault();
    var customer_group = [];
    $(':checkbox:checked').each(function(i){
      customer_group[i] = $(this).val();
    });
    $.ajax({
       type:'POST',
       url:'/exportcustomer_group',
       data:{
            customer_groupArray: customer_group
        },
       success:function(data){
         alert('Exported to CSV file successfully! Click Ok to download file');
         window.location.href = data;
       }
    });
});
</script>

@endsection