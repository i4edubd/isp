@extends ('laraview.layouts.sideNavLayout')

@section('title')
Billing Profiles helper
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '2';
$active_link = '5';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection


@section('contentTitle')
<h3>Create billing profile according to your business case</h3>
@endsection
@section('content')

<div class="card">

    <div class="card-body">
        <dl>
            <dt>
                নোট১ঃ শুধুমাত্র পি পি পি এবং স্টেটিক-আইপি ইউজার এর ক্ষেত্রে বিলিং প্রোফাইল প্রয়োজন হয় , হটস্পট ইউজার এর
                জন্য
                বিলং প্রোফাইল প্রয়োজন হয় না।
            </dt>
            <hr>
            <dt>
                নোট২ঃ <span class="text-success"> Automatic bill = yes </span> মানে প্রতিমাসের এক তারিখে কাস্টমারের বিল
                অটোমেটিক্যালি তৈরি হবে।
            </dt>
            <hr>
            <dt>
                নোট৩ঃ <span class="text-success"> Automatic Suspend = yes </span> মানে কাস্টমারের প্যাকেজের মেয়াদ শেষ
                হয়ে
                গেলে কাস্টমার অটোমেটিক্যালি সাসপেন্ড হয়ে যাবে। কাস্টমারের লিস্ট থেকে কাস্টমারের প্যাকেজের মেয়াদ দেখা
                যাবে।
            </dt>
            <hr>
            <dt>
                নোট৪ঃ <span class="text-success"> Billing cycle ends with month = no </span> মানে নতুন কাস্টমারকে পুরো
                এক
                মাসের জন্য বিল করুন।
            </dt>
            <hr>
            <dt>
                নোট৫ঃ <span class="text-success"> Billing cycle ends with month = yes </span> মানে নতুন কাস্টমারকে চলতি
                মাসের যে কয়দিন বাকি আছে সেই কয়দিনের জন্য বিল করুন।
            </dt>
            <hr>
            {{-- Case 1 --}}
            <dt>Monthly Billing#</dt>
            <dd>
                <ul>
                    <li>
                        কাস্টমারের কাছে ৩০ দিন বা এক মাসের বিল একসাথে নেওয়া হয় এবং রিসেলার তার আই এস পি কে একজন
                        কাস্টমারের
                        এক মাসের পেমেন্ট একসাথে প্রদান করে।
                    </li>
                    <li>
                        কাস্টমারের কাছ থেকে অনলাইন এবং অফলাইন দুই ভাবেই পেমেন্ট কালেক্ট করা হয়।
                    </li>
                    <li>
                        এক্ষেত্রে ১ থেকে ৩০ তারিখ পর্যন্ত ৩০ টা বিলিং প্রোফাইল তৈরি হবে।
                    </li>
                </ul>
            </dd>
            {{-- Case 1 --}}
            <hr>
            {{-- Case 2 --}}
            <dt>Jump Billing/Daily Billing#</dt>
            <dd>
                <ul>
                    <li>
                        ডেইলি বেসিস বিলিং । রিসেলার কাস্টমারের কাছ থেকে ১ মাসের পেমেন্ট একসাথে কালেক্ট করতে পারে কিন্তু
                        রিসেলার কাস্টমারকে ১ দিন, ২ দিন , ৭ দিন , ৩০ দিন ...
                        এই রকম যে কয়দিন প্রয়োজন সেই কয়দিনের জন্য একটিভেট করতে পারে এবং রিসেলার সেই কয়দিনের জন্য আই এস পি
                        কে
                        পেমেন্ট করে ।
                    </li>
                    <li>
                        এক্ষেত্রে Jump billing/Daily Billing নামে একটি বিলিং প্রোফাইল তৈরি হবে।
                    </li>
                </ul>
            </dd>
            {{-- Case 2 --}}
            <hr>
            {{-- Case 3 --}}
            <dt>Free Customer#</dt>
            <dd>
                <ul>
                    <li>
                        ফ্রি কাস্টমারের বিল তৈরি হবে না এবং তাদের প্যাকেজের মেয়াদ শেয হয়ে গেলেও তারা সাস্পেন্ড হবেন না।
                    </li>
                    <li>
                        এক্ষেত্রে Free Customer নামে একটি বিলিং প্রোফাইল তৈরি হবে।
                    </li>
                </ul>
            </dd>
            {{-- Case 3 --}}
            <hr>
            {{-- Case 4--}}
            <dt>Mixed Reseller#</dt>
            <dd>
                <ul>
                    <li>
                        আমার Jump/Daily Basis এবং Monthly Basis দুই ধরনের রিসেলার আছে ।
                    </li>
                    <li>
                        Jump/Daily Basis রিসেলারদের জন্য Jump/Daily Billing প্রোফাইল তৈরি করুন।
                    </li>
                    <li>
                        Monthly Basis রিসেলারদের জন্য Monthly Billing প্রোফাইল তৈরি করুন।
                    </li>
                </ul>
            </dd>
            {{-- Case 4 --}}
            <hr>
            <dt> আপনার Business Case যদি উপরের একটাও ম্যাচ না করে তাহলে আমাদের হেল্পলাইন নাম্বারে যোগাযোগ করুন। </dt>

        </dl>
    </div>

</div>

@endsection

@section('pageJs')
@endsection
