@extends('layouts.admin-auth')

@section('content')
<form method="POST" action="{{ route('admin.password.request') }}" style="display: flex; flex-direction: column; gap: 0.75rem;">
    {{ csrf_field() }}

    <input type="hidden" name="token" value="{{ $token }}">

    <div>
        <label for="email" class="admin-label">E-Mail Address</label>
        <input id="email" type="email" class="admin-input" name="email" value="{{ $email ?? old('email') }}" required autofocus>
    </div>

    <div>
        <label for="password" class="admin-label">Password</label>
        <input id="password" type="password" class="admin-input" name="password" required>
    </div>

    <div>
        <label for="password-confirm" class="admin-label">Confirm Password</label>
        <input id="password-confirm" type="password" class="admin-input" name="password_confirmation" required>
    </div>

    <button type="submit" class="admin-btn admin-btn--primary">
        Reset Password
    </button>
</form>
@endsection
