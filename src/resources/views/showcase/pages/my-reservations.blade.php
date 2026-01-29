<!DOCTYPE html>
<html>
@include('showcase.partials._head')
<style>
    .active-reservations{
        color: #d16806 !important;
        outline: none;
    }
</style>

<body>

    <div class="preloader">
        <div class="loader"></div>
    </div>

    @include('showcase.partials._header')

    <div class="container-fluid padding-side padding-small pt-5 pb-5">
        <div class="row mb-4" data-aos="fade-up">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="display-5 fw-bold mb-2">Le mie Prenotazioni</h2>
                        <p class="text-muted">Gestisci le tue prenotazioni attive</p>
                    </div>
                    <div>
                        <a href="{{ route('showcase.index') }}" class="btn btn-outline-primary">
                            <svg width="18" height="18" class="me-2">
                                <use xlink:href="#arrow-left"></use>
                            </svg>
                            Torna al Catalogo
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger ms-2">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" data-aos="fade-up">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card shadow-sm">
                    <div class="card-body">
                        
                        @if($reservations->isEmpty())
                            {{-- Nessuna prenotazione --}}
                            <div class="text-center py-5">
                                <svg width="64" height="64" class="mb-3 text-muted">
                                    <use xlink:href="#book"></use>
                                </svg>
                                <h4 class="text-muted">Nessuna prenotazione trovata</h4>
                                <p class="text-muted">Non hai ancora prenotato nessun libro.</p>
                                <a href="{{ route('showcase.index') }}" class="btn btn-primary mt-3">
                                    Sfoglia il Catalogo
                                </a>
                            </div>
                        @else
                            {{-- Tabella Prenotazioni --}}
                            <h4 class="card-title mb-4">Riepilogo Prenotazioni ({{ $reservations->count() }})</h4>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Libro</th>
                                            <th>Barcode</th>
                                            <th>Categoria</th>
                                            <th>Condizione</th>
                                            <th>Stato</th>
                                            <th>Prenotato il</th>
                                            <th>Scadenza</th>
                                            <th>Giorni Rimasti</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach($reservations as $reservation)
                                        <tr>
                                            <td class="fw-bold">#{{ $reservation->id }}</td>
                                            
                                            <td>
                                                <div>
                                                    <strong>{{ $reservation->bookCopy->book->title }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $reservation->bookCopy->book->author }}</small>
                                                </div>
                                            </td>
                                            
                                            <td>
                                                <code class="bg-light p-1 rounded">{{ $reservation->bookCopy->barcode }}</code>
                                            </td>
                                            
                                            <td>
                                                <span class="badge bg-info text-dark">
                                                    {{ $reservation->bookCopy->book->category->name ?? 'N/A' }}
                                                </span>
                                            </td>
                                            
                                            <td>
                                                @php
                                                    $conditionClass = match($reservation->bookCopy->condition) {
                                                        'very good' => 'success',
                                                        'good' => 'primary',
                                                        'bad' => 'warning',
                                                        default => 'secondary'
                                                    };
                                                    $conditionText = match($reservation->bookCopy->condition) {
                                                        'very good' => 'Ottimo',
                                                        'good' => 'Buono',
                                                        'bad' => 'Discreto',
                                                        default => 'N/A'
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $conditionClass }}">{{ $conditionText }}</span>
                                            </td>
                                            
                                            <td>
                                                @php
                                                    $statusClass = match($reservation->status) {
                                                        'active' => 'success',
                                                        'completed' => 'secondary',
                                                        'cancelled' => 'danger',
                                                        default => 'warning'
                                                    };
                                                    $statusText = match($reservation->status) {
                                                        'active' => 'Attiva',
                                                        'completed' => 'Completata',
                                                        'cancelled' => 'Annullata',
                                                        default => 'N/A'
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                                            </td>
                                            
                                            <td>{{ $reservation->reserved_at->format('d/m/Y') }}</td>
                                            
                                            <td>{{ $reservation->due_date->format('d/m/Y') }}</td>
                                            
                                            <td>
                                                @if($reservation->status === 'active')
                                                    @php
                                                        $dueDate = \Carbon\Carbon::parse($reservation->due_date);
                                                        $daysLeft = (int) now()->diffInDays($dueDate, false);
                                                        $daysLeftClass = $daysLeft < 0 ? 'danger' : ($daysLeft <= 3 ? 'warning' : 'success');
                                                    @endphp
                                                    <span class="badge bg-{{ $daysLeftClass }}">
                                                        @if($daysLeft < 0)
                                                            Scaduta ({{ abs($daysLeft) }} gg)
                                                        @elseif($daysLeft == 0)
                                                            Scade oggi
                                                        @else
                                                            {{ $daysLeft }} giorni
                                                        @endif
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Legenda --}}
                            <div class="mt-4 p-3 bg-light rounded">
                                <h6 class="mb-2">Legenda Stati:</h6>
                                <div class="d-flex flex-wrap gap-3">
                                    <div><span class="badge bg-success">Attiva</span> - Prenotazione in corso</div>
                                    <div><span class="badge bg-secondary">Completata</span> - Libro restituito</div>
                                    <div><span class="badge bg-danger">Annullata</span> - Prenotazione annullata</div>
                                </div>
                            </div>
                        @endif
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/showcase/reservations.js') }}"></script>
    <script src={{ asset('js/showcase/jquery-1.11.0.min.js') }}></script>
    <script type="text/javascript" src={{ asset('js/showcase/bootstrap.bundle.min.js') }}></script>
    <script type="text/javascript" src={{ asset('js/showcase/plugins.js') }}></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script type="text/javascript" src={{ asset('js/showcase/script.js') }}></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
</body>

</html>