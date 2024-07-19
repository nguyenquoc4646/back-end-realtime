@component('mail::message')
    Xin chào <b>{{ $user->name }}</b>
    <p>Bạn đã quên mật khẩu</p>
    <p>Vui lòng click vào đây để tạo mật khẩu mới</p>
    @component('mail::button', ['url' => url('http://localhost:3000/reset/' . $user->remember_token)])
        Đặt lại mật khẩu
    @endcomponent
    <p>Bạn sẽ tạo mật khẩu mới ngay</p>
    Gửi từ: {{ config('mail.from.address') }} ({{ config('mail.from.name') }})
    Cảm ơn bạn !
@endcomponent
