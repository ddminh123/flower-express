<div class="box">
    <div class="box-header with-border">
        <form role="form">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Search</label>
                            <input type="text" class="form-control" name="q" placeholder="invoice code">
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control select2" name="status">
                                <option value=""></option>
                                @foreach(\App\InvoiceEnum::getStatus() as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Shipper</label>
                            <select class="form-control select2" name="shipper">
                                <option value=""></option>
                                @foreach($shippers as $shipper)
                                    <option value="{{ $shipper->id }}">{{ $shipper->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- /.form-group -->
                    </div>
                    <!-- /.col -->
                </div>
            </div>
            <!-- /.box-body -->

            <div class="box-footer">
                <div class="pull-right">
                    <a href="{{admin_url('invoice-details')}}" class="btn btn-default">Clear</a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <table class="table table-bordered">
            <tbody><tr>
                <th style="width: 10px">#</th>
                <th>Invoice</th>
                <th>ProductName</th>
                <th>Customer</th>
                <th>Note</th>
                <th>Total</th>
                <th>TotalPayment</th>
                <th>Status</th>
                <th>Shipper</th>
                <th>Action</th>
            </tr>
            @foreach($invoices as $invoice)
            <tr>
                <td><img src="{{ $invoice->product->images[0] ?? url('no_image.jpg') }}" height="50px" alt=""></td>
                <td>{{ $invoice->invoice->code }}</td>
                <td>{{ $invoice->product->code. ' - '.$invoice->product->fullName }}</td>
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
                <td>{{ $invoice->note }}</td>
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
