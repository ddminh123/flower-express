@extends('v2.layout.mainlayout')
@section('content')
    <!-- Popular Section -->
    <section class="section">
        <div class="">
            <div class="row">
                <div class="col-md-12">
                    <div class="doctor-slider slider">
                        @foreach($invoices as $invoiceDetail)
                            @foreach($invoiceDetail->items as $invoice)
                                <div class="profile-widget">
                            <div class="doc-img" data-toggle="modal" data-target="#myModal{{$invoice->_id}}">
                                <a href="#{{$invoice->_id}}">
                                    <img class="img-fluid lazy img-responsive" alt="User Image" src="{{ $invoice->product->images[0] ?? url('no_image.jpg') }}">
                                </a>
                            </div>
                                    <div id="myModal{{ $invoice->_id }}" class="modal fade" role="dialog">
                                        <div class="modal-dialog modal-lg">

                                            <!-- Modal content-->
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">{{ $invoice->product->fullName ?? '' }}</h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                    <div id="myCarousel" class="carousel slide" data-ride="carousel">
                                                        <!-- Indicators -->
                                                        <ol class="carousel-indicators">
                                                            @if (isset($invoice->product->images) && is_array($invoice->product->images))
                                                                @foreach($invoice->product->images as $key => $image)
                                                                    <li data-target="#myCarousel" data-slide-to="{{$key}}" class="{{ $key == 0 ? 'active' : '' }}"></li>
                                                                @endforeach
                                                            @endif
                                                        </ol>

                                                        <!-- Wrapper for slides -->
                                                        <div class="carousel-inner">
                                                            @if (isset($invoice->product->images) && is_array($invoice->product->images))
                                                                @foreach($invoice->product->images as $key => $image)
                                                                    <div class="item {{ $key == 0 ? 'active' : '' }}">
                                                                        <img src="{{$image}}" alt="Los Angeles">
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        </div>

                                                        <!-- Left and right controls -->
                                                        <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                                                            <span class="glyphicon glyphicon-chevron-left"></span>
                                                            <span class="sr-only">Previous</span>
                                                        </a>
                                                        <a class="right carousel-control" href="#myCarousel" data-slide="next">
                                                            <span class="glyphicon glyphicon-chevron-right"></span>
                                                            <span class="sr-only">Next</span>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Thoát</button>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                            <div class="pro-content">
                                <h3 class="title">
                                    <a href="#" title="Thời gian giao hàng"><i class="far fa-clock"></i> {{ !empty($invoiceDetail->expectedDelivery) ? Carbon\Carbon::parse($invoiceDetail->expectedDelivery)->format('d/m/Y H:i:s') : $invoiceDetail->purchaseDate ?? '' }}</a>
                                    @if ($invoice->opsStatus == 2)
                                        <i class="fas fa-check-circle verified"></i>
                                    @endif
                                </h3>
                                <p class="speciality">{{ $invoice->invoice->code }} | SL: {{ $invoice->quantity }} | {{ $invoice->product->fullName ?? '' }}</p>
                                <div>
                                    <p id="note" data-type="text" data-pk="{{ $invoice->_id }}" data-url="/florist">{{ $invoice->note }}</p>
                                    <p id="opsNote" data-type="text" data-pk="{{ $invoice->_id }}" data-url="/florist">{{ $invoice->opsNote }}</p>
                                </div>
                                <ul class="available-info">
                                    <li>
                                        <i class="fas fa-user"></i> Sale: {{ $invoice->invoice->soldByName ?? '' }}
                                    </li>
                                    <li>
                                        <i class="fas fa-users"></i> Florist: <span class="florist-{{$invoice->_id}}">{{ $invoice->florist->name ?? '' }}</span>
                                    </li>
                                    <li>
                                        <i class="far fa-clock"></i> Status: <span class="status-{{$invoice->_id}}">{{ $invoice->status_text }}</span>
                                    </li>
                                    <li>
                                        <i class="far fa-clock"></i> {{ !empty($invoiceDetail->purchaseDate) ? Carbon\Carbon::parse($invoiceDetail->purchaseDate)->format('d/m/Y H:i:s') : now()->format('d/m/Y H:i:s') }}
                                    </li>
                                    <li>
                                        <i class="far fa-money-bill-alt"></i> Total: {{ number_format($invoice->invoice->total_florist) }}
                                    </li>
                                </ul>
                                <div class="row row-sm">
                                    @if ($invoice->opsFlorist != \Admin::user()->id)
                                    <div class="col-6">
                                        <a href="#{{$invoice->_id}}" class="btn book-btn pick" data-value="{{ $invoice->_id }}">
                                            <span class="pick-el-{{$invoice->_id}}">Nhận đơn</span>
                                        </a>
                                    </div>
                                    @else
                                        <div class="col-6 pick-el-{{$invoice->_id}}">
                                            <a href="#{{$invoice->_id}}" class="btn book-btn pick" data-value="{{ $invoice->_id }}">Hoàn thành</a>
                                        </div>
                                    @endif
                                    <div class="col-6">
                                        <a href="#{{$invoice->_id}}" id="sex" class="btn view-btn pick-user" data-type="select" data-pk="{{ $invoice->_id }}" data-url="/pick" data-title="Select sex">Giao đơn</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /Popular Section -->
    </div>
    <!-- /Main Wrapper -->
@endsection
