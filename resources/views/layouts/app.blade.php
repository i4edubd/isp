<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISP Bills</title>
    <style>
        body { font-family: sans-serif; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
    </style>
</head>
<body>
    <nav>
        <a href="{{ route('customer.dashboard') }}">Dashboard</a> |
        <a href="{{ route('customer.bills') }}">Bills</a> |
        <a href="{{ route('customer.payments') }}">Payments</a> |
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </nav>
    <main>
        @yield('content')
    </main>
</body>
</html>
