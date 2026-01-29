const palette = {
  blue:   '#393799ff',
  purple: '#284386ff',
  green:  '#116458ff',
  yellow: '#776528ff',
  red:    '#791414ff',
  gray:   '#828488ff'
};

// Libri per categoria (Bar)
new Chart(document.getElementById('booksByCategoryChart'), {
  type: 'bar',
  data: {
    labels: booksByCategory.map(i => i.name),
    datasets: [{
      label: 'Libri',
      data: booksByCategory.map(i => i.total),
      backgroundColor: booksByCategory.map((_, idx) => [
        palette.blue,
        palette.purple,
        palette.green,
        palette.yellow,
        palette.red,
        palette.gray
      ][idx % 6]),
      borderRadius: 8,
      maxBarThickness: 40
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: false },
      tooltip: { padding: 10 }
    },
    scales: { y: { beginAtZero: true } }
  }
});

// Copie per stato (Doughnut)
new Chart(document.getElementById('copiesStatusChart'), {
  type: 'doughnut',
  data: {
    labels: copiesStatus.map(i => i.status),
    datasets: [{
      data: copiesStatus.map(i => i.total),
      backgroundColor: [
        palette.green,   // available
        palette.yellow,  // reserved
        palette.blue,    // loaned
        palette.red      // maintenance
      ],
      borderWidth: 2,
      borderColor: '#fff'
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { position: 'bottom' } },
    cutout: '65%'
  }
});

// Libri più prenotati (Horizontal bar)
new Chart(document.getElementById('mostReservedBooksChart'), {
  type: 'bar',
  data: {
    labels: mostReservedBooks.map(i => i.title),
    datasets: [{
      label: 'Prenotazioni',
      data: mostReservedBooks.map(i => i.total),
      backgroundColor: mostReservedBooks.map((_, idx) => [
        palette.purple,
        palette.blue,
        palette.green,
        palette.yellow,
        palette.red,
        palette.gray
      ][idx % 6]),
      borderRadius: 8
    }]
  },
  options: {
    indexAxis: 'y',
    responsive: true,
    plugins: { legend: { display: false } },
    scales: { x: { beginAtZero: true } }
  }
});

// Prenotazioni attive per utente
new Chart(document.getElementById('activeReservationsByUserChart'), {
  type: 'bar',
  data: {
    labels: activeReservationsByUser.map(i => i.name),
    datasets: [{
      label: 'Prenotazioni attive',
      data: activeReservationsByUser.map(i => i.total),
      backgroundColor: activeReservationsByUser.map((_, idx) => [
        palette.green,
        palette.blue,
        palette.purple,
        palette.yellow,
        palette.red,
        palette.gray
      ][idx % 6]),
      borderRadius: 8
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: { y: { beginAtZero: true } }
  }
});
