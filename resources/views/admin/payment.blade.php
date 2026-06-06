@extends('layouts.admin')

@section('content')

<div class="page-section active">

<div class="page-header">
    <div>
        <div class="page-title">Payment</div>
        <div class="page-sub">
            Data pembayaran pasien
        </div>
    </div>
</div>


<div class="card">

<table class="table">

<thead>
<tr>
    <th>No</th>
    <th>No Invoice</th>
    <th>Pasien</th>
    <th>Jenis</th>
    <th>Total</th>
    <th>Status</th>
    <th>Tanggal</th>
</tr>
</thead>


<tbody>

@foreach($payments as $p)

<tr>

<td>
{{ $loop->iteration }}
</td>

<td>
{{ $p->no_invoice }}
</td>


<td>
{{ $p->nama }}
</td>


<td>
{{ $p->jenis }}
</td>


<td>
Rp {{ number_format($p->total_tagihan,0,',','.') }}
</td>


<td>
{{ $p->status }}
</td>


<td>
{{ $p->created_at->format('d-m-Y') }}
</td>


</tr>

@endforeach


</tbody>

</table>


</div>

</div>

@endsection