<h1>Sale Details</h1>
<p><strong>Reference: </strong>{{$reference_no}}</p>
<p>
	<strong>Sale Status: </strong>
	@if($sale_status==1){{'Completed'}}
	@elseif($sale_status==2){{'Pending'}}
	@endif
</p>
<p>
	<strong>Payment Status: </strong>
	@if($payment_status==1){{'Pending'}}
	@elseif($payment_status==2){{'Due'}}
	@elseif($payment_status==3){{'Partial'}}
	@else{{'Paid'}}@endif
</p>
<h3>Order Table</h3>
<table style="border-collapse: collapse; width: 100%;">
	<thead>
		<th style="border: 1px solid #000; padding: 5px">#</th>
		<th style="border: 1px solid #000; padding: 5px">Product</th>
		<th style="border: 1px solid #000; padding: 5px">Download Link</th>
		<th style="border: 1px solid #000; padding: 5px">Qty</th>
		<th style="border: 1px solid #000; padding: 5px">Unit Price</th>
		<th style="border: 1px solid #000; padding: 5px">SubTotal</th>
	</thead>
	<tbody>
		@foreach($products as $key=>$product)
		<tr>
			<td style="border: 1px solid #000; padding: 5px">{{$key+1}}</td>
			<td style="border: 1px solid #000; padding: 5px">{{$product}}</td>
			@if($file[$key])
				<td style="border: 1px solid #000; padding: 5px"><a href="{{ $file[$key] }}">Download</a></td>
			@else
				<td style="border: 1px solid #000; padding: 5px">N/A</td>
			@endif
			<td style="border: 1px solid #000; padding: 5px">{{$qty[$key].' '.$unit[$key]}}</td>
			<td style="border: 1px solid #000; padding: 5px">{{number_format((float)($total[$key] / $qty[$key]), 2, '.', '')}}</td>
			<td style="border: 1px solid #000; padding: 5px">{{$total[$key]}}</td>
		</tr>
		@endforeach
		<tr>
			<td colspan="3" style="border: 1px solid #000; padding: 5px"><strong>Total </strong></td>
			<td style="border: 1px solid #000; padding: 5px">{{$total_qty}}</td>
			<td style="border: 1px solid #000; padding: 5px"></td>
			<td style="border: 1px solid #000; padding: 5px">{{$total_price}}</td>
		</tr>
		<tr>
			<td colspan="5" style="border: 1px solid #000; padding: 5px"><strong>Order Tax </strong> </td>
			<td style="border: 1px solid #000; padding: 5px">{{$order_tax.'('.$order_tax_rate.'%)'}}</td>
		</tr>
		<tr>
			<td colspan="5" style="border: 1px solid #000; padding: 5px"><strong>Order discount </strong> </td>
			<td style="border: 1px solid #000; padding: 5px">
				@if($order_discount){{$order_discount}}
				@else 0 @endif
			</td>
		</tr>
		<tr>
			<td colspan="5" style="border: 1px solid #000; padding: 5px"><strong>Shipping Cost</strong> </td>
			<td style="border: 1px solid #000; padding: 5px">
				@if($shipping_cost){{$shipping_cost}}
				@else 0 @endif
			</td>
		</tr>
		<tr>
			<td colspan="5" style="border: 1px solid #000; padding: 5px"><strong>Grand Total</strong></td>
			<td style="border: 1px solid #000; padding: 5px">{{$grand_total}}</td>
		</tr>
		<tr>
			<td colspan="5" style="border: 1px solid #000; padding: 5px"><strong>Paid Amount</strong></td>
			<td style="border: 1px solid #000; padding: 5px">
				@if($paid_amount){{$paid_amount}}
				@else 0 @endif
			</td>
		</tr>
		<tr>
			<td colspan="5" style="border: 1px solid #000; padding: 5px"><strong>Due</strong></td>
			<td style="border: 1px solid #000; padding: 5px">{{number_format((float)($grand_total - $paid_amount), 2, '.', '')}}</td>
		</tr>
	</tbody>
</table>

<p>Thank You</p>