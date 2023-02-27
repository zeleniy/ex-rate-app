(function () {
  /**
   * Флаг успешного соединения с сервером.
   * @var {bool}
   */
  let wsError = false;

  // Соединяемся
  const ws = new WebSocket('ws://localhost:2346/');


  /**
   * Обработчик события: соединение установлено.
   * @handler
   */
  ws.onopen = function() {
    // Делаем запрос по факту открытия соединения
    ws.send('');
    // Запускаем опроса сервера.
    const descriptor = setInterval(function() {
      if (descriptor && wsError) {
        clearInterval(descriptor);
      } else {
        ws.readyState && ws.send('');
      }
    }, 1000);
  };


  /**
   * Обработчик события: возникла ошибка взаимодейтсвия.
   * @handler
   */
  ws.onerror = function() {
    wsError = true;
    alert('Сервер временно не доступен. Попробуйте перезагрузить приложение позже.');
  };


  /**
   * Обработчик события: соединение закрыто.
   * @handler
   */
  ws.onclose = function() {
    wsError = true;
  };


  /**
   * Функция расчёта разности в процентах.
   * @param {number} a
   * @param {number} b
   * @returns {number}
   */
  function getPerentDiff(a, b) {

    let value;

    if (a === b) {
      value = 0;
    } else if (a < b) {
      value = ((b - a) / a) * 100;
    } else {
      value = ((a - b) / a) * 100;
    }

    return round(value);
  }


  /**
   * Округлить до двух знаков после запятой.
   * @param {number} value
   * @returns {number}
   */
  function round(value) {

    return Math.round(value * 100) / 100;
  }


  /**
   * Обработчик события: данные с сервера получены.
   * @handler
   * @param {MessageEvent} e
   */
  ws.onmessage = function(e) {

    console.info(e)
    const data = JSON.parse(e.data);

    document.querySelector('#date').innerHTML = data.time.split(' ').pop();
    document.querySelectorAll('table tbody .value').forEach((spanElement) => {

      const currency = spanElement.classList.value.split('-').pop();
      const todayValue = round(data.rate[0].rates[currency.toUpperCase()]);
      const yesterdayValue = round(data.rate[1].rates[currency.toUpperCase()]);
      const difference = round(todayValue - yesterdayValue);

      spanElement.innerHTML = todayValue == null ? '—' : todayValue;
      spanElement.nextElementSibling.innerHTML = difference + ' (' + getPerentDiff(todayValue, yesterdayValue) + '%)';

      spanElement.nextElementSibling.classList.add(difference >= 0 ? 'bg-success' : 'bg-danger');
    });
  };
})();