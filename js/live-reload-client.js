(function () {
  const ws = new WebSocket('ws://localhost:3001');

  ws.onopen = () => {
    console.log('Live reload connected.');
  };

  ws.onmessage = (event) => {
    if (event.data === 'reload') {
      console.log('Reloading page...');
      window.location.reload();
    }
  };

  ws.onerror = (error) => {
    console.error('Live reload error:', error);
  };

  ws.onclose = () => {
    console.log('Live reload connection closed. Attempting to reconnect in 3 seconds...');
    setTimeout(() => {
        // Attempt to reconnect
        window.location.reload();
    }, 3000);
  };
})();