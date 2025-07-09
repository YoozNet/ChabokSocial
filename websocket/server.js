const http = require("http");
const { Server } = require("socket.io");
const Redis = require("ioredis");

// ====== HTTP server ======
const httpServer = http.createServer();

// ====== socket.io ======
const io = new Server(httpServer, {
  path: "/ws",
  transports: ["websocket"],
  cors: { origin: "*", methods: ["GET", "POST"] }
});

// ====== Redis publisher & subscriber ======
const pub = new Redis({
  host: "127.0.0.1",
  port: 6379,
  password: "" // password redis
});

const sub = new Redis({
  host: "127.0.0.1",
  port: 6379,
  password: "" // password redis
});

// ====== Subscribe all Redis channels ======
sub.psubscribe("*", (err, count) => {
  if (err) console.error(err);
  console.log("Subscribed to Redis channels");
});

// ====== Redis event relay ======
sub.on("pmessage", (pattern, channel, message) => {

  try {
    const data = JSON.parse(message);
    if (channel === "user.login") {
      io.emit("user.login", data);
    }
    if (channel === "user.presence") {
      io.emit("user.presence", data);
    }
    if (channel === "friend.updated") {
      io.emit("friend:list", data);
    }
    if (channel === "session:delete") {
      const { user_id, session_id } = data;
    }
    if (channel.startsWith("chabok_database_user.")) {
      const { event, data: innerPayload } = data;

      const payload = innerPayload || data;

      const userId = payload?.user_id || payload?.from_user_id;

      if (!userId) {
        console.warn(`[EMIT][PRIVATE] ⛔️ user_id یافت نشد برای event: ${event}`);
        return;
      }

      const userRoom = `user.${userId}`;

      if (event === "session.list") {
        io.to(userRoom).emit("session.list", data);
      }
      else if (event === "session.delete.response") {
        io.to(userRoom).emit("session.delete.response", data);
      }
      else if (event === "friend.list") {
        io.to(userRoom).emit("friend:list", payload);
      } 
      else if (event === "friend.request.response") {
        io.to(userRoom).emit("friend:request.response", payload);
      } 
      else if (event === "friend.accept.response") {
        io.to(userRoom).emit("friend:accept.response", payload);
      } 
      else if (event === "friend.decline.response") {
        io.to(userRoom).emit("friend:decline.response", payload);
      } 
      else if (event === "friend.remove.response") {
        io.to(userRoom).emit("friend:remove.response", payload);
      } 
      else if (event === "friend.favorite.response") {
        io.to(userRoom).emit("friend:favorite.response", payload);
      }

      else if (event === "chat.list.response") {
        io.to(userRoom).emit("chat.list.response", payload);
      } 
      else if (event === "chat.seen.response") {
        io.to(userRoom).emit("chat.seen.response", payload);
      } 
      else if (event === "chat.saved.response") {
        io.to(userRoom).emit("chat.saved.response", payload);
      } 
    }

  } catch (e) {
    console.error("Invalid JSON", e);
  }
});

// ====== Client connection ======
io.on("connection", (socket) => {
  console.log(`client connected: ${socket.id}`);
  socket.on("join-room", (roomName) => {
    if (typeof roomName === "string" && roomName.startsWith("user.")) {
      socket.join(roomName);
      console.log(`✅ ${socket.id} joined room: ${roomName}`);
      socket.emit("room.joined", { room: roomName, success: true });
    } else {
      socket.emit("room.joined", { room: roomName, success: false, error: "Room name not allowed" });
    }
  });
  socket.on("leave-room", (roomName) => {
    if (typeof roomName === "string" && roomName.startsWith("user.")) {
      socket.leave(roomName);
      console.log(`socket ${socket.id} left room ${roomName}`);
    }
  });

  socket.on("heartbeat", (data) => {
    pub.publish("heartbeat", JSON.stringify(data));
  });

  // friend events
  socket.on("friend:list", (data) => {
    pub.publish("friend:list", JSON.stringify({ user_id: data.user_id }));
  });

  socket.on("friend:request", (data) => {
    pub.publish("friend:request", JSON.stringify({
      from_user: data.from_user,
      username: data.username
    }));
  });

  socket.on("friend:accept", (data) => {
    pub.publish("friend:accept", JSON.stringify({
      user_id: data.user_id,
      id: data.id
    }));
  });

  socket.on("friend:decline", (data) => {
    pub.publish("friend:decline", JSON.stringify({
      user_id: data.user_id,
      id: data.id
    }));
  });

  socket.on("friend:remove", (data) => {
    pub.publish("friend:remove", JSON.stringify({
      user_id: data.user_id,
      id: data.id
    }));
  });

  socket.on("friend:favorite", (data) => {
    pub.publish("friend:favorite", JSON.stringify({
      user_id: data.user_id,
      id: data.id
    }));
  });

  // session events
  socket.on("session:list", (data) => {
    const userId = data.user_id;
    socket.join(`user.${userId}`);
    pub.publish("session:list", JSON.stringify({ user_id: userId }));
  });

  socket.on("session:delete", (data) => {
    const userId = data.user_id;
    socket.join(`user.${userId}`);
    pub.publish("session:delete", JSON.stringify({
      user_id: userId,
      session_id: data.id
    }));
  });

  // chat 
  socket.on("chat:list", (data) => {
    pub.publish("chat:list", JSON.stringify(data));
  });
  socket.on("chat:seen", (data) => {
    pub.publish("chat:seen", JSON.stringify(data));
  });
  socket.on("chat:saved", (data) => {
    pub.publish("chat:saved", JSON.stringify(data));
  });

  socket.on("disconnect", () => {
    console.log(`client disconnected: ${socket.id}`);
  });
});

// ====== Listen ======
httpServer.listen(6001, () => {
  console.log("Socket.io server listening on port 6001 (ws)");
});
