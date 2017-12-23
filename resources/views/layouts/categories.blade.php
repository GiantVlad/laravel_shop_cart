@if ( isset($parent_catalogs))
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <a href="{{asset('shop')}}">Shop</a>
                @php $last_elem_flag = count($parent_catalogs); @endphp
                @foreach( $parent_catalogs as $parent_catalog )
                    @php --$last_elem_flag; @endphp
                    &nbsp;
                    <small class="glyphicon glyphicon-arrow-right"></small>&nbsp;
                    <a @if ($last_elem_flag < 1)
                       class="active-catalog" data-id="{{ $parent_catalog['id'] }}"
                       @endif
                       href="{{ asset('shop/category/'.$parent_catalog['id']) }}">{{$parent_catalog['name']}}</a>
                @endforeach
            </div>
        </div>
    </div>
@endif
@if ( isset($catalogs))
    <div class="container">
        <div class="row">
            @foreach($catalogs as $catalog)
                <div class="col-xs-3 col-sm-2 col-md-1">
                    <a href="{{ asset('shop/category/'.$catalog->id) }}">{{$catalog->name}}</a>
                </div>
            @endforeach
        </div>
    </div>
@endif