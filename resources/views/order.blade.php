<!DOCTYPE html>
<html>
<head>
    <title>User Notification</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Xin chào, {{ $order->user->fullname }}</h2>
    <p>Đơn hàng của bạn đã được đặt </p>
    
    <table>
        <thead>
            <tr>
                <th>Sản phẩm</th>
                <th>Hình ảnh</th>
                <th>Đơn giá</th>
                <th>Số lượng</th>
                <th>Tổng cộng</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td><img src="{{ $item->product->image }}" alt="{{ $item->product->name }}" width="100"></td>
                    <td>{{ number_format($item->product->price, 0, ',', '.') }}đ</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->quantity * $item->product->price, 0, ',', '.') }}đ</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p>Tổng hoá đơn: {{ number_format($order->total, 0, ',', '.') }}đ</p>
</body>
</html>
