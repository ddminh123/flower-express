<?php $page = "blog-list"; ?>
@extends('v2.layout.mainlayout')
@section('content')
    <!-- Page Content -->
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    @foreach($invoices as $invoice)
                    <div class="blog">
                        <div class="blog-image">
                            <a href="#" class="booking-doc-img"><img class="img-responsive" style="border-radius: 4px;
    height: 190px;
    object-fit: cover;
    width: 150px;
    margin: 0 auto;" src="{{ $invoice->product->images[0] ?? url('no_image.jpg') }}" alt="Post Image"></a>
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
                                @if ($invoice->opsFlorist != \Admin::user()->id)
                                    <button class="btn btn-sm bg-success-light" href="#" id="pick" data-type="text" data-pk="{{ $invoice->_id }}" value="{{ $invoice->_id }}" data-title="Select sex"><i class="fas fa-check"></i> Nhận đơn</button>
                                @endif

                                <a href="#" id="sex" data-type="select" data-pk="{{ $invoice->_id }}" data-url="/pick"><i class="fas fa-user"></i> Giao đơn</a>
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
