@extends('layouts.admin')

@section('title', 'Triage des dons par IA')

@section('content')
{{-- Header avec bouton Retour --}}
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-1 text-gray-800 font-weight-bold">
            <i class="fas fa-robot text-success"></i> Triage des Dons (IA)
        </h1>
        <p class="text-muted mb-0 small">
            Score = probabilité d'acceptation (Naive Bayes) + ancienneté + quantité + mots-clés.
        </p>
    </div>
    <a href="{{ route('admin.dons.index') }}" class="btn btn-secondary shadow-sm">
        <i class="fas fa-arrow-left"></i> Retour aux dons
    </a>
</div>

{{-- SECTION GRAPHIQUES --}}
<div class="row mb-4">
    {{-- Graphique 1 : Niveau d'urgence --}}
    <div class="col-md-5">
        <div class="card shadow h-100">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-primary">Répartition des urgences (Priorité)</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2" style="height: 250px; position: relative;">
                    <canvas id="priorityChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Graphique 2 : Probabilités d'acceptation --}}
    <div class="col-md-7">
        <div class="card shadow h-100">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-success">Probabilité d'acceptation par l'association</h6>
            </div>
            <div class="card-body">
                <div class="chart-bar" style="height: 250px; position: relative;">
                    <canvas id="probabilityChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- TABLEAU --}}
<div class="card shadow mb-4">
    <div class="card-body table-responsive p-0">
        <table class="table table-hover mb-0">
            <thead class="thead-light">
                <tr>
                    <th>Référence</th>
                    <th>Association</th>
                    <th>Donateur</th>
                    <th class="text-center">Qté</th>
                    <th>Statut</th>
                    <th class="text-center text-primary">P(Acceptation)</th>
                    <th class="text-center text-success">Priorité (IA)</th>
                    <th class="text-right">Créé le</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dons as $don)
                    <tr>
                        <td class="font-weight-bold align-middle">{{ $don->reference ?? '-' }}</td>
                        <td class="align-middle">{{ $don->association?->name ?? '-' }}</td>
                        <td class="align-middle">{{ $don->donor?->name ?? '-' }}</td>
                        <td class="text-center align-middle font-weight-bold">{{ $don->quantity }}</td>
                        <td class="align-middle">
                            <span class="badge badge-info">
                                {{ $don->status?->name ?? $don->status }}
                            </span>
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge badge-primary px-2 py-1" style="font-size: 0.85rem;">
                                {{ number_format((float) $don->acceptance_probability * 100, 0) }}%
                            </span>
                        </td>
                        <td class="text-center align-middle">
                            @php
                                $score = (float) $don->priority_score;
                                $badgeClass = $score > 70 ? 'danger' : ($score > 40 ? 'warning' : 'success');
                            @endphp
                            <span class="badge badge-{{ $badgeClass }} px-2 py-1" style="font-size: 0.85rem;">
                                {{ number_format($score, 1) }}
                            </span>
                        </td>
                        <td class="text-right align-middle text-muted small">
                            {{ $don->created_at?->format('d/m/Y H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                            <h5 class="font-weight-bold">Aucun don en attente</h5>
                            <p class="mb-0">Tous les dons ont été traités.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($dons->hasPages())
        <div class="card-footer bg-white">
            {{ $dons->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>
@endsection

{{-- SCRIPT CHART.JS --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- GRAPHIQUE 1 : CAMEMBERT (Priorité) ---
        var ctxPie = document.getElementById("priorityChart");
        var priorityChart = new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(array_keys($priorityStats)) !!},
                datasets: [{
                    data: {!! json_encode(array_values($priorityStats)) !!},
                    backgroundColor: ['#e74a3b', '#f6c23e', '#1cc88a'], // Rouge, Orange, Vert
                    hoverBackgroundColor: ['#be2617', '#dda20a', '#13855c'],
                    borderWidth: 0
                }]
            },
            options: {
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // --- GRAPHIQUE 2 : BARRES (Probabilités) ---
        var ctxBar = document.getElementById("probabilityChart");
        var probabilityChart = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($probabilityStats)) !!},
                datasets: [{
                    label: "Nombre de dons",
                    data: {!! json_encode(array_values($probabilityStats)) !!},
                    backgroundColor: '#4e73df', // Bleu Bootstrap
                    hoverBackgroundColor: '#2e59d9',
                    borderRadius: 4
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 } // Nombre entier uniquement
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    });
</script>
@endpush