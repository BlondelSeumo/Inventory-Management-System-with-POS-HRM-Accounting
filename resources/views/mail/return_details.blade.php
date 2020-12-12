<h1>Return Details</h1>
<p><strong>Reference: </strong>{{$reference_no}}</p>
<h3>Order Table</h3>
<table style="border-collapse: collapse; width: 100%;">
	<thead>
		<th style="border: 1px solid #000; padding: 5px">#</th>
		<th style="border: 1px solid #000; padding: 5px">Product</th>
		<th style="border: 1px solid #000; padding: 5px">Qty</th>
		<th style="border: 1px solid #000; padding: 5px">Unit Price</th>
		<th style="border: 1px solid #000; padding: 5px">SubTotal</th>
	</thead>
	<tbody>
		@foreach($products as $key=>$product)
		<tr>
			<td style="border: 1px solid #000; padding: 5px">{{$key+1}}</td>
			<td style="border: 1px solid #000; padding: 5px">{{$product}}</td>
			<td style="border: 1px solid #000; padding: 5px">{{$qty[$key].' '.$unit[$key]}}</td>
			<td style="border: 1px solid #000; padding: 5px">{{number_format((float)($total[$key] / $qty[$key]), 2, '.', '')}}</td>
			<td style="border: 1px solid #000; padding: 5px">{{$total[$key]}}</td>
		</tr>
		@endforeach
		<tr>
			<td colspan="2" style="border: 1px solid #000; padding: 5px"><strong>Total </strong></td>
			<td style="border: 1px solid #000; padding: 5px">{{$total_qty}}</td>
			<td style="border: 1px solid #000; padding: 5px"></td>
			<td style="border: 1px solid #000; padding: 5px">{{$total_price}}</td>
		</tr>
		<tr>
			<td colspan="4" style="border: 1px solid #000; padding: 5px"><strong>Order Tax </strong> </td>
			<td style="border: 1px solid #000; padding: 5px">{{$order_tax.'('.$order_tax_rate.'%)'}}</td>
		</tr>
		<tr>
			<td colspan="4" style="border: 1px solid #000; padding: 5px"><strong>Grand Total</strong></td>
			<td style="border: 1px solid #000; padding: 5px">{{$grand_total}}</td>
		</tr>
	</tbody>
</table>

<p>Thank You</p>