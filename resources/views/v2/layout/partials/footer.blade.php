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
                                    <option value="all">Time</option>
                                    <option value="today" {{ request('time') && request('time') == 'today' ? 'selected' : '' }}>Hôm nay</option>
                                    <option value="tomorrow" {{ request('time') && request('time') == 'tomorrow' ? 'selected' : '' }}>Ngày mai</option>
                                    <option value="me" {{ request('time') && request('time') == 'me' ? 'selected' : '' }}>Của tôi</option>
                                </select>
                                <select class="form-control" name="status">
                                    <option value="all">Status</option>
                                    <option value="1" {{ request('status') && request('status') == '1' ? 'selected' : '' }}>Chưa làm</option>
                                    <option value="2" {{ request('status') && request('status') == '2' ? 'selected' : '' }}>Đang làm</option>
                                    <option value="3" {{ request('status') && request('status') == '3' ? 'selected' : '' }}>Đã xong</option>
                                </select>
                                <input type="text" placeholder="Search..." class="form-control" name="q" value="{{ request('q') }}">
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
