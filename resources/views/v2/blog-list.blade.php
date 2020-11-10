<?php $page = "blog-list"; ?>
@extends('v2.layout.mainlayout')
@section('content')
    <!-- Page Content -->
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-md-12" style="margin-bottom: 100px">
                    @foreach($invoices as $invoice)
                    <div class="blog">
                        <div class="blog-image" data-toggle="modal" data-target="#myModal{{$invoice->_id}}">
                            <a href="#{{$invoice->_id}}" class="booking-doc-img"><img class="lazy img-responsive" style="border-radius: 4px;height: 190px;object-fit: cover;width: 150px;margin: 0 auto;" src="{{ $invoice->product->images[0] ?? url('no_image.jpg') }}" alt="Post Image"></a>
                        </div>
                        <h3 class="blog-title"><a href="#"><i class="far fa-clock"></i> {{ Carbon\Carbon::parse($invoice->created_at)->diffForHumans() }}</a>
                        </h3>
                        <div class="blog-info clearfix">
                            <div class="post-left">
                                <ul>
                                    <li>
                                        <div class="post-author">
                                            <span>{{ $invoice->product->fullName }}</span>
                                        </div>
                                    </li>
                                    <li><i class="fa fa-list"></i>{{ $invoice->invoice->code }} | SL: {{ $invoice->quantity }}</li>
                                    <li><i class="fa fa-user"></i>Sale: {{ $invoice->invoice->soldByName }}</li>
                                    <li><i class="far fa-comments"></i>{{ $invoice->status_text }}</li>
                                    <li><i class="fa fa-user"></i>Florist: {{ $invoice->florist->name }}</li>
                                </ul>
                            </div>
                        </div>
                        <div class="blog-content">
                            <p id="note" data-type="text" data-pk="{{ $invoice->_id }}" data-url="/florist">{{ $invoice->note }}</p>
                            <p id="opsNote" data-type="text" data-pk="{{ $invoice->_id }}" data-url="/florist">{{ $invoice->opsNote }}</p>
                            <div class="table-action">
                                @if(empty($invoice->opsFlorist))
                                @if ($invoice->opsFlorist != \Admin::user()->id)
                                    <button class="btn btn-sm bg-success-light" href="#" id="pick" data-type="text" data-pk="{{ $invoice->_id }}" value="{{ $invoice->_id }}" data-title="Select sex"><i class="fas fa-check"></i> Nhận đơn</button>
                                @endif

                                <a href="#{{$invoice->_id}}" id="sex" class="pick-user" data-type="select" data-pk="{{ $invoice->_id }}" data-url="/pick"><i class="fas fa-user"></i> Giao đơn</a>
                                @endif
                            </div>
                        </div>
                    </div>
                        <!-- Modal -->
                        <div id="myModal{{ $invoice->_id }}" class="modal fade" role="dialog">
                            <div class="modal-dialog modal-lg">

                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">{{ $invoice->product->fullName }}</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <div id="myCarousel" class="carousel slide" data-ride="carousel">
                                            <!-- Indicators -->
                                            <ol class="carousel-indicators">
                                                @if (is_array($invoice->product->images))
                                                    @foreach($invoice->product->images as $key => $image)
                                                        <li data-target="#myCarousel" data-slide-to="{{$key}}" class="{{ $key == 0 ? 'active' : '' }}"></li>
                                                    @endforeach
                                                @endif
                                            </ol>

                                            <!-- Wrapper for slides -->
                                            <div class="carousel-inner">
                                                @if (is_array($invoice->product->images))
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
                    @endforeach
                </div>
            </div>
        </div>

    </div>
    <!-- /Page Content -->
    </div>
@endsection
