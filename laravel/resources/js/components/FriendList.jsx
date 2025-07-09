import React, { useEffect, useRef, useState, useCallback } from "react";
import { socket } from "./socket";
import useJoinUserRoom from "./useJoinUserRoom";

export default function FriendList({ user, onOpenChat }) {
  const [friends, setFriends] = useState([]);
  const [pending, setPending] = useState([]);
  const [sent, setSent] = useState([]);
  const [showContactsModal, setShowContactsModal] = useState(false);
  const [searchUsername, setSearchUsername] = useState("");
  const [requestMessage, setRequestMessage] = useState({ text: "", type: "" });
  const [friendToDelete, setFriendToDelete] = useState(null);

  useJoinUserRoom(socket, user.id, true);

  const requestFriendList = () => {
    socket.emit("friend:list", { user_id: user.id });
  };

  useEffect(() => {
      requestFriendList(); 
      const interval = setInterval(requestFriendList, 10000);

      socket.on("friend:list", (payload) => {
        const data = payload?.data ?? payload;

        setFriends(data.friends || []);
        setPending(data.pending || []);
        setSent(data.sent || []);
      });


      return () => {
        socket.off("friend:list");
        clearInterval(interval);
      };
  }, [user.id]);

  const handleSendRequest = () => {
    if (!searchUsername) {
      setRequestMessage({ text: "یوزرنیم را وارد کنید", type: "error" });
      return;
    }

    const payload = {
      username: searchUsername,
      from_user: user.id,
      user_id: user.id 
    };

    socket.emit("friend:request", payload);
  };


  useEffect(() => {
    socket.on("friend:request.response", (data) => {
      if (data.success) {
        setRequestMessage({ text: "درخواست دوستی ارسال شد", type: "success" });
        setSearchUsername("");
      } else {
        setRequestMessage({ text: data.message || "خطا", type: "error" });
      }
      socket.emit("friend:list", { user_id: user.id });
    });
    return () => socket.off("friend:request.response");
  }, [user.id]);

  const acceptRequest = (id) => {
    socket.emit("friend:accept", { id, user_id: user.id });
  };
  useEffect(() => {
    socket.on("friend:accept.response", (data) => {
      if (data.success) {
        socket.emit("friend:list", { user_id: user.id });
      }
    });
    return () => socket.off("friend:accept.response");
  }, [user.id]);

  const declineRequest = (id) => {
    socket.emit("friend:decline", { id, user_id: user.id });
  };
  useEffect(() => {
    socket.on("friend:decline.response", (data) => {
      if (data.success) {
        socket.emit("friend:list", { user_id: user.id });
      }
    });
    return () => socket.off("friend:decline.response");
  }, [user.id]);

  const removeFriend = (id) => {
    socket.emit("friend:remove", { id, user_id: user.id });
  };
  useEffect(() => {
    socket.on("friend:remove.response", (data) => {
      if (data.success) {
        socket.emit("friend:list", { user_id: user.id });
      }
    });
    return () => socket.off("friend:remove.response");
  }, [user.id]);

  const toggleFavorite = (id) => {
    socket.emit("friend:favorite", { id, user_id: user.id });
  };
  useEffect(() => {
    socket.on("friend:favorite.response", (data) => {
      if (data.success) {
        socket.emit("friend:list", { user_id: user.id });
      } else {
        alert(data.error || "خطا در پین");
      }
    });
    return () => socket.off("friend:favorite.response");
  }, [user.id]);
  const handleOpenChat = useCallback(
    async (id, name) => {
      const avatarUrl = `/avatar/${id}`;
      try {
        const res = await fetch(avatarUrl, { method: "HEAD" });
        if (res.ok) onOpenChat(id, name, avatarUrl);
        else onOpenChat(id, name, "/default.png");
      } catch {
        onOpenChat(id, name, "/default.png");
      }
    },
    [onOpenChat]
  );


  return (
      <aside className="w-full md:w-1/3 lg:w-1/4 bg-gradient-to-br from-gray-900 to-gray-800/80 backdrop-blur-xl border-l border-gray-700 shadow-xl overflow-y-auto p-2">
        <div className="flex items-center justify-between p-4 border-gray-700">
          <h2 className="font-bold text-lg">گفتگوها</h2>
          <div className="flex items-center gap-2">
            <button
              onClick={requestFriendList}
              className="w-9 h-9 rounded-full flex justify-center items-center bg-gradient-to-br from-purple-600 to-indigo-700 hover:scale-105 transition shadow-md"
              aria-label="بروزرسانی"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                className="w-5 h-5 text-white"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                strokeWidth="2"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  d="M4 4v6h6M20 20v-6h-6M4 20l16-16"
                />
              </svg>
            </button>
            <button
              onClick={() => setShowContactsModal(true)}
              className="w-9 h-9 rounded-full flex justify-center items-center bg-gradient-to-br from-green-500 to-emerald-700 hover:scale-105 transition shadow-md"
              aria-label="ارسال درخواست دوستی"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                className="w-5 h-5 text-white"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                strokeWidth="2"
              >
                <path strokeLinecap="round" strokeLinejoin="round" d="M12 4v16m8-8H4" />
              </svg>
            </button>
          </div>
        </div>

        {pending.length > 0 && (
          <div className="mt-4 border-t border-gray-700 pt-4">
            <h3 className="text-sm font-bold mb-2">درخواست‌های دوستی</h3>
            {pending.map((p) => (
              <div
                key={p.id}
                className="flex justify-between items-center rounded-xl bg-gray-700 p-2 mb-2"
              >
                <div className="flex gap-3 items-center">
                  <img
                    src={`/avatar/${p.user.id}`}
                    onError={(e) => { e.target.src = "/default.png"; }}
                    className="w-10 h-10 rounded-full"
                  />
                  <div className="flex flex-col">
                    <span className="text-xs">{p.user.name}</span>
                    <span className="text-[10px] text-yellow-400">
                      این کاربر درخواست داده، قبول می‌کنی؟
                    </span>
                  </div>
                </div>
                <div className="flex flex-col gap-1 items-end">
                  <button
                    onClick={() => acceptRequest(p.id)}
                    className="bg-green-600 rounded px-2 py-0.5 text-xs text-white"
                  >
                    پذیرش
                  </button>
                  <button
                    onClick={() => declineRequest(p.id)}
                    className="bg-red-600 rounded px-2 py-0.5 text-xs text-white"
                  >
                    رد
                  </button>
                </div>
              </div>
            ))}
          </div>
        )}
        {sent.length > 0 && (
          <div className="mt-4 border-t border-gray-700 pt-4">
            <h3 className="text-sm font-bold mb-2">درخواست‌های ارسال‌شده</h3>
            {sent.map((s) => (
              <div
                key={s.id}
                className="flex justify-between items-center rounded-xl bg-gray-700 p-2 mb-2"
              >
                <div className="flex gap-3 items-center">
                  <img
                    src={`/avatar/${s.friend.id}`}
                    onError={(e) => { e.target.src = "/default.png"; }}
                    className="w-10 h-10 rounded-full"
                  />
                  <div className="flex flex-col">
                    <span className="text-xs">{s.friend.name}</span>
                    <span className={`text-[10px] ${
                      s.status === "pending" ? "text-yellow-400" : "text-red-400"
                    }`}>
                      {s.status === "pending" ? "در انتظار تایید" : "رد شده"}
                    </span>
                  </div>
                </div>
              </div>
            ))}
          </div>
        )}
        <ul className="space-y-2 mt-2 border-t">
          <li
            key="saved"
            onClick={() => handleOpenChat(user.id, "ذخیره شده‌ها")}
            className="flex justify-between items-center p-3 rounded-xl hover:bg-gray-700/50 transition cursor-pointer"
          >
            <div className="flex gap-3 items-center">
              <div className="relative">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  className="w-12 h-12 p-2 rounded-full border border-green-500 bg-gradient-to-br from-blue-600 to-indigo-600 text-white"
                  fill="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path d="M6 4a2 2 0 0 0-2 2v14l8-4 8 4V6a2 2 0 0 0-2-2H6z" />
                </svg>
              </div>
              <div className="flex flex-col">
                <span className="text-sm font-semibold">ذخیره شده‌ها</span>
                <span className="text-xs text-green-400">پیام های شخصی</span>
              </div>
            </div>
          </li>
          {[...friends]
            .sort((a, b) => {
              if (b.is_favorite !== a.is_favorite) return b.is_favorite - a.is_favorite;
              if (b.is_online !== a.is_online) return b.is_online - a.is_online;
              return b.unread_count - a.unread_count;
            })
            .map((f) => (
              <li key={f.id} className="flex justify-between items-center p-3 rounded-xl hover:bg-gray-700/50 transition cursor-pointer">
                <div className="flex gap-3 items-center">
                  <div className="relative">
                    <img
                      src={`/avatar/${f.id}`}
                      onError={(e) => { e.target.src = "/default.png"; }}
                      className="w-12 h-12 rounded-full border border-green-500"
                    />
                    <span className={`absolute bottom-0 left-0 w-3 h-3 rounded-full ring-2 ring-gray-800 ${f.is_online ? "bg-green-500 animate-pulse" : "bg-gray-500"}`}></span>
                  </div>
                  <div className="flex flex-col">
                    <div className="flex items-center gap-1 flex-wrap">
                      <span className="text-sm font-semibold">{f.name}</span>
                      {f.is_favorite === 1 && (
                        <span className="bg-yellow-500 text-xs text-white rounded px-1">پین</span>
                      )}
                    </div>
                    <span className={`text-xs ${f.is_online ? "text-green-400" : "text-gray-400"}`}>
                      {f.is_online ? "آنلاین" : "آفلاین"}
                    </span>
                    {f.unread_count > 0 && (
                      <span className="bg-red-600 text-white text-[10px] px-2 rounded-full animate-bounce shadow mt-1">
                        {f.unread_count} پیام
                      </span>
                    )}
                  </div>
                </div>
                <div className="flex gap-1">
                  <button onClick={() => handleOpenChat(f.id, f.name)} className="bg-green-600 px-2 rounded text-xs"> چت </button>
                  <button onClick={() => toggleFavorite(f.id)} className={`px-2 rounded text-xs ${f.is_favorite ? "bg-yellow-500" : "bg-gray-600"}`}> {f.is_favorite ? "آنپین" : "پین"} </button>
                  <button onClick={() => setFriendToDelete(f)} className="bg-red-600 px-2 rounded text-xs" > حذف </button>
                </div>
              </li>
            ))}
        </ul>
        {showContactsModal && (
          <div className="fixed inset-0 bg-black/50 backdrop-blur z-[9999] flex justify-center items-center">
            <div className="bg-gray-800 rounded-2xl p-4 w-80 max-w-full text-white space-y-4 shadow-xl animate-scale-up">
              <div className="text-center font-bold text-lg">ارسال درخواست دوستی</div>
              {requestMessage.text && (
                <div
                  className={`text-xs text-center ${
                    requestMessage.type === "success" ? "text-green-400" : "text-red-400"
                  }`}
                >
                  {requestMessage.text}
                </div>
              )}
              <input
                type="text"
                value={searchUsername}
                onChange={(e) => setSearchUsername(e.target.value)}
                className="w-full rounded px-2 py-1 text-black"
                placeholder="یوزرنیم کاربر"
              />
              <div className="flex gap-2 justify-end">
                <button
                  onClick={() => setShowContactsModal(false)}
                  className="px-3 py-1 rounded bg-gray-600 hover:bg-gray-700 text-sm"
                >
                  بستن
                </button>
                <button
                  onClick={handleSendRequest}
                  className="px-3 py-1 rounded bg-green-600 hover:bg-green-700 text-sm"
                >
                  ارسال
                </button>
              </div>
            </div>
          </div>
        )}
        {friendToDelete && (
          <div className="fixed inset-0 bg-black/50 backdrop-blur z-[9999] flex justify-center items-center">
            <div className="bg-gray-800 rounded-2xl p-4 w-80 max-w-full text-white space-y-4 shadow-xl animate-scale-up">
              <div className="text-center font-bold text-lg">
                حذف دوستی
              </div>
              <div className="text-center text-sm">
                آیا مطمئن هستید می‌خواهید {friendToDelete.name} را حذف کنید؟
              </div>
              <div className="flex gap-2 justify-end">
                <button
                  onClick={() => setFriendToDelete(null)}
                  className="px-3 py-1 rounded bg-gray-600 hover:bg-gray-700 text-sm"
                >
                  خیر
                </button>
                <button
                  onClick={() => {
                    removeFriend(friendToDelete.id);
                    setFriendToDelete(null);
                  }}
                  className="px-3 py-1 rounded bg-red-600 hover:bg-red-700 text-sm"
                >
                  بله، حذف شود
                </button>
              </div>
            </div>
          </div>
        )}
      </aside>
  );
}