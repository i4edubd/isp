@section('contentTitle')
<h3>Bulk update users</h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <!-- Timelime -->
        <div class="row">

            <div class="col-md-12">

                <!-- The time line -->
                <div class="timeline">

                    <!-- timeline item -->
                    <div>

                        <span class="badge badge-danger">1</span> <i class="fas fa-download"></i>

                        <div class="timeline-item">

                            <h3 class="timeline-header">
                                <a class="btn btn-link" href="{{ route('download-users-uploadable.create') }}"
                                    role="button"> <i class="fas fa-download"></i> Download Excel file</a>
                            </h3>

                        </div>

                    </div>
                    <!-- END timeline item -->

                    <!-- timeline item -->
                    <div>

                        <span class="badge badge-danger">2</span> <i class="fas fa-edit"></i>

                        <div class="timeline-item">
                            <h3 class="timeline-header no-border">
                                Edit user info in Excel
                            </h3>

                            <div class="timeline-body">

                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item text-danger">Please do not edit id and username</li>
                                    <li class="list-group-item text-danger">Please do not edit the Excel file header
                                    </li>
                                </ul>

                            </div>

                        </div>

                    </div>
                    <!-- END timeline item -->

                    <!-- timeline item -->
                    <div>

                        <span class="badge badge-danger">3</span> <i class="fas fa-upload"></i>

                        <div class="timeline-item">

                            <h3 class="timeline-header">
                                Upload Excel file
                            </h3>

                            <div class="timeline-footer">

                                <form method="POST" action="{{ route('bulk-update-users.store') }}"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <!--update_info-->
                                    <div class="form-group col-md-6">
                                        <div class="custom-file">
                                            <input type="file" name="update_info" class="custom-file-input"
                                                id="update_info" required>
                                            <label class="custom-file-label" for="update_info">Choose file</label>
                                        </div>
                                    </div>
                                    <!--/update_info-->

                                    <button type="submit" class="btn btn-dark">Submit</button>

                                </form>

                            </div>

                        </div>

                    </div>
                    <!-- END timeline item -->

                </div>
                <!-- /.timeline -->

            </div>
            <!-- /.col -->

        </div>

    </div>
    <!--/card-body-->

</div>

@endsection

@section('pageJs')

<script type="text/javascript">
    $(document).ready(function () {
      bsCustomFileInput.init();
    });
</script>

@endsection