const http = require('http');
const { exec } = require('child_process');

setInterval(() => {
  exec('php save_bitcoin_cache.php', (error, stdout, stderr) => {
    if (error) {
      console.error('שגיאה:', error);
      return;
    }
    console.log('עודכן:', stdout.trim());
  });
}, 5000); // כל 5 שניות

// שרת דמה כדי ש-Railway לא יכבה את האפליקציה
http.createServer((req, res) => {
  res.end('Updater is running');
}).listen(process.env.PORT || 3000);
