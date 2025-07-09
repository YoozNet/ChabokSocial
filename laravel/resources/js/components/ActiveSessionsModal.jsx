import { useEffect, useState } from "react";
import { socket } from "./socket";
import useJoinUserRoom from "./useJoinUserRoom";

export default function ActiveSessionsModal({ onClose }) {
  const [sessions, setSessions] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [confirmDelete, setConfirmDelete] = useState(null);
  const [deleteError, setDeleteError] = useState("");
  const [deleting, setDeleting] = useState(false);

  useJoinUserRoom(socket, window.dmz_user?.id, true);
  useEffect(() => {
    socket.on("session.list", (data) => {
      const payload = data?.data?.sessions;

      if (!Array.isArray(payload)) {
        setError("دریافت لیست سشن‌ها با خطا مواجه شد");
        setSessions([]);
        setLoading(false);
        return;
      }

      const seen = new Set();
      const unique = payload.filter((s) => {
        const key = `${s.device}_${s.ip_address}_${s.session_id}`;
        if (seen.has(key)) return false;
        seen.add(key);
        return true;
      });

      setSessions(unique);
      setLoading(false);
      setError("");
    });

    socket.on("session.delete.response", (packet) => {
      const payload = packet?.data || {};
      const rawMessage = payload?.message;
      const fallbackMessage = "خطا در حذف سشن";

      const userMessage = (typeof rawMessage === "string" && rawMessage.trim())
        ? rawMessage
        : fallbackMessage;

      if (payload.success) {
        setConfirmDelete(null);
        setDeleteError("");
        setDeleting(false);

        socket.emit("session:list", {
          user_id: window.dmz_user.id,
        });
      } else {
        setDeleteError(userMessage);
        setDeleting(false);
      }
    });


    socket.once("disconnect", (reason) => {
      console.warn("[SOCKET] Socket disconnected:", reason);
    });

    return () => {
      socket.off("connect");
      socket.off("disconnect");
      socket.off("session.list");
      socket.off("session.error");
      socket.off("session.delete.response");
    };
  }, []);

  const confirmDeleteSession = (session) => {
    setConfirmDelete(session);
    setDeleteError("");
  };

  const performDelete = (id) => {
    setDeleting(true);
    socket.emit("session:delete", {
      user_id: window.dmz_user.id,
      id: id,
    });
  };

  return (
    <>
      <div
        className="fixed inset-0 bg-black/50 backdrop-blur z-[9999] flex justify-center items-center"
        onClick={onClose}
      >
        <div
          className="bg-gray-800 rounded-xl p-4 w-full max-w-md relative shadow-xl animate-fade-in text-white"
          onClick={(e) => e.stopPropagation()}
        >
          <button
            onClick={onClose}
            className="absolute top-2 left-2 text-xl text-white"
          >
            &times;
          </button>
          <h2 className="text-center font-bold text-lg mb-4">دستگاه‌های فعال</h2>

          {loading ? (
            <div className="text-center text-xs text-gray-400">در حال بارگذاری...</div>
          ) : error ? (
            <div className="text-center text-xs text-red-400">{error}</div>
          ) : (
            <ul className="divide-y divide-gray-600 text-xs max-h-80 overflow-y-auto">
              {sessions.length === 0 && (
                <li className="text-center text-gray-400 py-2">هیچ سشن فعالی موجود نیست</li>
              )}
              {sessions.map((s) => (
                <li key={s.id} className="flex justify-between items-center py-2 px-1">
                  <div>
                    <div>{s.device} - {s.ip_address}</div>
                    <div className="text-gray-400 text-[10px]">
                      {s.is_online ? "آنلاین" : "آفلاین"} - {new Date(s.last_activity).toLocaleString("fa-IR")}
                    </div>
                  </div>
                  <button
                    onClick={() => confirmDeleteSession(s)}
                    className="bg-red-600 rounded px-2 py-0.5 text-xs disabled:opacity-50"
                    disabled={deleting}
                  >
                    {deleting ? "درحال حذف..." : "حذف"}
                  </button>
                </li>
              ))}
            </ul>
          )}
        </div>
      </div>

      {confirmDelete && (
        <div
          className="fixed inset-0 bg-black/60 backdrop-blur z-[10000] flex justify-center items-center"
          onClick={() => setConfirmDelete(null)}
        >
          <div
            className="bg-gray-800 rounded-xl p-4 w-full max-w-md relative shadow-xl text-white animate-scale-up"
            onClick={(e) => e.stopPropagation()}
          >
            <button
              onClick={() => setConfirmDelete(null)}
              className="absolute top-2 left-2 text-xl text-white"
            >
              &times;
            </button>
            <h2 className="text-center font-bold text-lg mb-4">تایید حذف</h2>
            <div className="text-xs mb-2">
              <strong>دستگاه:</strong> {confirmDelete.device}<br />
              <strong>آی‌پی:</strong> {confirmDelete.ip_address}<br />
              <strong>وضعیت:</strong> {confirmDelete.is_online ? "آنلاین" : "آفلاین"}<br />
              <strong>آخرین فعالیت:</strong> {new Date(confirmDelete.last_activity).toLocaleString("fa-IR")}
            </div>
            {deleteError && (
              <div className="text-red-400 text-xs mb-2">{deleteError}</div>
            )}
            <div className="flex gap-2 justify-end mt-4">
              <button
                onClick={() => setConfirmDelete(null)}
                className="px-3 py-1 rounded bg-gray-600 hover:bg-gray-700 text-xs"
                disabled={deleting}
              >
                خیر
              </button>
              <button
                onClick={() => performDelete(confirmDelete.id)}
                className="px-3 py-1 rounded bg-red-600 hover:bg-red-700 text-xs disabled:opacity-50"
                disabled={deleting}
              >
                {deleting ? "درحال حذف..." : "بله، حذف شود"}
              </button>
            </div>
          </div>
        </div>
      )}
    </>
  );
}
