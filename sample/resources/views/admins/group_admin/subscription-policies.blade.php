@extends ('laraview.layouts.sideNavLayout')

@section('title')
subscription policies
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '9';
$active_link = '3';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@section('contentTitle')
<h3>Subscription Policies</h3>
@endsection

@section('content')
<!--
<div class="card">

    <div class="card-header">
        This subscription policy effective from December 2021.
    </div>

    <div class="card-body">

        <dl>

            {{-- P#1 --}}
            <dt>
                P#1: Minimum payment: 500 {{ config('consumer.currency') }}
            </dt>
            {{-- P#1 --}}

            <hr>

            {{-- P#2 --}}
            <dt>
                P#2: For users 0 to 500, per user 3 {{ config('consumer.currency') }} OR 1000 {{
                config('consumer.currency') }} which is lower.
            </dt>
            <dd>
                According to P#1 and P#2
                <ul>
                    <li>
                        Subscription Fee for 0 users: 500 {{ config('consumer.currency') }}
                    </li>
                    <li>
                        Subscription Fee for 100 users: (100x3)=<s>300</s> 500 {{ config('consumer.currency') }}
                    </li>
                    <li>
                        Subscription Fee for 200 users: (200x3)=600 {{ config('consumer.currency') }}
                    </li>
                    <li>
                        Subscription Fee for 300 users: (300x3)=900 {{ config('consumer.currency') }}
                    </li>
                    <li>
                        Subscription Fee for 400 users: (400x3)= <s> 1200 </s> 1000 {{ config('consumer.currency') }}
                    </li>
                    <li>
                        Subscription Fee for 500 users: (500x3)= <s> 1500 </s> 1000 {{ config('consumer.currency') }}
                    </li>
                </ul>
            </dd>
            {{-- P#2 --}}

            <hr>

            {{-- P#3 --}}
            <dt>
                P#3: For users 501 to 1000, per user 2 {{ config('consumer.currency') }} OR 1500 {{
                config('consumer.currency') }} which is lower.
            </dt>
            <dd>
                According to P#3
                <ul>
                    <li>
                        Subscription Fee for 501 users: (501x2)=1002 {{ config('consumer.currency') }}
                    </li>
                    <li>
                        Subscription Fee for 600 users: (600x2)=1200 {{ config('consumer.currency') }}
                    </li>
                    <li>
                        Subscription Fee for 700 users: (700x2)=1400 {{ config('consumer.currency') }}
                    </li>
                    <li>
                        Subscription Fee for 800 users: (800x2)=<s>1600</s> 1500 {{ config('consumer.currency') }}
                    </li>
                    <li>
                        Subscription Fee for 900 users: (900x2)= <s> 1800 </s> 1500 {{ config('consumer.currency') }}
                    </li>
                    <li>
                        Subscription Fee for 1000 users: (1000x2)= <s> 2000 </s> 1500 {{ config('consumer.currency') }}
                    </li>
                </ul>
            </dd>
            {{-- P#3 --}}

            <hr>

            {{-- P#4 --}}
            <dt>
                P#4: For users 1001 to 1700, per user 1.5 {{ config('consumer.currency') }} OR 2000 {{
                config('consumer.currency') }} which is lower.
            </dt>
            <dd>
                According to P#4
                <ul>
                    <li>
                        Subscription Fee for 1001 users: (1001x1.5)=1501 {{ config('consumer.currency') }}
                    </li>
                    <li>
                        Subscription Fee for 1100 users: (1100x1.5)=1650 {{ config('consumer.currency') }}
                    </li>
                    <li>
                        Subscription Fee for 1200 users: (1200x1.5)=1800 {{ config('consumer.currency') }}
                    </li>
                    <li>
                        Subscription Fee for 1300 users: (1300x1.5)=1950 {{ config('consumer.currency') }}
                    </li>
                    <li>
                        Subscription Fee for 1400 users: (1400x1.5)= <s> 2100 </s> 2000 {{ config('consumer.currency')
                        }}
                    </li>
                    <li>
                        Subscription Fee for 1700 users: (1700x1.5)= <s> 2550 </s> 2000 {{ config('consumer.currency')
                        }}
                    </li>
                </ul>
            </dd>
            {{-- P#4 --}}

            <hr>

            {{-- P#5 --}}
            <dt>
                P#5: For users 1701 to 3000, per user 1.25 {{ config('consumer.currency') }} OR 3000 {{
                config('consumer.currency') }} which is lower.
            </dt>
            <dd>
                According to P#4
                <ul>
                    <li>
                        Subscription Fee for 1701 users: (1701x1.25)=2125 {{ config('consumer.currency') }}
                    </li>
                    <li>
                        Subscription Fee for 1800 users: (1800x1.25)=2250 {{ config('consumer.currency') }}
                    </li>
                    <li>
                        Subscription Fee for 2000 users: (2000x1.25)=2500 {{ config('consumer.currency') }}
                    </li>
                    <li>
                        Subscription Fee for 2500 users: (2500x1.25)=<s>3125</s> 3000 {{ config('consumer.currency') }}
                    </li>
                    <li>
                        Subscription Fee for 3000 users: (3000x1.25)= <s> 3750 </s> 3000 {{ config('consumer.currency')
                        }}
                    </li>
                </ul>
            </dd>
            {{-- P#5 --}}

            <hr>

            {{-- P#6 --}}
            <dt>
                P#6: For users 3001 to Unlimited, per user 1 {{ config('consumer.currency') }}.
            </dt>
            {{-- P#6 --}}

            <hr>

        </dl>

    </div>

</div>
-->
@endsection

@section('pageJs')
@endsection