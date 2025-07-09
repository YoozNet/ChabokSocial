import React, { useEffect, useRef, useState } from "react";
import DashboardHeader from "./DashboardHeader";
import FriendList from "./FriendList";
import ChatBox from "./ChatBox";
import { socket } from "./socket";

export function useHeartbeat() {
  useEffect(() => {
    let timer;

    function sendPing() {
      socket.emit("heartbeat", {
        user_id: window.dmz_user.id,
        online: true,
      });
      timer = setTimeout(sendPing, 20000);
    }

    function goOnline() {
      clearTimeout(timer);
      sendPing();
    }

    function goOffline() {
      socket.emit("heartbeat", {
        user_id: window.dmz_user.id,
        online: false,
      });
      clearTimeout(timer);
    }

    goOnline();

    document.addEventListener("visibilitychange", () => {
      if (document.visibilityState === "visible") {
        goOnline();
      } else {
        goOffline();
      }
    });

    window.addEventListener("beforeunload", () => {
      try {
        socket.emit("heartbeat", {
          user_id: window.dmz_user.id,
          online: false,
        });
      } catch {}
    });

    return () => {
      clearTimeout(timer);
      goOffline();
    };
  }, []);
}
export default function Dashboard() {
  const [activeChat, setActiveChat] = useState(null);
  const [activeChatAvatar, setActiveChatAvatar] = useState("");
  const [activeChatName, setActiveChatName] = useState("");
  const [isMobileChatOpen, setIsMobileChatOpen] = useState(false);
  const [isClosingChat, setIsClosingChat] = useState(false);

  useHeartbeat();

  return (
    <div>
      <DashboardHeader />
      <main className="flex h-screen pt-20 overflow-hidden w-full text-white">
        <FriendList
          user={window.dmz_user}
          onOpenChat={(id, name, avatar) => {
            if (activeChat && activeChat !== id) {
              setIsClosingChat(true);
              setTimeout(() => {
                setActiveChat(id);
                setActiveChatName(name);
                setActiveChatAvatar(avatar);
                setIsClosingChat(false);
                setIsMobileChatOpen(true);
              }, 300); 
            } else {
              setActiveChat(id);
              setActiveChatName(name);
              setActiveChatAvatar(avatar);
              setIsMobileChatOpen(true);
            }
        }}

        />
        {activeChat && !isClosingChat && (
          <ChatBox
            key={activeChat}
            friendId={activeChat}
            friendName={activeChatName}
            friendAvatar={activeChatAvatar}
            isMobileChatOpen={isMobileChatOpen}
            onClose={() => {
              setIsClosingChat(true);
              setTimeout(() => {
                setActiveChat(null);
                setIsMobileChatOpen(false);
                setIsClosingChat(false);
              }, 300); 
            }}
          />
        )}
      </main>
    </div>
  );
}