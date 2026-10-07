<header class="page-hero">
    <div class="container position-relative">
        @isset($badge)<span class="eyebrow mb-3">{{ $badge }}</span>@endisset
        <h1 class="display-5 mb-2">{{ $titre }}</h1>
        @isset($sous)<p class="lead mb-0">{{ $sous }}</p>@endisset
        @isset($actions)<div class="mt-4 d-flex flex-wrap gap-2">{!! $actions !!}</div>@endisset
    </div>
</header>