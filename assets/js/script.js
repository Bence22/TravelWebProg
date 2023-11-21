
const ctx = document.querySelector('.mnb-chart');
if (ctx) {
  var dates = document.querySelectorAll('[data-attribute-date]');
  var _dates = [];
  var _rates = [];
  var rates = document.querySelectorAll('[data-attribute-value]');
  if (dates) {
    dates.forEach(date => {
      _dates.push(date.getAttribute('data-attribute-date'));
    })
  }
  if (rates) {
    rates.forEach(rate => {
      _rates.push(
        rate.getAttribute('data-attribute-value')
      );
    })
  }
  var label = document.querySelector('[data-attribute-label]')?.getAttribute('data-attribute-label');
}

if (_rates && _dates) {
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: _dates,
      datasets: [
        {
          label: label,
          data: _rates,
          borderWidth: 1,
          borderColor: '#007bff',
          backgroundColor: '#007bff'
        }
      ]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
}
