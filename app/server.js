import http from 'http';
import director from 'director';
import bot from './bot.js';
import https from 'https';

const router = new director.http.Router({
  '/': {
    post: bot.respond,
    get: ping,
  },
});

const server = http.createServer((request, res) => {
  const req = request;

  req.chunks = [];
  req.on('data', (chunk) => {
    req.chunks.push(chunk.toString());
  });

  router.dispatch(req, res, (err) => {
    res.writeHead(err.status, { 'Content-Type': 'text/plain' });
    res.end(err.message);
  });
});

const port = Number(process.env.PORT || 4000);
server.listen(port);

function ping() {
  console.log('ping');
  this.res.writeHead(200);
  this.res.end('Groupme test robot');
}
