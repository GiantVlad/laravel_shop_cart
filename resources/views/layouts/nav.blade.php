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
        <div class="col-sm-4 col-md-5 col-lg-4 general-nav-col">
            <form role="search" class="navbar-form">
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control" placeholder="Search" id="nav-search">
                    <div class="input-group-btn">
                        <button class="btn btn-default" type="button" id="nav-search-btn">
                            <i class="glyphicon glyphicon-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="collapse navbar-collapse" id="app-navbar-collapse">

                <div class="col-sm-4 col-lg-5 general-nav-col pull-right">
                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right general-nav">
                        <!-- Authentication Links -->
                        @guest
                            <li><a href="{{ route('login') }}">Login</a></li>
                            <li><a href="{{ route('register') }}">Register</a></li>
                            @else

                                <li><a href="/cart"><img height="22" width="25"
                                                         src="{{ asset('images/cart-icon.png') }}">
                                        Items: <span
                                                id="nav-items">{{ session('cartProducts') ? count(session('cartProducts'))-2 : 0 }}</span>
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
            <form style="display: none" id="logout-form" name="logout-form" action="{{ route('user.logout') }}" method="POST">
                {{ csrf_field() }}
            </form>
        </div>
    </div>
</nav>