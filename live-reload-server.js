const WebSocket = require('ws');
const chokidar = require('chokidar');

// Create a new WebSocket server on port 3001
const wss = new WebSocket.Server({ port: 3001 });

console.log('Live reload server started on port 3001...');

// Broadcast a message to all connected clients
function sendReload() {
  console.log('File change detected. Sending reload signal...');
  wss.clients.forEach(client => {
    if (client.readyState === WebSocket.OPEN) {
      client.send('reload');
    }
  });
}

// Initialize chokidar watcher
const watcher = chokidar.watch(['./**/*.php', './**/*.js', './**/*.css'], {
  ignored: /(^|[\/\\])\../, // ignore dotfiles
  persistent: true,
  ignoreInitial: true, // Don't trigger on initial scan
});

// Add event listeners
watcher
  .on('add', path => sendReload())
  .on('change', path => sendReload())
  .on('unlink', path => sendReload());