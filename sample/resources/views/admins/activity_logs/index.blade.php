@section('contentTitle')
    <h3>Activity Logs</h3>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Activity Log History</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            {{-- Filters --}}
            <form method="GET" action="{{ route('activity_logs_new.index') }}" class="mb-3">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="topic">Topic</label>
                            <select name="topic" id="topic" class="form-control">
                                <option value="">All Topics</option>
                                @foreach($topics as $topic)
                                    <option value="{{ $topic }}" {{ request('topic') == $topic ? 'selected' : '' }}>
                                        {{ ucfirst($topic) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="year">Year</label>
                            <input type="text" name="year" id="year" class="form-control" 
                                   placeholder="YYYY" value="{{ request('year') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="month">Month</label>
                            <input type="text" name="month" id="month" class="form-control" 
                                   placeholder="MM" value="{{ request('month') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search">Search</label>
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="Search in logs..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i> Filter
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Results count --}}
            <div class="mb-3">
                <strong>Total Records:</strong> {{ $activityLogs->total() }}
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date & Time</th>
                            <th>Operator</th>
                            <th>Topic</th>
                            <th>Activity</th>
                            <th>Customer</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activityLogs as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    @if($log->operator)
                                        {{ $log->operator->name ?? 'N/A' }}
                                        <small class="text-muted d-block">{{ $log->operator->readable_role ?? '' }}</small>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($log->topic)
                                        <span class="badge badge-info">{{ ucfirst($log->topic) }}</span>
                                    @else
                                        <span class="badge badge-secondary">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ Str::limit($log->log, 100) }}</small>
                                </td>
                                <td>
                                    @if($log->customer)
                                        {{ $log->customer->username ?? 'N/A' }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No activity logs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $activityLogs->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection

@section('pageJs')
@endsection

