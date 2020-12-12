

<table style="border-collapse: collapse; width: 100%;">
	<tbody>
		<tr>
			<td style="border: 1px solid #000; padding: 5px">Date</td>
			<td style="border: 1px solid #000; padding: 5px">{{$date}}</td>
		</tr>
		<tr>
			<td style="border: 1px solid #000; padding: 5px">Delivery Reference</td>
			<td style="border: 1px solid #000; padding: 5px">{{$delivery_reference_no}}</td>
		</tr>
		<tr>
			<td style="border: 1px solid #000; padding: 5px">Sale Reference</td>
			<td style="border: 1px solid #000; padding: 5px">{{$sale_reference_no}}</td>
		</tr>
	</tbody>
</table>

<table style="border-collapse: collapse; width: 100%;">
	<thead>
		<th style="border: 1px solid #000; padding: 5px">No</th>
		<th style="border: 1px solid #000; padding: 5px">Code</th>
		<th style="border: 1px solid #000; padding: 5px">Description</th>
		<th style="border: 1px solid #000; padding: 5px">Qty</th>
	</thead>
	<tbody>
		@foreach($codes as $key=>$code)
		<tr>
			<td style="border: 1px solid #000; padding: 5px">{{$key+1}}</td>
			<td style="border: 1px solid #000; padding: 5px">{{$code}}</td>
			<td style="border: 1px solid #000; padding: 5px">{{$name[$key]}}</td>
			<td style="border: 1px solid #000; padding: 5px">{{$qty[$key]}}</td>
		</tr>
		@endforeach
	</tbody>
</table>

<p>Prepared By: {{$prepared_by}}</p>
<p>Delivered By: {{$delivered_by}}</p>
<p>Recieved By: {{$recieved_by}}</p>