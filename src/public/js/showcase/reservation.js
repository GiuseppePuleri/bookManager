/**
 * Showcase Reservations JavaScript
 * Gestisce prenotazioni, cancellazioni e notifiche
 */

// Token CSRF per le richieste AJAX
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

/**
 * Prenota un libro
 */
function reserveBook(bookId) {
    if (!bookId) {
        showNotification('Errore: ID libro non valido', 'error');
        return;
    }

    // Conferma prenotazione
    if (!confirm('Vuoi prenotare questo libro?')) {
        return;
    }

    // Mostra loader
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Prenotazione...';

    fetch('/showcase/reserve', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            book_id: bookId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            
            // Aggiorna UI - disabilita il bottone
            btn.innerHTML = '<span>✓ Prenotato</span>';
            btn.classList.remove('btn-light');
            btn.classList.add('btn-success');
            
            // Opzionale: mostra dettagli prenotazione
            if (data.reservation) {
                console.log('Prenotazione creata:', data.reservation);
            }
            
            // Redirect alle prenotazioni dopo 2 secondi
            setTimeout(() => {
                window.location.href = '/showcase/my-reservations';
            }, 2000);
            
        } else {
            showNotification(data.message, 'error');
            btn.disabled = false;
            btn.innerHTML = originalText;
            
            // Se richiede login, redirect
            if (data.redirect) {
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            }
        }
    })
    .catch(error => {
        console.error('Errore:', error);
        showNotification('Errore di connessione. Riprova.', 'error');
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

/**
 * Annulla una prenotazione
 */
function cancelReservation(reservationId) {
    if (!reservationId) {
        showNotification('Errore: ID prenotazione non valido', 'error');
        return;
    }

    if (!confirm('Sei sicuro di voler annullare questa prenotazione?')) {
        return;
    }

    fetch(`/showcase/reservation/${reservationId}/cancel`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            
            // Rimuovi la riga dalla tabella o ricarica la pagina
            setTimeout(() => {
                location.reload();
            }, 1500);
            
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Errore:', error);
        showNotification('Errore di connessione. Riprova.', 'error');
    });
}

/**
 * Mostra notifiche toast
 */
function showNotification(message, type = 'info') {
    // Rimuovi notifiche esistenti
    const existingToast = document.querySelector('.toast-notification');
    if (existingToast) {
        existingToast.remove();
    }

    // Crea notifica
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    
    const bgColor = {
        'success': '#28a745',
        'error': '#dc3545',
        'warning': '#ffc107',
        'info': '#17a2b8'
    }[type] || '#17a2b8';
    
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${bgColor};
        color: white;
        padding: 15px 25px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 9999;
        animation: slideIn 0.3s ease-out;
        max-width: 400px;
        font-size: 14px;
    `;
    
    toast.textContent = message;
    document.body.appendChild(toast);

    // Animazione CSS
    if (!document.querySelector('#toast-animation-style')) {
        const style = document.createElement('style');
        style.id = 'toast-animation-style';
        style.textContent = `
            @keyframes slideIn {
                from {
                    transform: translateX(400px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            @keyframes slideOut {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(400px);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }

    // Rimuovi dopo 5 secondi
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

/**
 * Inizializza tooltip Bootstrap (se presenti)
 */
document.addEventListener('DOMContentLoaded', function() {
    // Inizializza tooltip
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});