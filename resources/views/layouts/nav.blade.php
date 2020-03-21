<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header col-sm-4 col-md-3 general-nav-col">

            <!-- Collapsed Hamburger -->
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#app-navbar-collapse">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <!-- Branding Image -->
            <a class="navbar-brand" href="{{ url('/shop') }}">
                {{ config('app.name', 'Laravel') }}
            </a>
        </div>

        <nav-search :search-url="{{json_encode(route('search'))}}"></nav-search>

        <div class="collapse navbar-collapse" id="app-navbar-collapse">

                <div class="col-sm-4 col-lg-5 general-nav-col pull-right">

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right general-nav">
                        <!-- Authentication Links -->
                        @guest
                            <li><a href="{{ route('login') }}">Login</a></li>
                            <li><a href="{{ route('register') }}">Register</a></li>
                            @else
                            <li><a href="/orders" target="_blank">Orders</a></li>
                                <nav-cart :cart="@if (session()->has('cartProducts')) {{json_encode(session('cartProducts'))}} @else {{json_encode([])}} @endif">
                                </nav-cart>

                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                       aria-expanded="false">
                                        {{ Auth::user()->name }} <span class="caret"></span>
                                    </a>

                                    <ul class="dropdown-menu" role="menu">
                                        <li>
                                            <a onclick="document.getElementById('logout-form').submit();" style="cursor: pointer">
                                                Logout
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                @endguest
                    </ul>

                </div>
            <form style="display: none" id="logout-form" name="logout-form" action="{{ route('logout') }}" method="POST">
                @csrf
                @method('POST')
            </form>
        </div>
    </div>
</nav>
<modal-wrapper></modal-wrapper>
