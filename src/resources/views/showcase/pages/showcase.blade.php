<!DOCTYPE html>
<html>

@include('showcase.partials._head')
<style>
.active-index{
    color: #d16806 !important;
    outline: none;
}
</style>

<body>
  <div class="preloader">
    <div class="loader"></div>
  </div>

  @include('showcase.partials._header')


  <section id="slider" data-aos="fade-up">
    <div class="container-fluid padding-side">
      <div class="d-flex rounded-5" style="background-image: url('{{asset('assets/showcase/images/nav-cover.png') }}'); background-size: cover; background-repeat: no-repeat; height: 85vh; background-position: center;">
          {{-- Form di Ricerca Libri --}}
          <div class="row align-items-center m-auto pt-5 px-4 px-lg-0">
              <div class="text-start col-md-6 col-lg-4 col-xl-6 offset-lg-1">
                  <h2 style="color:white" class="display-1 fw-normal">BookManage - Reserve your book</h2>
              </div>
              
              <div class="col-md-6 col-lg-5 col-xl-4 mt-5 mt-md-0"> 
                  <form id="search-form" class="form-group flex-wrap bg-white p-5 rounded-4 ms-md-5">                    
                      {{-- Ricerca Testuale --}}
                      <div class="col-lg-12 my-4">
                          <label class="form-label text-uppercase">Cerca (Titolo, Autore, ISBN)</label>
                          <input type="text" 
                                name="query" 
                                id="query"
                                class="form-control text-black-50 ps-3" 
                                placeholder="Es: Clean Code">
                      </div>

                      {{-- Categoria --}}
                      <div class="col-lg-12 my-4">
                          <label class="form-label text-uppercase">Categoria</label>
                          <select name="category_id" id="category_id" class="form-select text-black-50 ps-3">
                              <option value="">Tutte le categorie</option>
                              @foreach($categories ?? [] as $category)
                                  <option value="{{ $category->id }}">{{ $category->name }}</option>
                              @endforeach
                          </select>
                      </div>

                      {{-- Autore --}}
                      <div class="col-lg-12 my-4">
                          <label class="form-label text-uppercase">Autore</label>
                          <input type="text" 
                                name="author" 
                                id="author"
                                class="form-control text-black-50 ps-3" 
                                placeholder="Es: Robert C. Martin">
                      </div>

                      {{-- Anno Pubblicazione (Range) --}}
                      <div class="row">
                          <div class="col-6">
                              <div class="my-4">
                                  <label class="form-label text-uppercase">Anno Da</label>
                                  <input type="number" 
                                        name="year_from" 
                                        id="year_from"
                                        class="form-control text-black-50 ps-3" 
                                        placeholder="2000"
                                        min="1900"
                                        max="{{ date('Y') }}">
                              </div>
                          </div>
                          <div class="col-6">
                              <div class="my-4">
                                  <label class="form-label text-uppercase">Anno A</label>
                                  <input type="number" 
                                        name="year_to" 
                                        id="year_to"
                                        class="form-control text-black-50 ps-3" 
                                        placeholder="{{ date('Y') }}"
                                        min="1900"
                                        max="{{ date('Y') }}">
                              </div>
                          </div>
                      </div>

                      {{-- Solo Disponibili --}}
                      <div class="col-lg-12 my-4">
                          <div class="form-check">
                              <input class="form-check-input" 
                                    type="checkbox" 
                                    name="available_only" 
                                    id="available_only"
                                    value="1">
                              <label class="form-check-label" for="available_only">
                                  Solo libri con copie disponibili
                              </label>
                          </div>
                      </div>

                      {{-- Pulsanti --}}
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-arrow btn-primary flex-grow-1">
                            <span>Cerca Libri
                                <svg width="18" height="18">
                                    <use xlink:href="#arrow-right"></use>
                                </svg>
                            </span>
                        </button>
                        
                        <button type="button" id="reset-search" class="btn btn-outline-secondary px-4">
                            Reset
                        </button>
                    </div>
                  </form>
              </div>
          </div>

      </div>
    </div>
  </section>

  {{-- Sezione Risultati Ricerca --}}
  <section id="search-results" class="padding-medium" style="display: none;">
      <div class="container-fluid padding-side" data-aos="fade-up">
          <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
              <div>
                  <h3 class="display-4 fw-normal">Risultati Ricerca</h3>
                  <p class="text-muted" id="search-count">Trovati <span id="results-count">0</span> libri</p>
              </div>
              <button id="close-search" class="btn btn-outline-danger">
                  <svg width="18" height="18">
                      <use xlink:href="#close"></use>
                  </svg>
                  Chiudi Ricerca
              </button>
          </div>

          {{-- Spinner di caricamento --}}
          <div id="search-loading" class="text-center py-5" style="display: none;">
              <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Caricamento...</span>
              </div>
              <p class="mt-3 text-muted">Ricerca in corso...</p>
          </div>

          {{-- Risultati --}}
          <div id="search-books-container" class="swiper room-swiper mt-5">
              <div class="swiper-wrapper" id="search-books-wrapper">
                  {{-- I risultati verranno inseriti qui tramite JavaScript --}}
              </div>
              <div class="swiper-pagination room-pagination position-relative mt-5"></div>
          </div>

          {{-- Nessun risultato --}}
          <div id="no-results" class="text-center py-5" style="display: none;">
              <svg width="64" height="64" class="text-muted mb-3">
                  <use xlink:href="#book"></use>
              </svg>
              <h4 class="text-muted">Nessun libro trovato</h4>
              <p class="text-muted">Prova a modificare i filtri di ricerca</p>
          </div>
      </div>
  </section>

  {{-- Loop per ogni categoria --}}
  @foreach($categories as $category)
    @php
        $categoryBooks = $books->where('category_id', $category->id);
    @endphp
    
    @if($categoryBooks->count() > 0)
      <section id="categoria-{{ $category->id }}" class="padding-medium">
          <div class="container-fluid padding-side" data-aos="fade-up">
              <div class="d-flex flex-wrap align-items-center justify-content-between">
                  <div>
                      <h3 class="display-3 fw-normal text-left">{{ $category->name }}</h3>
                      @if($category->description)
                          <p class="text-left text-muted">{{ $category->description }}</p>
                          <br>
                      @endif
                  </div>
              </div>

              <div class="swiper room-swiper mt-5">
                  <div class="swiper-wrapper">
                      @foreach($categoryBooks as $book)
                      <div id="libro-{{ $book->id }}" class="swiper-slide">
                          <div class="room-item position-relative bg-black rounded-4 overflow-hidden">
                              <img src="{{ $book->cover_url }}" 
                                    alt="{{ $book->title }}" 
                                    class="post-image img-fluid rounded-4">
                              
                              <div class="product-description position-absolute p-5 text-start">
                                  <h4 class="display-6 fw-normal text-white">{{ $book->title }}</h4>
                                  <p class="product-paragraph text-white">
                                      {{ Str::limit($book->description ?? 'Nessuna descrizione disponibile.', 100) }}
                                  </p>
                                  
                                  <table>
                                      <tbody>
                                          <tr class="text-white">
                                              <td class="pe-2">Autore:</td>
                                              <td>{{ $book->author }}</td>
                                          </tr>
                                          <tr class="text-white">
                                              <td class="pe-2">Anno:</td>
                                              <td>{{ $book->year ?? 'N/D' }}</td>
                                          </tr>
                                          <tr class="text-white">
                                              <td class="pe-2">ISBN:</td>
                                              <td>{{ $book->isbn }}</td>
                                          </tr>
                                          @if($book->publisher)
                                          <tr class="text-white">
                                              <td class="pe-2">Editore:</td>
                                              <td>{{ $book->publisher }}</td>
                                          </tr>
                                          @endif
                                          <tr class="text-white">
                                              <td class="pe-2">Copie Disponibili:</td>
                                              <td>
                                                  <span class="badge bg-success">{{ $book->available_copies_count }}</span>
                                              </td>
                                          </tr>
                                      </tbody>
                                  </table>
                                  
                                  @auth
                                      <button onclick="reserveBook({{ $book->id }})" 
                                              class="btn btn-light mt-2">
                                          <span>Prenota Ora</span>
                                      </button>
                                  @else
                                      <a href="{{ route('showcase.my-reservations') }}">
                                          <p class="text-decoration-underline text-white m-0 mt-2">Login per Prenotare</p>
                                      </a>
                                  @endauth
                              </div>
                          </div>
                          
                          <div class="room-content text-center mt-3">
                              <h4 class="display-6 fw-normal">
                                  <a href="#libro-{{ $book->id }}">{{ $book->title }}</a>
                              </h4>
                              <p>
                                  <span class="text-primary fs-4">{{ $book->author }}</span>
                                  @if($book->year)
                                      /{{ $book->year }}
                                  @endif
                              </p>
                          </div>
                      </div>
                      @endforeach
                  </div>
                  <div class="swiper-pagination room-pagination position-relative mt-5"></div>
              </div>
          </div>
      </section>
    @endif
  @endforeach

  {{-- Messaggio se nessun libro --}}
  @if($books->count() === 0)
  <section class="padding-medium">
    <div class="container-fluid padding-side text-center">
        <p class="text-muted fs-4">Nessun libro disponibile al momento.</p>
        <p class="text-muted">Controlla più tardi o modifica i filtri di ricerca.</p>
    </div>
  </section>
  @endif


  <script>
  function reserveBook(bookId) {
      @guest
          alert('Devi effettuare il login per prenotare un libro!');
          window.location.href = "{{ route('showcase.my-reservations') }}";
          return;
      @endguest
      
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      
      if(confirm('Vuoi prenotare questo libro?')) {
          $.ajax({
              url: "{{ route('showcase.reserve') }}",
              type: 'POST',
              data: { book_id: bookId },
              headers: { 'X-CSRF-TOKEN': csrfToken },
              success: function(response) {
                  if (response.success) {
                      alert(response.message);
                      location.reload();
                  }
              },
              error: function(xhr) {
                  alert((xhr.responseJSON?.message || 'Errore durante la prenotazione.'));
              }
          });
      }
  }
  </script>


  <script>
    // Token CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // Gestione Form di Ricerca
    document.getElementById('search-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Raccogli i dati del form
        const formData = new FormData(this);
        const params = new URLSearchParams();
        
        for (let [key, value] of formData.entries()) {
            if (value) params.append(key, value);
        }
        
        // Mostra loading
        document.getElementById('search-loading').style.display = 'block';
        document.getElementById('search-results').style.display = 'block';
        document.getElementById('search-books-wrapper').innerHTML = '';
        document.getElementById('no-results').style.display = 'none';
        
        // Scroll alla sezione risultati
        document.getElementById('search-results').scrollIntoView({ behavior: 'smooth' });
        
        // Chiamata AJAX
        fetch(`{{ route('showcase.search') }}?${params.toString()}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('search-loading').style.display = 'none';
            
            if (data.success && data.books.length > 0) {
                // Aggiorna contatore
                document.getElementById('results-count').textContent = data.count;
                
                // Genera HTML per i risultati
                const booksHTML = data.books.map(book => generateBookCard(book)).join('');
                document.getElementById('search-books-wrapper').innerHTML = booksHTML;
                
                // Reinizializza Swiper (se lo usi)
                if (typeof Swiper !== 'undefined') {
                    new Swiper('.room-swiper', {
                        slidesPerView: 1,
                        spaceBetween: 20,
                        pagination: {
                            el: '.room-pagination',
                            clickable: true,
                        },
                        breakpoints: {
                            768: { slidesPerView: 2 },
                            1024: { slidesPerView: 3 },
                        }
                    });
                }
            } else {
                // Nessun risultato
                document.getElementById('no-results').style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Errore ricerca:', error);
            document.getElementById('search-loading').style.display = 'none';
            alert('Errore durante la ricerca. Riprova.');
        });
    });

    // Genera HTML per una card libro
    function generateBookCard(book) {
        const isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};
        const availableBadge = book.available_copies_count > 0 
            ? `<span class="badge bg-success">${book.available_copies_count}</span>`
            : `<span class="badge bg-danger">0</span>`;
        
        const actionButton = isAuthenticated && book.available_copies_count > 0
            ? `<button onclick="reserveBook(${book.id})" class="btn btn-light mt-2">
                  <span>Prenota Ora</span>
              </button>`
            : `<a href="{{ route('login') }}">
                  <p class="text-decoration-underline text-white m-0 mt-2">Login per Prenotare</p>
              </a>`;
        
        return `
            <div id="libro-${book.id}" class="swiper-slide">
                <div class="room-item position-relative bg-black rounded-4 overflow-hidden">
                    <img src="${book.cover_url || '/images/default-book.jpg'}" 
                        alt="${book.title}" 
                        class="post-image img-fluid rounded-4">
                    
                    <div class="product-description position-absolute p-5 text-start">
                        <h4 class="display-6 fw-normal text-white">${book.title}</h4>
                        <p class="product-paragraph text-white">
                            ${book.description ? book.description.substring(0, 100) + '...' : 'Nessuna descrizione disponibile.'}
                        </p>
                        
                        <table>
                            <tbody>
                                <tr class="text-white">
                                    <td class="pe-2">Autore:</td>
                                    <td>${book.author}</td>
                                </tr>
                                <tr class="text-white">
                                    <td class="pe-2">Anno:</td>
                                    <td>${book.year || 'N/D'}</td>
                                </tr>
                                <tr class="text-white">
                                    <td class="pe-2">ISBN:</td>
                                    <td>${book.isbn}</td>
                                </tr>
                                ${book.publisher ? `
                                <tr class="text-white">
                                    <td class="pe-2">Editore:</td>
                                    <td>${book.publisher}</td>
                                </tr>
                                ` : ''}
                                <tr class="text-white">
                                    <td class="pe-2">Copie Disponibili:</td>
                                    <td>${availableBadge}</td>
                                </tr>
                            </tbody>
                        </table>
                        
                        ${actionButton}
                    </div>
                </div>
                
                <div class="room-content text-center mt-3">
                    <h4 class="display-6 fw-normal">
                        <a href="#libro-${book.id}">${book.title}</a>
                    </h4>
                    <p>
                        <span class="text-primary fs-4">${book.author}</span>
                        ${book.year ? `/ ${book.year}` : ''}
                    </p>
                </div>
            </div>
        `;
    }

    // Reset filtri
    document.getElementById('reset-search').addEventListener('click', function() {
        document.getElementById('search-form').reset();
        document.getElementById('search-results').style.display = 'none';
    });

    // Chiudi ricerca
    document.getElementById('close-search')?.addEventListener('click', function() {
        document.getElementById('search-results').style.display = 'none';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  </script>
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