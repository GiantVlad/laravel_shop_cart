<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">

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


        <!-- Left Side Of Navbar -->
        <form role="form" class="form-inline">
            <div class="row">
                <div class="nav-search input-group col-md-5 col-lg-offset-1">
                    <input type="text" class="form-control" placeholder="Search" id="nav-search">
                    <div class="input-group-btn">
                        <button class="btn btn-default" type="button" id="nav-search-btn">
                            <i class="glyphicon glyphicon-search"></i>
                        </button>
                    </div>
                </div>
                <div class="input-group col-md-4 pull-right">
                    <!-- Right Side Of Navbar -->
                    <div class="collapse navbar-collapse" id="app-navbar-collapse">
                        <ul class="nav navbar-nav navbar-right list-inline">
                            <!-- Authentication Links -->
                            @guest
                                <li><a href="{{ route('login') }}">Login</a></li>
                                <li><a href="{{ route('register') }}">Register</a></li>
                            @else

                                <li><a href="/cart"><img height="22" width="25"
                                                         src="{{ asset('images/cart-icon.png') }}">
                                        Items: <span
                                                id="nav-items">{{ session('cartProducts') ? count(session('cartProducts'))-1 : 0 }}</span>
                                        Total: <span
                                                id="nav-total">{{ session('cartProducts') ? session('cartProducts')['total'] : 0 }}</span>
                                    </a>
                                </li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                       aria-expanded="false">
                                        {{ Auth::user()->name }} <span class="caret"></span>
                                    </a>

                                    <ul class="dropdown-menu" role="menu">
                                        <li>
                                            <a href="{{ route('user.logout') }}"
                                               onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                                Logout
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </div>
        </form>
        <form id="logout-form" name="logout-form" action="{{ route('user.logout') }}" method="POST"
              style="display: none;">
            {{ csrf_field() }}
        </form>
    </div>
</nav>