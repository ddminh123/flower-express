@extends('v2.layout.mainlayout')
@section('content')
{{--    <section class="section">--}}
{{--        <div class="container-fluid">--}}
{{--            <div class="banner-wrapper">--}}
{{--                <div class="banner-header text-center">--}}
{{--                    <h1>Search Doctor, Make an Appointment</h1>--}}
{{--                    <p><a href="{{ admin_url('auth/logout') }}">Logout</a></p>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </section>--}}
{{--    <!-- Popular Section -->--}}
    <section class="section">
        <div class="">
            <a href="{{ admin_url('auth/logout') }}">Logout</a> |
            Total {{ $total }}
            <div class="row">
                <div class="col-md-12">
                    <div class="doctor-slider slider">
                        @foreach($invoices as $invoiceDetail)
                                <div class="profile-widget">
                            <div class="doc-img" data-toggle="modal" data-target="#myModal{{$invoiceDetail->_id}}">
                                <a href="#{{$invoiceDetail->_id}}">
                                    <img class="img-fluid lazy img-responsive" alt="User Image" src="{{ $invoiceDetail->product->images[0] ?? url('no_image.jpg') }}">
                                </a>
                            </div>
                                    <div id="myModal{{ $invoiceDetail->_id }}" class="modal fade" role="dialog">
                                        <div class="modal-dialog modal-lg">

                                            <!-- Modal content-->
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">{{ $invoiceDetail->product->fullName ?? '' }}</h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                    <div id="myCarousel" class="carousel slide" data-ride="carousel">
                                                        <!-- Indicators -->
                                                        <ol class="carousel-indicators">
                                                            @if (isset($invoiceDetail->product->images) && is_array($invoiceDetail->product->images))
                                                                @foreach($invoiceDetail->product->images as $key => $image)
                                                                    <li data-target="#myCarousel" data-slide-to="{{$key}}" class="{{ $key == 0 ? 'active' : '' }}"></li>
                                                                @endforeach
                                                            @endif
                                                        </ol>

                                                        <!-- Wrapper for slides -->
                                                        <div class="carousel-inner">
                                                            @if (isset($invoiceDetail->product->images) && is_array($invoiceDetail->product->images))
                                                                @foreach($invoiceDetail->product->images as $key => $image)
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
                                    <a href="#" title="Thời gian giao hàng"><i class="far fa-clock"></i> {{ !empty($invoiceDetail->invoice->expectedDelivery) ? Carbon\Carbon::parse($invoiceDetail->invoice->expectedDelivery)->format('d/m/Y H:i:s') : $invoiceDetail->invoice->purchaseDate ?? '' }}</a>
                                    @if ($invoiceDetail->opsStatus == 2)
                                        <i class="fas fa-check-circle verified"></i>
                                    @endif
                                </h3>
                                <p class="speciality">{{ $invoiceDetail->invoice->code }} | SL: {{ $invoiceDetail->quantity }} | {{ $invoiceDetail->product->fullName ?? '' }}</p>
                                <div>
                                    <p id="note" data-type="text" data-pk="{{ $invoiceDetail->_id }}" data-url="/florist">{{ $invoiceDetail->note }}</p>
                                    <p id="opsNote" data-type="text" data-pk="{{ $invoiceDetail->_id }}" data-url="/florist">{{ $invoiceDetail->opsNote }}</p>
                                </div>
                                <ul class="available-info">
                                    <li>
                                        <i class="fas fa-user"></i> Sale: {{ $invoiceDetail->invoice->soldByName ?? '' }}
                                    </li>
                                    <li>
                                        <i class="fas fa-users"></i> Florist: <span class="florist-{{$invoiceDetail->_id}}">{{ $invoiceDetail->florist->name ?? '' }}</span>
                                    </li>
                                    <li>
                                        <i class="far fa-clock"></i> Status: <span class="status-{{$invoiceDetail->_id}}">{{ $invoiceDetail->status_text }}</span>
                                    </li>
                                    <li>
                                        <i class="far fa-clock"></i> {{ !empty($invoiceDetail->invoice->purchaseDate) ? Carbon\Carbon::parse($invoiceDetail->invoice->purchaseDate)->format('d/m/Y H:i:s') : now()->format('d/m/Y H:i:s') }}
                                    </li>
                                    <li>
                                        <i class="far fa-money-bill-alt"></i> Total: {{ number_format($invoiceDetail->invoice->total_florist) }}
                                    </li>
                                </ul>
                                <div class="row row-sm">
                                    @if ($invoiceDetail->opsFlorist != \Admin::user()->id)
                                    <div class="col-6 pick-el-{{$invoiceDetail->_id}}">
                                        <a href="#{{$invoiceDetail->_id}}" class="btn book-btn pick" data-value="{{ $invoiceDetail->_id }}">Nhận đơn</a>
                                    </div>
                                    @elseif ($invoiceDetail->opsStatus == \App\InvoiceEnum::STATUS_FLORIS_PICKED)

                                        <div class="col-6 pick-el-{{$invoiceDetail->_id}} success-el-{{$invoiceDetail->_id}}">
                                            <a href="#{{$invoiceDetail->_id}}" class="btn book-btn pick" data-value="{{ $invoiceDetail->_id }}">Hoàn thành</a>
                                        </div>
                                    @endif
                                    <div class="col-6">
                                        <a href="#{{$invoiceDetail->_id}}" id="sex" class="btn view-btn pick-user" data-type="select" data-pk="{{ $invoiceDetail->_id }}" data-url="/pick" data-title="Select sex">Giao đơn</a>
                                    </div>
                                </div>
                            </div>
                        </div>
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
