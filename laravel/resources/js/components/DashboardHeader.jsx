import React, { useEffect, useRef, useState } from "react";
import ProfileModal from "./ProfileModal";
import ActiveSessionsModal from "./ActiveSessionsModal";
import BackupCodesModal from "./BackupCodesModal";

export default function DashboardHeader() {
  const [message, setMessage] = useState("");
  const [showProfile, setShowProfile] = useState(false);
  const [user, setUser] = useState(window.dmz_user || {});
  const [messageType, setMessageType] = useState("success");
  const [showAvatarMenu, setShowAvatarMenu] = useState(false);
  const [showBackupModal, setShowBackupModal] = useState(false);
  const [showSessionModal, setShowSessionModal] = useState(false);

  const notifButtonRef = useRef();
  const avatarButtonRef = useRef();
  const messageModalRef = useRef();

  useEffect(() => {
    const handleClickOutside = (e) => {
      if (
        showAvatarMenu &&
        avatarButtonRef.current &&
        !avatarButtonRef.current.contains(e.target)
      ) {
        setShowAvatarMenu(false);
      }
      if (
        message &&
        messageModalRef.current &&
        !messageModalRef.current.contains(e.target)
      ) {
        setMessage("");
      }
    };
    document.addEventListener("click", handleClickOutside);
    return () => document.removeEventListener("click", handleClickOutside);
  }, [showAvatarMenu]);

  const showGlobalMessage = (text, type = "success") => {
    setMessage(text);
    setMessageType(type);
    setTimeout(() => {
      setMessage("");
    }, 10000); 
  };

  return (
    <div>
        <header className="flex justify-between items-center p-4 bg-gray-800 fixed top-0 w-full z-50 text-white">
          <div className="text-xl font-bold">پیامرسان چابُک</div>
            <div className="flex items-center space-x-4 space-x-reverse">
              <div className="relative flex items-center space-x-4 space-x-reverse" ref={avatarButtonRef}>
                <button
                  onClick={() => {
                    setShowAvatarMenu((prev) => !prev);
                  }}
                  className="w-12 h-12 rounded-full border-4 border-indigo-500 bg-gradient-to-tr from-indigo-600 to-purple-600 shadow-xl ring-2 ring-offset-2 ring-indigo-400 ring-offset-gray-800 overflow-hidden transform hover:scale-105 transition duration-300 hover:shadow-2xl hover:ring-indigo-300"
                >
                  <img
                    src="/profile/avatar"
                    alt="avatar"
                    className="w-full h-full object-cover"
                    onError={(e) => {
                      e.target.src = "/default.png";
                    }}
                  />
                </button>
                <span className="hidden sm:inline text-sm font-semibold transition duration-300 hover:text-indigo-300">
                  {user.name}
                </span>
                {showAvatarMenu && (
                  <div className="absolute top-full left-0 mt-3 w-56 rounded-2xl shadow-2xl bg-gradient-to-br from-gray-800 to-gray-700 border border-indigo-700 z-[9999] p-4 transition-all duration-300 origin-top scale-95">
                    <div className="block sm:hidden mb-2 text-xs text-gray-300">{user.name}</div>
                      <button
                        onClick={() => {
                          setShowSessionModal(true);
                          setShowAvatarMenu(false);
                        }}
                        className="w-full flex items-center justify-start space-x-2 space-x-reverse text-sm rounded p-2 hover:bg-gray-600 transition"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" className="w-4 h-4 text-indigo-400" fill="currentColor" viewBox="0 0 24 24">
                          <path d="M4 4h16v12H4zm0 14h16v2H4z" />
                        </svg>
                        <span>دستگاه‌های فعال</span>
                    </button>
                    <button
                      onClick={() => {
                        setShowBackupModal(true);
                        setShowAvatarMenu(false);
                      }}
                      className="w-full flex items-center justify-start space-x-2 space-x-reverse text-sm rounded p-2 hover:bg-gray-600 transition"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" className="w-4 h-4 text-indigo-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17 3H7a2 2 0 00-2 2v14l7-3 7 3V5a2 2 0 00-2-2z" />
                      </svg>
                      <span>کدهای بکاپ</span>
                    </button>

                    <button
                      onClick={() => {
                        setShowProfile(true);
                        setShowAvatarMenu(false);
                      }}
                      className="w-full flex items-center justify-start space-x-2 space-x-reverse text-sm rounded p-2 hover:bg-gray-600 transition"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" className="w-4 h-4 text-indigo-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4S8 5.79 8 8s1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                      </svg>
                      <span>ویرایش حساب کاربری</span>
                    </button>

                    <button
                      onClick={() => {
                        document.getElementById("logout-form").submit();
                      }}
                      className="w-full flex items-center justify-start space-x-2 space-x-reverse text-sm rounded p-2 hover:bg-gray-600 transition text-red-400"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" className="w-4 h-4 text-red-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M16 17v-1a4 4 0 00-3-3.87V12h-2v.13A4 4 0 008 16v1H5v2h14v-2h-3z" />
                      </svg>
                      <span>خروج</span>
                    </button>
                    <form id="logout-form" method="POST" action="/logout" className="hidden">
                      <input
                        type="hidden"
                        name="_token"
                        value={document.querySelector('meta[name="csrf-token"]').content}
                      />
                    </form>
                  </div>
                )}
              </div>
            </div>
            {showProfile && (
              <ProfileModal
                user={user}
                onClose={() => setShowProfile(false)}
                onUpdated={(result)=>{
                  if (result.success) {
                    setUser(prev=>({...prev, ...result.data}));
                  }
                }}
                showMessage={(text,type)=>{
                  setMessage(text);
                  setMessageType(type);
                }}
              />
              )}
        </header>
        {message && (
          <div className="fixed inset-0 bg-black/50 backdrop-blur-sm z-[9999] flex justify-center items-center text-white">
            <div ref={messageModalRef} className={`bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-4 relative animate-fade-in ${messageType === "success" ? "border-green-500" : "border-red-500"} border`}>
              <button
                onClick={() => setMessage("")}
                className="absolute top-2 left-2 text-white text-xl"
              >
                &times;
              </button>
              <div className="text-center text-sm font-bold mb-2">پیام سیستم</div>
              <div className="text-center text-xs text-gray-200">{message}</div>
            </div>
          </div>
        )}
        {showSessionModal && (
          <ActiveSessionsModal onClose={() => setShowSessionModal(false)} />
        )}
        {showBackupModal && (
          <BackupCodesModal onClose={() => setShowBackupModal(false)} />
        )}
    </div>
  );
}