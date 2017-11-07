@if ( isset($parent_catalogs))
    <div class="row">
        <div class="col-md-6 col-md-offset-1">
            <a href="{{asset('shop')}}">Shop</a>
            @foreach( $parent_catalogs as $parent_catalog )
                &nbsp;<small class="glyphicon glyphicon-arrow-right"></small>&nbsp;<a href="{{ asset('shop/category/'.$parent_catalog['id']) }}">{{$parent_catalog['name']}}</a>
            @endforeach
        </div>
    </div>
@endif
@if ( isset($catalogs))
    <div class="row">
        <div class="col-md-1">
        </div>
        @foreach($catalogs as $catalog)
            <div class="col-md-1">
                <a href="{{ asset('shop/category/'.$catalog->id) }}">{{$catalog->name}}</a>
            </div>
        @endforeach
    </div>
@endif