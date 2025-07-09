import { useEffect } from "react";

export default function useJoinUserRoom(socket, userId, active = true) {
  useEffect(() => {
    if (!socket || !userId || !active) return;

    const roomName = `user.${userId}`;

    const joinRoom = () => {
      socket.emit("join-room", roomName);
    };

    if (socket.connected) {
      joinRoom();
    }

    socket.on("connect", joinRoom);
    socket.on("reconnect", joinRoom);

    return () => {
      socket.off("connect", joinRoom);
      socket.off("reconnect", joinRoom);

      socket.emit("leave-room", roomName); 
    };
  }, [socket, userId, active]);
}
