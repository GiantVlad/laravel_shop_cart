@extends('layouts.admin-auth')

@section('content')
<div style="display: flex; flex-direction: column; gap: 0.75rem;">
    <form method="POST" action="{{ route('admin.password.email') }}" style="display: flex; flex-direction: column; gap: 0.75rem;">
        {{ csrf_field() }}

        <div>
            <label for="email" class="admin-label">E-Mail Address</label>
            <input id="email" type="email" class="admin-input" name="email" value="{{ old('email') }}" required autofocus>
        </div>

        <button type="submit" class="admin-btn admin-btn--primary">
            Send Password Reset Link
        </button>
    </form>

    <a href="{{ route('admin.login') }}" style="font-size: 0.8rem; color: #0284c7;">Back to login</a>
</div>
@endsection
