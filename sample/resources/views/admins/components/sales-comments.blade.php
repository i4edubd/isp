@section('contentTitle')
@endsection

@section('content')

<dl class="row">
    <dt class="col-sm-4">ID</dt>
    <dd class="col-sm-8">{{ $operator->id }}</dd>
</dl>

<dl class="row">
    <dt class="col-sm-4">Name</dt>
    <dd class="col-sm-8">{{ $operator->name }}</dd>
</dl>

<hr>

<dl class="row">
    <dt class="col-sm-4">Mobile</dt>
    <dd class="col-sm-8">{{ $operator->mobile }}</dd>
</dl>

<hr>

<dl class="row">
    <dt class="col-sm-4">Email</dt>
    <dd class="col-sm-8">{{ $operator->email }}</dd>
</dl>

<hr>

<dl class="row">
    <dt class="col-sm-4">Company Name</dt>
    <dd class="col-sm-8">{{ $operator->company }}</dd>
</dl>

<div class="card">

    <div class="card-body">

        <div class="row">

            <div class="col-md-12">

                <!-- The time line -->
                <div class="timeline">

                    <!-- timeline time label -->
                    <div class="time-label">
                        <span class="bg-red">{{ $operator->created_at }}</span>
                    </div>
                    <!-- /timeline-label -->

                    <!-- timeline item -->
                    @foreach ($comments as $comment)
                    <div>
                        <i class="fas fa-comments bg-yellow"></i>

                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i>{{ $comment->created_at }}</span>

                            <div class="timeline-body">
                                {{ $comment->comment }}
                            </div>

                        </div>

                    </div>
                    @endforeach
                    <!-- END timeline item -->

                    <div>
                        <i class="fas fa-clock bg-gray"></i>
                    </div>

                    {{-- New Comment --}}
                    <div>
                        <div class="timeline-item">
                            <form action="{{ route('operators.sales_comments.store', ['operator' => $operator->id]) }}"
                                method="POST">
                                @csrf

                                {{-- new_comment --}}
                                <div class="form-group">
                                    <label for="new_comment">New Comment</label>
                                    <textarea name="new_comment" class="form-control" id="new_comment" rows="3"
                                        required></textarea>
                                </div>
                                {{-- new_comment --}}
                                {{-- provisioning_status --}}
                                @if ($operator->provisioning_status == 2)
                                <div class="form-check">
                                    <input class="form-check-input" name="provisioned" type="checkbox"
                                        value="provisioned" id="provisioned" checked>
                                    <label class="form-check-label" for="provisioned">
                                        Customer has been provisioned
                                    </label>
                                </div>
                                @else
                                <div class="form-check">
                                    <input class="form-check-input" name="provisioned" type="checkbox"
                                        value="provisioned" id="provisioned">
                                    <label class="form-check-label" for="provisioned">
                                        Customer has been provisioned
                                    </label>
                                </div>
                                @endif
                                {{-- provisioning_status --}}
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                    {{-- New Comment --}}

                </div>
                <!-- /The time line -->

            </div>
            <!-- /col -->

        </div>
        {{-- /row --}}

        {{-- nav --}}
        <ul class="nav justify-content-end">
            <li class="nav-item">
                <a class="nav-link btn btn-danger"
                    href="{{ route('next-sales-comments', ['operator' =>$operator->id]) }}">
                    Next <i class="fas fa-arrow-right"></i></a>
            </li>
        </ul>
        {{-- nav --}}

    </div>
    <!-- /card-body -->

</div>

@endsection

@section('pageJs')
@endsection