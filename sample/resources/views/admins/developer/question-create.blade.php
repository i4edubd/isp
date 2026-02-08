@extends ('laraview.layouts.sideNavLayout')

@section('title')
Question Create
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '11';
$active_link = '0';
@endphp
@endsection

@section('sidebar')
@include('admins.developer.sidebar')
@endsection


@section('contentTitle')
<h3>New Question</h3>
@endsection

@section('content')

<div class="card">

    <p class="text-danger">* required field</p>

    <form id="quickForm" method="POST" action="{{ route('questions.store') }}">

        @csrf

        <div class="card-body">

            <!--type-->
            <div class="form-group">
                <label for="type"><span class="text-danger">*</span>Type</label>
                <select class="form-control" id="type" name="type" required>
                    <option value="single_select">single_select</option>
                    <option value="multi_select">multi_select</option>
                    <option value="text">text</option>
                </select>
            </div>
            <!--/type-->

            <!--lang-->
            <div class="form-group">
                <label for="lang"><span class="text-danger">*</span>Lang</label>
                <select class="form-control" id="lang" name="lang" required>
                    <option value="eng">eng</option>
                    <option value="bn">bn</option>
                </select>
            </div>
            <!--/lang-->

            <!--question-->
            <div class="form-group">
                <label for="question"><span class="text-danger">*</span>Question</label>
                <input name="question" type="text" class="form-control @error('question') is-invalid @enderror"
                    id="question" value="{{ old('question') }}" required>
            </div>
            <!--/question-->

        </div>
        <!--/card-body-->

        <div class="card-footer">
            <button type="submit" class="btn btn-dark">Submit</button>
        </div>
        <!--/card-footer-->

    </form>

</div>

@endsection

@section('pageJs')
@endsection
