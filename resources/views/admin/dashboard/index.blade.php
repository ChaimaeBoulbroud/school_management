@extends('layouts.app')

@section('content')
<div class="container-fluid" style="--sidebar-blue: #0056b3; --logo-blue-darker: #0d47a1; --logo-red: #D72828; --logo-yellow: #F7B52C;">
    
    <h1 class="h3 mb-4" style="color: #343a40;">Tableau de Bord</h1>

    <!-- ===== TOP STATS ROW ===== -->
    <div class="row">
        <!-- Students Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 0.75rem;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-dark text-uppercase mb-1">Élèves Inscrits</div>
                            <div class="h4 mb-0 fw-bold text-dark">{{ $totalEleves ?? '0' }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fa-solid fa-user-graduate fa-3x" style="color: var(--sidebar-blue); opacity: 0.8;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Teachers Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 0.75rem;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-dark text-uppercase mb-1">Enseignants</div>
                            <div class="h4 mb-0 fw-bold text-dark">{{ $totalEnseignants ?? '0' }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fa-solid fa-chalkboard-user fa-3x" style="color: var(--logo-red); opacity: 0.7;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Staff Card -->
        <div class="col-xl-3 col-md-6 mb-4">
             <div class="card border-0 shadow-sm h-100" style="border-radius: 0.75rem;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-dark text-uppercase mb-1">Personnel</div>
                            <div class="h4 mb-0 fw-bold text-dark">{{ $totalPersonnels ?? '0' }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fa-solid fa-users-gear fa-3x" style="color: var(--logo-yellow); opacity: 0.7;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Groups Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 0.75rem;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-dark text-uppercase mb-1">Groupes</div>
                            <div class="h4 mb-0 fw-bold text-dark">{{ $totalGroupes ?? '0' }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fa-solid fa-layer-group fa-3x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- ===== MAIN CONTENT ROW (Table & Chart) ===== -->
    <div class="row">
        <!-- Recent Inscriptions -->
        <div class="col-lg-7 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 0.75rem;">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0" style="color: #343a40;">Dernières Inscriptions</h5>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                           <thead>
                                <tr class="border-bottom">
                                    <th class="ps-3 text-uppercase text-dark" style="font-size: 0.8rem;">Nom Complet</th>
                                    <th class="text-uppercase text-dark" style="font-size: 0.8rem;">Date d'inscription</th>
                                    <th class="text-end pe-3 text-uppercase text-dark" style="font-size: 0.8rem;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentEleves as $eleve)
                                    <tr>
                                        <td class="ps-3 align-middle fw-bold" style="color: #343a40;">{{ $eleve->prenom }} {{ $eleve->nom }}</td>
                                        <td class="align-middle" style="color: #5a5c69;">{{ $eleve->created_at->format('d/m/Y') }}</td>
                                        <td class="text-end pe-3 align-middle">
                                            <a href="{{ route('admin.eleves.show', $eleve->id) }}" class="btn btn-sm text-white" style="background-color: var(--logo-blue-darker); border-radius: 0.5rem;">Voir Profil</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5" style="color: #5a5c69;">Aucune inscription récente.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 0.75rem;">
                 <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0" style="color: #343a40;">Répartition par Niveau</h5>
                 </div>
                 <div class="card-body d-flex align-items-center justify-content-center">
                    <div style="position: relative; height:250px; width:100%; max-width: 300px;">
                        <canvas id="niveauxChart"></canvas>
                    </div>
                 </div>
            </div>
        </div>
    </div>

</div>

<!-- ===== SCRIPT SECTION ===== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const labels = @json($niveauLabels ?? []);
    const data = @json($niveauData ?? []);
    const ctx = document.getElementById('niveauxChart');

    if (ctx && data.reduce((a, b) => a + b, 0) > 0) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    // THIS LINE IS NOW CORRECTED
                    backgroundColor: ['#0d47a1', '#D72828', '#F7B52C', '#1cc88a', '#343a40', '#6c757d'],
                    borderColor: '#ffffff', 
                    borderWidth: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 20,
                            boxWidth: 8,
                            font: { size: 12 }
                        }
                    }
                },
                cutout: '80%'
            }
        });
    } else if (ctx) {
        const canvasContext = ctx.getContext('2d');
        canvasContext.font = '16px "Figtree", sans-serif';
        canvasContext.fillStyle = '#858796';
        canvasContext.textAlign = 'center';
        canvasContext.fillText("Aucune donnée à afficher.", ctx.width / 2, ctx.height / 2);
    }
});
</script>
@endsection