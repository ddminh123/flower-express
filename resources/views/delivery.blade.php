<section class="invoice">
    <!-- info row -->
    <div class="row invoice-info">
        <div class="col-sm-12 invoice-col">
            Người đặt
            <address>
                <strong>{{ $model->invoice->customerName }}</strong><br>
                SDT: {{ $model->invoice->customer->contactNumber ?? '' }}
            </address>
            Người nhận
            <address>
                <strong>{{ $value['receiver'] ?? '' }}</strong><br>
                {{ $value['address'] ?? '' }}, {{ $value['wardName'] ?? '' }}, {{ $value['locationName'] ?? '' }}
            </address>
        </div>
    </div>
    <!-- /.row -->
</section>
