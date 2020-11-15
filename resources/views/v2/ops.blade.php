<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">Danh sách hóa đơn</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <table class="table table-bordered">
            <tbody><tr>
                <th style="width: 10px">#</th>
                <th>ProductName</th>
                <th>Customer</th>
                <th>Total</th>
                <th>TotalPayment</th>
                <th>Status</th>
                <th>Shipper</th>
                <th>Action</th>
            </tr>
            @foreach($invoices as $invoice)
            <tr>
                <td><img src="{{ $invoice->product->images[0] ?? url('no_image.jpg') }}" height="50px" alt=""></td>
                <td>{{ $invoice->product->fullName }}</td>
                <td>Người đặt
                    <address>
                        <strong>{{ $invoice->invoice->customerName }}</strong><br>
                        SDT: {{ $invoice->invoice->customer->contactNumber ?? '' }}
                    </address>
                    Người nhận
                    <address>
                        <?php $value = $invoice->invoice->invoiceDelivery ?? [] ?>
                        <strong>{{ $value['receiver'] ?? '' }}</strong><br>
                        {{ $value['address'] ?? '' }}, {{ $value['wardName'] ?? '' }}, {{ $value['locationName'] ?? '' }}
                    </address></td>
                <td>{{ $invoice->invoice->total }}</td>
                <td>{{ $invoice->invoice->totalPayment }}</td>
                <td>{{ $invoice->status_text }} <br>
                    <div class="progress">
                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="{{ $invoice->status_progress }}"
                             aria-valuemin="0" aria-valuemax="100" style="width:{{ $invoice->status_progress }}%">
                            {{ round($invoice->status_progress) }}%
                        </div>
                    </div>
                </td>
                <td>{{ $invoice->shipper->name }}</td>
                <td><a href="{{ admin_url('invoice-details/'.$invoice->_id.'/edit') }}"><i class="fa fa-edit"></i> Edit</a></td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.box-body -->
    <div class="box-footer clearfix">
        {{ $invoices->appends(request()->all())->links() }}
    </div>
</div>
