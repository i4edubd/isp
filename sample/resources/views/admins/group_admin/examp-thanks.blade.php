@extends ('laraview.layouts.sideNavLayout')

@section('title')
Questions & Answers
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '12';
$active_link = '4';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection


@section('contentTitle')
<h3>Questions & Answers</h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <p> we would like to thank you for attending the exam. </p>
        <p> Your Score: 100 out of 100 </p>
        <p> We hope you had fun, and we look forward to seeing you as a active user of the software. </p>

    </div>

</div>

@endsection

@section('pageJs')
@endsection
