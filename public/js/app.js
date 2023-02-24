(function () {
  /**
   * Флаг успешного соединения с сервером.
   * @var bool
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
   * Обработчик события: данные с сервера получены.
   * @handler
   * @param MessageEvent
   */
  ws.onmessage = function(e) {

    const data = JSON.parse(e.data);
    console.info(data)

    document.querySelector('#date').innerHTML = data.time.split(' ').pop();
    document.querySelectorAll('table tbody .value').forEach((spanElement) => {

      const currency = spanElement.classList.value.split('-').pop();
      const todayValue = data.rate[0].rates[currency.toUpperCase()];
      const yesterdayValue = data.rate[1].rates[currency.toUpperCase()];
      const difference = todayValue - yesterdayValue;

      spanElement.innerHTML = todayValue == null ? '—' : todayValue;

      if (difference !== 0) {
        spanElement.nextElementSibling.innerHTML = difference;
        spanElement.nextElementSibling.classList.add(difference > 0 ? 'bg-success' : 'bg-danger');
      }
    });
  };

  // Запускаем опроса сервера.
  const descriptor = setInterval(function() {
    if (descriptor && wsError) {
      clearInterval(descriptor);
    } else {
      ws.readyState && ws.send('');
    }
  }, 1000);
})();