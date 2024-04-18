<!DOCTYPE html>
<html>
<head>
    <title>User Notification</title>
</head>
<body>
    <h2>Xin chào, {{ $user->fullname }}</h2>
    <p>Đơn hàng của bạn đã được đặt </p>
    <h4>{{ $token }}</h4>
    <i>Vui lòng không chia sẻ mã này cho bất cứ ai </i>
</body>
</html>
