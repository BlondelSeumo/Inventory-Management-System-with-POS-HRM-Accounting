@extends('layout.main') @section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			{{ Form::open(['route' => 'report.bestSellerByWarehouse', 'method' => 'post', 'id' => 'report-form']) }}
			<input type="hidden" name="warehouse_id_hidden" value="{{$warehouse_id}}">
            <h4 class="text-center mt-3">{{trans('file.Best Seller')}} {{trans('file.From')}} {{$start_month.' - '.date("F Y")}} &nbsp;&nbsp;
            <select class="selectpicker" id="warehouse_id" name="warehouse_id">
				<option value="0">{{trans('file.All Warehouse')}}</option>
				@foreach($lims_warehouse_list as $warehouse)
				<option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
				@endforeach
			</select>
            </h4>
            {{ Form::close() }}
            <div class="card-body">
            	@php
            		if($general_setting->theme == 'default.css'){
            			$color = '#733686';
                        $color_rgba = 'rgba(115, 54, 134, 0.8)';
            		}
            		elseif($general_setting->theme == 'green.css'){
                        $color = '#2ecc71';
                        $color_rgba = 'rgba(46, 204, 113, 0.8)';
                    }
                    elseif($general_setting->theme == 'blue.css'){
                        $color = '#3498db';
                        $color_rgba = 'rgba(52, 152, 219, 0.8)';
                    }
                    elseif($general_setting->theme == 'dark.css'){
                        $color = '#34495e';
                        $color_rgba = 'rgba(52, 73, 94, 0.8)';
                    }
                 @endphp
              	<canvas id="bestSeller" data-color="{{$color}}" data-color_rgba="{{$color_rgba}}" data-product = "{{json_encode($product)}}" data-sold_qty="{{json_encode($sold_qty)}}" ></canvas>
            </div>
        </div>
	</div>
</div>

<script type="text/javascript">

	$("ul#report").siblings('a').attr('aria-expanded','true');
    $("ul#report").addClass("show");
    $("ul#report #best-seller-report-menu").addClass("active");

	$('#warehouse_id').val($('input[name="warehouse_id_hidden"]').val());
	$('.selectpicker').selectpicker('refresh');

	$('#warehouse_id').on("change", function(){
		$('#report-form').submit();
	});
</script>
@endsection