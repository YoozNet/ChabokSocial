import { io } from "socket.io-client";

export const socket = io("wss://chabok.site", {
  path: "/ws",
  transports: ["websocket"],
  autoConnect: true,
  reconnection: true,
  reconnectionAttempts: Infinity,
  reconnectionDelay: 1000,
  pingInterval: 25000,  // هر ۲۵ ثانیه یک پینگ
  pingTimeout: 60000,   // فاصله‌ی زمانی مجاز برای دریافت pong: ۶۰ ثانیه
});

let heartbeatInterval = null;
socket.on("connect", () => {
  if (heartbeatInterval) clearInterval(heartbeatInterval);
  heartbeatInterval = setInterval(() => {
    socket.emit("heartbeat", { ts: Date.now() });
  }, 20000);
});

socket.on("disconnect", (reason) => {
  clearInterval(heartbeatInterval);
  if (reason === "io server disconnect") {
    socket.connect();
  }
});

socket.on("reconnect_attempt", (attempt) => {
  console.log(`🔄 Reconnect attempt #${attempt}`);
});

socket.on("reconnect", (attempt) => {
  console.log(`✅ Reconnected on attempt #${attempt}`);
});