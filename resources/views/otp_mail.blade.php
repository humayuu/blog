@php
    $otp = random_int(100000, 999999);
    $cacheKey = 'otp_' . $user->id;
    Cache::put($cacheKey, $otp, now()->addMinutes(10));
@endphp

<!DOCTYPE html>
<html>

<head>
    <title>Welcome</title>
</head>

<body>
    <h1>Welcome, {{ $user->name }}!</h1>
    <p>Your otp is {{ $otp }}.</p>
</body>

</html>
