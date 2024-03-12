<div class="card text-truncate mt-4">
    <div class="card-header">
        {{ $title }}
    </div>
    <img onclick="playVideo('{{ $videoId }}')" data-index="{{ $videoId }}" class="card-img-top"
        src="{{ $thumbnail }}" alt="">
</div>
