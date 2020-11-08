<?php $page="doctor-dashboard";?>
@extends('v2.layout.mainlayout')
@section('content')
    <!-- Page Content -->
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="appointment-tab">

                                <!-- Appointment Tab -->
                                <ul class="nav nav-tabs nav-tabs-solid nav-tabs-rounded">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#today" data-toggle="tab">Hôm nay</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#tomorrow" data-toggle="tab">Ngày mai</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#me" data-toggle="tab">Của tôi</a>
                                    </li>
                                </ul>
                                <!-- /Appointment Tab -->

                                <div class="tab-content">

                                    <div class="tab-pane show active" id="today">
                                        <div class="card card-table mb-0">
                                            <div class="card-body">
                                                @include('v2.invoice-florist', ['invoices' => $invoiceTodays])
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane show" id="tomorrow">
                                        <div class="card card-table mb-0">
                                            <div class="card-body">
                                                @include('v2.invoice-florist', ['invoices' => $invoiceTomorrows])
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane show" id="me">
                                        <div class="card card-table mb-0">
                                            <div class="card-body">
                                                @include('v2.invoice-florist', ['invoices' => $invoiceMes])
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>
    <!-- /Page Content -->
</div>
@endsection

