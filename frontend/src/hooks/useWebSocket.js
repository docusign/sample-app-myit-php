import { useEffect, useRef, useState } from "react";
import { io } from "socket.io-client";

export const useWebSocket = (url) => {
  const socket = useRef(null);
  const [connected, setConnected] = useState(false);

  const connect = (channel, callback) => {

    socket.current = io(url, {
      transports: ['websocket'],
      path: '/ws/'
    });

    socket.current.on("connect_error", (err) => {
      console.log(`on connect_error due to ${err.message}`);
    });

    socket.current.on("connect", () => {
      setConnected(true);
    });

    socket.current.on("disconnect", () => {
      setConnected(false);
    });

    socket.current.on(channel, (data) => {
      callback(data);
    });
  };

  const disconnect = () => {
    if (socket.current) {
      socket.current.disconnect();
      socket.current = null;
    }
  };

  useEffect(() => () => disconnect(), []);

  return [connect, disconnect, connected];
};
