const express = require('express');
const socket = require('socket.io');
const dontenv = require('dotenv');
const Redis = require('ioredis');

dontenv.config();

const app = express();
const port = process.env.WEBSOCKETS_PORT;
const server = app.listen(port, function () {
  console.log(`Listening on port ${ port }`);
});

const io = socket(server, {
  cors: {
    origin: process.env.CLIENT_URL.split(','),
    credentials: true
  },
  path: '/ws/' 
});

const redis = new Redis({
  host: 'redis_host',
  password: process.env.REDIS_PASSWORD
});

redis.psubscribe('*', (err, count) => {
  if (err) {
    console.log('error', err);
  }
});

redis.on('pmessage', (sub, channel, message) => {
  message = JSON.parse(message);
  io.emit(channel, {
    event: message.event,
    ...message.data
  });
});

io.on('connection', function (socket) {
  console.log('Connected');
});