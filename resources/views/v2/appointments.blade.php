<?php $page="appointments";?>
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
										<a href="patient-profile" class="booking-doc-img">
											<img src="{{ $invoice->product->images[0] }}" alt="User Image">
										</a>
										<div class="profile-det-info">
											<h3><a href="patient-profile">{{ $invoice->product->fullName ?? '' }}</a></h3>
											<div class="patient-details">
												<h5><i class="far fa-clock"></i> 14 Nov 2019, 10.00 AM</h5>
												<h5><i class="fas fa-map-marker-alt"></i>
                                                    {{ $invoice->invoice->invoiceDelivery['address'] ?? '' }}
                                                    {{ $invoice->invoice->invoiceDelivery['wardName'] ?? '' }}
                                                    {{ $invoice->invoice->invoiceDelivery['locationName'] ?? '' }}
                                                </h5>
												<h5><i class="fas fa-envelope"></i> {{ $invoice->invoice->invoiceDelivery['receiver'] ?? '' }}</h5>
												<h5 class="mb-0"><i class="fas fa-phone"></i> {{ $invoice->invoice->invoiceDelivery['contactNumber'] ?? '' }}</h5>
											</div>
										</div>
									</div>
									<div class="appointment-action">
										<a href="#" class="btn btn-sm bg-success-light" data-toggle="modal" data-target="#appt_details">
                                            <i class="fas fa-check"></i> Giao thành công
										</a>
									</div>
								</div>
                                @endforeach
								<!-- /Appointment List -->
							</div>
						</div>
					</div>

				</div>

			</div>
            <!-- /Page Content -->
		<!-- Appointment Details Modal -->
		<div class="modal fade custom-modal" id="appt_details">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Thông tin vị trí</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<ul class="info-details">
							<li>
								<div class="details-header">
									<div class="row">
										<div class="col-md-6">
											<span class="title">#APT0001</span>
											<span class="text">21 Oct 2019 10:00 AM</span>
										</div>
										<div class="col-md-6">
											<div class="text-right">
												<button type="button" class="btn bg-success-light btn-sm" id="topup_status">Completed</button>
											</div>
										</div>
									</div>
								</div>
							</li>
							<li>
								<span class="title">Status:</span>
								<span class="text">Completed</span>
							</li>
							<li>
								<span class="title">Confirm Date:</span>
								<span class="text">29 Jun 2019</span>
							</li>
							<li>
								<span class="title">Paid Amount</span>
								<span class="text">$450</span>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<!-- /Appointment Details Modal -->
@endsection
