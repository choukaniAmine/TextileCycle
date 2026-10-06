<div class="col">
    <div class="atelier-card card">
        {{-- Image --}}
        <div class="card-img-wrap">
            @if ($atelier->image && file_exists(public_path('storage/' . $atelier->image)))
                <img src="{{ asset('storage/' . $atelier->image) }}" alt="{{ $atelier->nom }}">
            @else
                <div class="placeholder-icon">
                    <i class="bi bi-scissors" style="font-size:2.5rem;"></i>
                    <span class="small fw-semibold mt-1">{{ Str::limit($atelier->nom, 18) }}</span>
                </div>
            @endif
            <span class="ville-badge">
                <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $atelier->ville }}
            </span>
            @if ($atelier->services_count > 0)
                <span class="type-badge">
                    <i class="bi bi-tools me-1"></i>{{ $atelier->services_count }} service(s)
                </span>
            @endif
        </div>

        {{-- Body --}}
        <div class="card-body d-flex flex-column p-4">
            {{-- Étoiles fictives basées sur l'id (valeur ajoutée visuelle) --}}
            @php
                $stars = (($atelier->id * 7) % 2) + 4; // 4 ou 5 étoiles
            @endphp
            <div class="stars mb-2">
                @for ($i = 1; $i <= 5; $i++)
                    @if ($i <= $stars)
                        <i class="bi bi-star-fill"></i>
                    @else
                        <i class="bi bi-star empty"></i>
                    @endif
                @endfor
                <span class="text-muted small ms-1">{{ $stars }}.0</span>
            </div>

            <h5 class="fw-bold text-dark mb-1">{{ $atelier->nom }}</h5>
            <p class="text-muted small mb-3 flex-grow-1">
                {{ Str::limit($atelier->description ?? 'Atelier partenaire engagé dans la retouche et la transformation textile durable.', 100) }}
            </p>

            {{-- Infos --}}
            <div class="d-flex flex-column gap-1 small text-secondary mb-3 pb-3 border-bottom">
                <div><i class="bi bi-clock text-success me-2"></i>{{ $atelier->horaires ?? 'Horaires non renseignés' }}</div>
                <div><i class="bi bi-telephone text-success me-2"></i>{{ $atelier->telephone }}</div>
            </div>

            {{-- Tags services --}}
            @if ($atelier->services && $atelier->services->count() > 0)
                <div class="d-flex flex-wrap gap-1 mb-3">
                    @foreach ($atelier->services->take(3) as $srv)
                        <span class="badge rounded-pill px-2 py-1"
                              style="background:#e8f5e9; color:#0f7038; font-size:.7rem; border:1px solid #c8e6c9;">
                            {{ $srv->type_service }}
                        </span>
                    @endforeach
                </div>
            @endif

            <a href="{{ route('ateliers.show', $atelier) }}"
               class="btn w-100 rounded-pill py-2 fw-semibold"
               style="background:linear-gradient(135deg,#0f7038,#28c76f); color:#fff; border:none;">
                Voir les services <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</div>
