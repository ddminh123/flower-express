<div class="table-responsive">
    <table class="table table-hover table-center mb-0">
        <tbody>
        @foreach($invoices as $invoice)
            <tr>
                <td>
                    <h2 class="table-avatar">
                        <a href="#" class="avatar avatar-sm mr-2"><img class="avatar-img" src="{{ $invoice->product->images[0] ?? url('no_image.jpg') }}" alt="User Image"></a>
                        <a href="#">{{ $invoice->product->fullName }} <span>{{ $invoice->invoice->code }}</span></a>
                    </h2>
                </td>
                <td><label class="badge badge-info">{{ $invoice->florist->name }}</label> <span class="d-block text-info">{{ Carbon\Carbon::parse($invoice->created_at)->diffForHumans() }}</span></td>
                <td>{{ number_format($invoice->price) }}đ <span class="d-block text-danger">SL: {{ $invoice->quantity }}</span></td>
                <td>{{ $invoice->note }}</td>
                <td>{{ $invoice->status_text }}</td>
                <td class="text-right">
                    <div class="table-action">
                        @if ($invoice->opsFlorist != \Admin::user()->id)
                            <a href="javascript:void(0);" class="btn btn-sm bg-success-light">
                                <i class="fas fa-check"></i> Nhận đơn
                            </a>
                        @endif
                        <a href="javascript:void(0);" class="btn btn-sm bg-warning-light">
                            <i class="fas fa-user"></i> Giao đơn
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
