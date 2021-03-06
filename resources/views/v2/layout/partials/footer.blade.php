<!-- Footer -->
<footer class="footer fixed-bottom">
    <!-- Footer Bottom -->
    <div class="footer-bottom">
        <div class="container-fluid">

            <!-- Copyright -->
            <div class="copyright">
                <div class="row">
                    <div class="col-md-12">
                        <form class="search-form" id="frmFlorist">
                            <div class="input-group">
                                <select class="form-control" name="time">
                                    <option value="today" {{ request('time') && request('time') == 'today' ? 'selected' : '' }}>Hôm nay</option>
                                    <option value="tomorrow" {{ request('time') && request('time') == 'tomorrow' ? 'selected' : '' }}>Ngày mai</option>
                                    <option value="me" {{ request('time') && request('time') == 'me' ? 'selected' : '' }}>Của tôi</option>
                                </select>
                                <select class="form-control" name="status">
                                    <option value="all">Tất cả</option>
                                    @if(request()->path() == 'admin/florist')
                                        @foreach(\App\InvoiceEnum::getStatusFlorist() as $status => $value)
                                            <option value="{{$status}}" {{ request('status') && request('status') == $status ? 'selected' : '' }}>
                                                {{$value}}</option>
                                        @endforeach
                                    @elseif(request()->path() == 'admin/shipper')
                                        @foreach(\App\InvoiceEnum::getStatusShip() as $status => $value)
                                            <option value="{{$status}}" {{ request('status') && request('status') == $status ? 'selected' : '' }}>
                                                {{$value}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <input type="date" placeholder="Search..." class="form-control" name="q" value="{{ request('q') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /Copyright -->

        </div>
    </div>
    <!-- /Footer Bottom -->

</footer>
<!-- /Footer -->
