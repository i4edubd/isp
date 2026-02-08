<div class="row">

    {{-- comments --}}

    <div class="col-md-6">

        <!-- The time line -->
        <div class="timeline">

            <!-- timeline time label -->
            <div class="time-label">
                <span class="bg-red">{{ $customer_complain->start_time }}</span>
            </div>
            <!-- /timeline-label -->

            <!-- received complaint -->
            <div>
                <i class="fas fa-envelope bg-blue"></i>

                <div class="timeline-item">

                    <span class="time">
                        <i class="fas fa-clock"></i>
                        {{ $customer_complain->start_time }}
                    </span>

                    <h3 class="timeline-header">
                        @if ($customer_complain->requester == "customer")
                        {{ $customer_complain->username }} has complained.
                        @else
                        {{ $customer_complain->receiver->name }} has received the complaint.
                        @endif
                    </h3>

                    <div class="timeline-body">
                        {{ $customer_complain->message }}
                    </div>

                </div>
            </div>
            <!-- received complaint -->

            {{-- complaint comments --}}
            @foreach ($complain_comments as $comment)

            <div>
                <i class="fas fa-comments bg-yellow"></i>

                <div class="timeline-item">

                    <span class="time">
                        <i class="fas fa-clock"></i>
                        {{ $comment->comment_time }}
                    </span>

                    <h3 class="timeline-header">
                        @if ($comment->operator_id == 0)
                        {{ $customer_complain->username }}
                        @else
                        {{ $comment->operator->name }}
                        @endif
                    </h3>

                    <div class="timeline-body">
                        {{ $comment->comment }}
                    </div>

                </div>

            </div>

            @endforeach
            {{-- complaint comments --}}

            <!-- new comment -->
            @if ($customer_complain->is_active == 1)
            <div>
                <i class="fas fa-plus bg-maroon"></i>

                <div class="timeline-item">

                    <h3 class="timeline-header">
                        Add new comment
                    </h3>

                    @if (Auth::user())
                    {{-- comment by operator --}}
                    <form method="POST"
                        action="{{ route('customer_complains.complain_comments.store', ['customer_complain' => $customer_complain->id]) }}">
                        @csrf

                        <div class="timeline-body">

                            <div class="form-group">
                                <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                            </div>

                        </div>

                        <div class="timeline-footer">
                            {{-- done --}}
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="done" value="yes"
                                    id="defaultCheck1">
                                <label class="form-check-label" for="defaultCheck1">
                                    The Complaint is solved.
                                </label>
                            </div>
                            {{-- done --}}
                            <button type="submit" class="btn btn-dark">SUBMIT</button>
                        </div>

                    </form>
                    {{-- comment by operator --}}
                    @else
                    {{-- comment by customer --}}
                    <form method="POST"
                        action="{{ route('complaints-customer-interface.update', ['customer_complain' => $customer_complain->id]) }}">
                        @method('put')
                        @csrf

                        <div class="timeline-body">

                            <div class="form-group">
                                <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                            </div>

                        </div>

                        <div class="timeline-footer">
                            {{-- done --}}
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="done" value="yes"
                                    id="defaultCheck1">
                                <label class="form-check-label" for="defaultCheck1">
                                    The Complaint is solved.
                                </label>
                            </div>
                            {{-- done --}}
                            <button type="submit" class="btn btn-dark">SUBMIT</button>
                        </div>

                    </form>
                    {{-- comment by customer --}}
                    @endif

                </div>

            </div>

            @endif
            <!-- new comment -->

            <div>
                <i class="fas fa-clock bg-gray"></i>
            </div>

        </div>

    </div>

    {{-- comments --}}

    {{-- logs --}}
    <div class="col-md-6">

        <!-- The time line -->
        <div class="timeline">

            <!-- timeline item -->
            @foreach ($complain_ledgers as $ledger)
            <div>
                <i class="fas fa-history bg-blue"></i>

                <div class="timeline-item">

                    <span class="time">
                        <i class="fas fa-clock"></i>
                        {{ $ledger->start_time }}
                    </span>

                    <h3 class="timeline-header">
                        {{ $ledger->comment }}
                        <div class="progress">
                            <div class="progress-bar bg-maroon" role="progressbar" style="width: 25%;"
                                aria-valuenow="{{ round(($ledger->diff_in_seconds * 100)/$customer_complain->elapsed_time) }}"
                                aria-valuemin="0" aria-valuemax="100">
                                {{ round(($ledger->diff_in_seconds * 100)/$customer_complain->elapsed_time) }}%
                            </div>
                        </div>
                    </h3>


                    <div class="timeline-body">

                        @if ($ledger->topic == "category")
                        {{ $ledger->operator->name }} has changed the category from
                        {{ $ledger->fromCategory->name }} to {{ $ledger->toCategory->name }}
                        @endif

                        @if ($ledger->topic == "department")
                        {{ $ledger->operator->name }} has changed the department from
                        {{ $ledger->fromDepartment->name }} to {{ $ledger->toDepartment->name }}
                        @endif

                        @if ($ledger->topic == "acknowledge")
                        {{ $ledger->operator->name }} has acknowledged the complaint.
                        @endif

                        @if ($ledger->topic == "done")
                        @if ($ledger->operator_id == 0)
                        {{ $customer_complain->username }} has marked the complaint as done!
                        @else
                        {{ $ledger->operator->name }} has marked the complaint as done!
                        @endif
                        @endif

                        @if ($ledger->topic == "comment")
                        @if ($ledger->operator_id == 0)
                        {{ $customer_complain->username }} has commented on the complaint!
                        @else
                        {{ $ledger->operator->name }} has commented on the complaint!
                        @endif
                        @endif

                    </div>

                </div>
            </div>
            @endforeach
            <!-- END timeline item -->

            <div>
                <i class="fas fa-clock bg-gray"></i>
            </div>

        </div>
        <!-- The time line -->

    </div>
    {{-- logs --}}

</div>
