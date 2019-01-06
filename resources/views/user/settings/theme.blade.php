<div class="container-fluid pushFromTop">
    <div class="row">
            @foreach($data['themes'] as $theme)
            <div class="col-lg-6">
                <div class="card text-center @if($loop->iteration >= 3) mt-4 @endif">
                    <div class="card-body">
                        <h5 class="card-title"> {{ $theme['name']}}</h5>
                        <p class="card-text">Primary Color <div style="background-color: {{ $theme['primary'] }}" class="color-preview mx-auto"></div></p>
                        <p class="card-text">Secondary Color <div style="background-color:  {{ $theme['secondary'] }}" class="color-preview mx-auto"></div></p>

                        <theme-btn theme="{{ $theme['id'] }}"></theme-btn>
                    </div>
                    <div class="card-footer text-muted">
                       {!! $theme['comment'] !!}
                    </div>
                </div>
            </div>
            @endforeach
    </div>
</div>

