<!DOCTYPE html>
<html>
<head>
    <title>User Notification</title>
</head>
<body>
    <h2>Xin chào, {{ $user->fullname }}</h2>
    <p>Đây là mật khẩu mới của bạn: </p>
    <h4>{{ $code }}</h4>
    <i>Vui lòng không chia sẻ cho bất cứ ai </i>
</body>
</html>
