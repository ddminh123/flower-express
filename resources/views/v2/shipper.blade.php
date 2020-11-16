<?php $page = "appointments"; ?>
@extends('v2.layout.mainlayout')
@section('content')
    <!-- Breadcrumb -->

    <!-- Page Content -->
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-md-12">
                    <div class="appointments">
                    @foreach($invoices as $invoice)
                        <!-- Appointment List -->
                            <div class="appointment-list">
                                <div class="profile-info-widget">
                                    <a href="#{{ $invoice->_id }}" class="booking-doc-img">
                                        <img src="{{ $invoice->product->images[0] ?? url('no_image.jpg') }}"
                                             alt="User Image">
                                    </a>
                                    <div class="profile-det-info">
                                        <h3><a href="#{{ $invoice->_id }}">{{ $invoice->product->fullName ?? '' }}</a>
                                        </h3>
                                        <div class="patient-details">
                                            <h5><i class="far fa-clock"></i> {{ $invoice->invoice->expectedDelivery }}
                                            </h5>
                                            <h5><i class="fas fa-map-marker-alt"></i>
                                                {{ $invoice->invoice->invoiceDelivery['address'] ?? '' }}
                                                {{ $invoice->invoice->invoiceDelivery['wardName'] ?? '' }}
                                                {{ $invoice->invoice->invoiceDelivery['locationName'] ?? '' }}
                                            </h5>
                                            <h5>
                                                <i class="fas fa-envelope"></i> {{ $invoice->invoice->invoiceDelivery['receiver'] ?? '' }}
                                            </h5>
                                            <h5 class="mb-0"><i
                                                    class="fas fa-phone"></i> {{ $invoice->invoice->invoiceDelivery['contactNumber'] ?? '' }}
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="appointment-action-{{$invoice->_id}}" title="{{ $invoice->opsStatus }}">
                                    @if ($invoice->opsStatus == \App\InvoiceEnum::STATUS_SHIPPER_PICKED)
                                            <button class="btn btn-sm bg-success-light"
                                                    onclick="getLocation('{{$invoice->_id}}', '{{ \App\InvoiceEnum::STATUS_SHIPPER_DONE }}')">
                                                <i class="fas fa-check"></i> Giao thành công
                                            </button>
                                    @else
                                        @if ($invoice->opsStatus != \App\InvoiceEnum::STATUS_SHIPPER_DONE)
                                        <button class="btn btn-sm bg-primary-light shipper-start-{{$invoice->_id}}"
                                                onclick="getLocation('{{$invoice->_id}}', '{{\App\InvoiceEnum::STATUS_SHIPPER_PICKED}}')">
                                            <i class="fas fa-check"></i> Bắt đầu giao
                                        </button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                    @endforeach
                    <!-- /Appointment List -->
                    </div>
                </div>
            </div>

        </div>
        <a href="{{ admin_url('auth/logout') }}">Logout</a>
    </div>
@endsection

<script type="text/javascript">
    var ivdId = 0;
    var status = '{{\App\InvoiceEnum::STATUS_SHIPPER_PICKED}}'

    function getLocation(invoiceDetailId, invoiceStatus) {
        ivdId = invoiceDetailId
        status = invoiceStatus
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, showError);
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }

    function showPosition(position) {
        let lat = position.coords.latitude;
        let lon = position.coords.longitude

        $.ajax({
            type: "POST",
            url: '/shipper/' + ivdId,
            dataType: 'json',
            data: {
                lat: lat,
                lon: lon,
                s: status
            },
            success: function (res) {
                if (res.opsStatus === 5) {
                    var html = '<button class="btn btn-sm bg-success-light" onclick="getLocation(' + ivdId + ', ' + status + ')">\n' +
                        '                                            <i class="fas fa-check"></i> Giao thành công\n' +
                        '                                        </button>'
                    $('.shipper-start-' + ivdId).hide()
                    $('.appointment-action-' + ivdId).append(html)
                } else {

                }
            },
        });
    }

    function showError(error) {
        switch (error.code) {
            case error.PERMISSION_DENIED:
                alert("User denied the request for Geolocation.")
                break;
            case error.POSITION_UNAVAILABLE:
                alert("Location information is unavailable.")
                break;
            case error.TIMEOUT:
                alert("The request to get user location timed out.")
                break;
            case error.UNKNOWN_ERROR:
                alert("An unknown error occurred.")
                break;
        }
    }
</script>
