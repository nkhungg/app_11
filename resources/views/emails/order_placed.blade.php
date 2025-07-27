<h2>Thank you for your order!</h2>
<p>Order Details:</p>
<ul>
    @foreach($cart as $item)
        <li>{{ $item->name }} x {{ $item->qty }} - {{ $item->subtotal() }}đ</li>
    @endforeach
</ul>
<p>Total: {{ $order['total'] }}đ</p>
<p>We will ship your order to:</p>
<p>
    {{ $order['name'] }}<br>
    {{ $order['address'] }}, {{ $order['city'] }}, {{ $order['state'] }}, {{ $order['country'] }}<br>
    {{ $order['zip'] }}
</p>