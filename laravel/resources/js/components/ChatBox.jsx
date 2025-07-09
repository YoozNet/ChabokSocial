import React, { useEffect, useRef, useState } from "react";
import axios from "axios";
import ImageEditorModal from "./ImageEditorModal";
import { socket } from "./socket";
import useJoinUserRoom from "./useJoinUserRoom";

export default function ChatBox({ friendId, friendName,friendAvatar, isMobileChatOpen, onClose, isClosing }) {
    const [file, setFile] = useState(null);
    const [newMsg, setNewMsg] = useState("");
    const [messages, setMessages] = useState([]);
    const [replyTo, setReplyTo] = useState(null);
    const [sendError, setSendError] = useState(null);
    const [uploading, setUploading] = useState(false);
    const [activeMenu, setActiveMenu] = useState(null);
    const [editingImage, setEditingImage] = useState(null);
    const [uploadProgress, setUploadProgress] = useState(0);
    const [fileSelected, setFileSelected] = useState(false);
    const [confirmClear, setConfirmClear] = useState(false);
    const [modalImageSrc, setModalImageSrc] = useState(null);
    const [confirmDelete, setConfirmDelete] = useState(null);
    const [successMessage, setSuccessMessage] = useState(null);
    const [editingMessageId, setEditingMessageId] = useState(null);
    const [conversationMenu, setConversationMenu] = useState(false);
    const [friendStatus, setFriendStatus] = useState({is_online: false,last_seen: null});
    const [page, setPage] = useState(1);
    const [loadingOlder, setLoadingOlder] = useState(false);
    const [hasMore, setHasMore] = useState(true);
    
    const initialLoad = useRef(true);
    const menuWrapperRef = useRef(null);
    const textareaRefMobile = useRef(null);
    const textareaRefDesktop = useRef(null);
    const messagesEndRefMobile = useRef(null);
    const messagesEndRefDesktop = useRef(null);
    const messagesContainerRefMobile = useRef(null);
    const messagesContainerRefDesktop = useRef(null);

    useJoinUserRoom(socket, window.dmz_user?.id, !!friendId);
    useEffect(() => {
        let isMounted = true;

        const requestMessages = (pageNumber = 1) => {
            if (!friendId) return;
            if (!hasMore && pageNumber !== 1) return;
    
            setLoadingOlder(true);
    
            const payload = {
                from_user_id: window.dmz_user.id,
                to_user_id: friendId,
                page: pageNumber
            };
    
            if (friendId === window.dmz_user.id) {
                socket.emit("chat:saved", { from_user_id: window.dmz_user.id });
            } else {
                socket.emit("chat:list", payload);
            }
        };
        const fetchMessages = (payload) => {
            try {
                if (!isMounted) return;

                const messages = payload?.messages || [];
                const pageNumber = payload?.page || 1;
                const RemainingPages = payload?.remaining_pages;
                
                if (pageNumber === 1) {
                    setMessages(messages);
                    initialLoad.current = false;

                    setTimeout(() => {
                        messagesEndRefDesktop.current?.scrollIntoView({ behavior: "auto" });
                        messagesEndRefMobile.current?.scrollIntoView({ behavior: "auto" });
                    }, 100);
                } else {
                    setMessages(prev => [...messages, ...prev]);
                }

                if (RemainingPages <= 0) {
                    setHasMore(false);
                } else {
                    setHasMore(true);
                }

                const unreadIds = messages
                    .filter(m => m.from_user_id === friendId && m.is_read == 0)
                    .map(m => m.id);

                if (unreadIds.length > 0) {
                    socket.emit("chat:seen", {
                        from_user_id: window.dmz_user.id,
                        message_ids: unreadIds
                    });
                }
            } catch (err) {
                setSendError(`خطای دریافت شده: ${err}`);
            } finally {
                setLoadingOlder(false);
            }
        };
        
        socket.on("chat.list.response", fetchMessages);
        socket.on("chat.saved.response", fetchMessages);
        
        requestMessages(1);
        
        const timer = setInterval(() => {
            if (!loadingOlder) requestMessages(1);
        }, 1500);

        return () => {
            clearInterval(timer);
            socket.off("chat.list.response", fetchMessages);
            socket.off("chat.saved.response", fetchMessages);
            isMounted = false;
        };
    }, [friendId]);

    useEffect(() => {
        const fetchStatus = async () => {
        try {
            const res = await axios.get(`/friends/status/${friendId}`);
            if (res.data.ok) {
            setFriendStatus({
                is_online: res.data.is_online,
                last_seen: res.data.last_seen
            });
            }
        } catch (err) {
            console.error("خطای دریافت وضعیت دوست:", err);
            let serverMsg = "";
            if (err.response) {
                console.log("Response data:", err.response.data);
                serverMsg = typeof err.response.data === "object"
                ? JSON.stringify(err.response.data)
                : err.response.data;
            }
            const errText = `Axios error: ${err.message}` + (serverMsg ? `, Server: ${serverMsg}` : "");

            setSendError(errText);
        }
        };
        fetchStatus();
        const timer = setInterval(fetchStatus, 5000);
        return () => clearInterval(timer);
    }, [friendId]);

    useEffect(() => {
        setTimeout(() => {
            textareaRefDesktop.current?.focus();
            // textareaRefMobile.current?.focus();
        }, 200);
    }, [friendId]);

    useEffect(() => {
        initialLoad.current = true;
    }, [friendId]);

    useEffect(() => {
        if (replyTo) {
            textareaRefDesktop.current?.focus();
            textareaRefMobile.current?.focus();
        }
    }, [replyTo]);
    
    useEffect(() => {
        const handler = () => setActiveMenu(null);
        document.addEventListener("click", handler);
        return () => document.removeEventListener("click", handler);
    }, []);

    useEffect(() => {
        const handleOutsideClick = () => setConversationMenu(false);
        document.addEventListener("click", handleOutsideClick);
        return () => document.removeEventListener("click", handleOutsideClick);
    }, []);

    const handleSend = async () => {
        if (uploading) return;
        if (!newMsg.trim() && !file) return;
        try {
            setUploading(true);
            if (editingMessageId) {
                await axios.patch(`/chat/${editingMessageId}`, {
                    message: newMsg
                });
                setEditingMessageId(null);
            } else {
                const formData = new FormData();
                formData.append("to_user_id", friendId);

                if (newMsg) {
                    formData.append("message", newMsg); 
                }
                if (file) formData.append("attachment", file);
                if (replyTo) formData.append("reply_to", replyTo);
                await axios.post("/chat/send", formData, {
                    onUploadProgress: (e) => {
                        setUploadProgress(Math.round((e.loaded * 100) / e.total));
                    }
                });
            }
            setNewMsg("");
            setReplyTo(null);
            setFile(null);
            setUploadProgress(0);
            setUploading(false);
            setSendError(null);
        } catch (e) {
            console.error("خطای ارسال پیام:", e);
            let serverMsg = "";
            if (e.response) {
                console.log("Response data:", e.response.data);
                serverMsg = typeof e.response.data === "object" ? JSON.stringify(e.response.data) : e.response.data;
            }
            const errText = `Axios error: ${e.message}` + (serverMsg ? `, Server: ${serverMsg}` : "");
            setUploading(false);
            setSendError(errText);
        }
    };

    const formatDateTime = (dateStr) => {
        const dateObj = new Date(dateStr);

        const faDate = new Intl.DateTimeFormat("fa-IR", {
            year: "numeric",
            month: "2-digit",
            day: "2-digit",
        }).format(dateObj);

        const hh = String(dateObj.getHours()).padStart(2, "0");
        const mm = String(dateObj.getMinutes()).padStart(2, "0");

        return `${hh}:${mm} ${faDate}`;
    };

    const formatLastSeen = (dateStr) => {
        if (!dateStr) return "";
        const date = new Date(dateStr);
        const now = new Date();
        const diff = Math.floor((now - date) / 1000);
        if (diff < 60) return "لحظاتی پیش";
        if (diff < 3600) return `${Math.floor(diff / 60)} دقیقه پیش`;
        if (diff < 86400) return `${Math.floor(diff / 3600)} ساعت پیش`;
        return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2,"0")}-${String(date.getDate()).padStart(2,"0")} ${String(date.getHours()).padStart(2,"0")}:${String(date.getMinutes()).padStart(2,"0")}`;
    };

    const renderMessages = () => (
        messages.map((m) => (
        <div
            key={m.id}
            id={`msg-${m.id}`}
            className={`relative max-w-xs rounded-xl p-3 shadow
            ${
                m.from_user_id === friendId
                ? "bg-gray-700 self-end items-end"
                : "bg-green-600 self-start items-start my-message"
            }
            `}
        >
            {m.reply_to && (
            <div
                onClick={() => {
                const el = document.getElementById(`msg-${m.reply_to}`);
                if (el) {
                    el.scrollIntoView({ behavior: "smooth" });
                    el.classList.add("highlighted");
                    setTimeout(() => el.classList.remove("highlighted"), 1500);
                }
                }}
                className="text-xs p-1 bg-gray-800 border-r-4 border-green-600 rounded cursor-pointer mb-1"
            >
                پاسخ به:{" "}
                {m.reply?.message
                    ? (m.reply.message.length <= 20
                        ? m.reply.message
                        : m.reply.message.slice(0, 20) + "..."
                        )
                    : "مدیا"
                }
            </div>
            )}

            {m.attachment && (
                <img
                    src={`/attachment/${m.id}`}
                    className="w-48 rounded mt-1 cursor-pointer"
                    onClick={() => openImage(`/attachment/${m.id}`)}
                    alt=""
                />
            )}

            <div
                dir="rtl"
                style={{
                    whiteSpace: "pre-line",
                    wordBreak: "break-word",
                    overflowWrap: "anywhere",
            }}
            >
            {m.message}
            </div>
            {m.is_edited === 1 && (
                <small className="block text-[10px] text-yellow-400 mt-1">ویرایش شده</small>
            )}
            <small className="text-[10px] text-gray-400 flex justify-between mt-1">
            <span className="pl-10 text-white">{formatDateTime(m.created_at)}</span>
            {m.from_user_id !== friendId && (
                <span className="ml-1">
                    {m.is_read ? (
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        className="w-4 h-4 text-green-400 inline"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        strokeWidth="2"
                    >
                        <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        d="M5 13l4 4L19 7"
                        />
                        <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        d="M9 13l4 4L23 7"
                        />
                    </svg>
                    ) : (
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        className="w-4 h-4 text-green-400 inline"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        strokeWidth="2"
                    >
                        <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        d="M5 13l4 4L19 7"
                        />
                    </svg>
                    )}
                </span>
            )}
            </small>
            <div
                ref={menuWrapperRef}
                className="absolute bottom-1"
                style={{
                    [m.from_user_id === friendId ? "right" : "left"]: "-50px"
                }}
            >
                <button
                    onClick={(e) => {
                    e.stopPropagation();
                    setActiveMenu(activeMenu === m.id ? null : m.id);
                    }}
                    className="bg-gray-700/40 backdrop-blur rounded-full p-1 hover:bg-green-600 transition"
                >
                    <svg
                    className="w-4 h-4 text-white"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    >
                    <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth="2"
                        d="M6 12h.01M12 12h.01M18 12h.01"
                    />
                    </svg>
                </button>

                {activeMenu === m.id && (
                    <div
                        className="z-50 bg-gray-800 border border-gray-600 rounded shadow text-xs flex flex-col p-1 gap-1 absolute"
                        style={{
                            bottom: "-60px",
                            [m.from_user_id === friendId ? "right" : "left"]: "40px"
                        }}
                    >
                        <button
                            onClick={() => {
                                setReplyTo(m.id);
                                setActiveMenu(null);
                            }}
                            className="flex items-center gap-1 hover:bg-green-600 p-1 rounded"
                        >
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M3 10h12l-4-4m0 8l4-4H3" />
                            </svg>
                            ریپلای
                        </button>
                        {m.from_user_id === window.dmz_user.id && m.is_edited !== 1 && (
                            <button
                                onClick={() => {
                                setNewMsg(m.message ?? "");
                                setEditingMessageId(m.id);
                                setActiveMenu(null);
                                textareaRefDesktop.current?.focus();
                                }}
                                className="flex items-center gap-1 hover:bg-green-600 p-1 rounded"
                            >
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M15.232 5.232l3.536 3.536M4 13v4h4l10-10-4-4L4 13z" />
                                </svg>
                                ویرایش
                            </button>
                        )}
                        <button
                            onClick={() => setConfirmDelete(m)}
                            className="flex items-center gap-1 hover:bg-red-600 p-1 rounded"
                        >
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            حذف
                        </button>
                    </div>
                )}
            </div>

        </div>
        ))
    );

    const handleDeleteMessage = async (messageId) => {
        try {
            await axios.post(`/chat/delete`, { message_id: messageId });
        } catch (error) {
            const errText = error.response?.data?.error || "خطا در حذف پیام";
            setSendError(errText);
        } finally {
            setConfirmDelete(null); 
        }
    };

    const handleClearConversation = async () => {
        try {
            if (!friendId || typeof friendId !== "number") {
                setSendError("شناسه کاربر نامعتبر است");
                return;
            }
            await axios.post("/chat/clear-conversation",
                JSON.stringify({ friend_id: friendId }),
                {
                    headers: {
                    "Content-Type": "application/json",
                    },
                }
            );

            setMessages([]);
            setConversationMenu(null);
            setSuccessMessage("گفتگو با موفقیت پاک شد.");
        } catch (err) {
            const errText = err.response?.data?.error || "خطا در پاکسازی گفتگو";
            setSendError(errText);
        }
    };
    
    const openImage = (src) => { setModalImageSrc(src); };
    const closeImageModal = () => { setModalImageSrc(null); };
    return (
        <>
            <section className={`hidden md:flex flex-col w-full h-full bg-gray-900 transition-opacity duration-300 ${isClosing ? 'opacity-0' : 'opacity-100'}`}>
                <div className="flex items-center justify-between p-4 border-b border-gray-700">
                    <div className="flex items-center gap-2">
                        <img src={friendAvatar} className="w-10 h-10 rounded-full border border-green-500" alt="" />
                        <div>
                        <p className="font-semibold text-base">{friendName}</p>
                        <p className="text-xs text-green-400">
                            {friendStatus.is_online
                                ? "آنلاین"
                                : `آخرین بازدید: ${formatLastSeen(friendStatus.last_seen)}`}
                        </p>
                        </div>
                    </div>
                    <div className="flex items-center gap-2">
                        <div className="relative">
                            <button
                                onClick={(e) => {
                                e.stopPropagation();
                                setConversationMenu(!conversationMenu);
                                }}
                                className="w-10 h-10 flex justify-center items-center rounded-full hover:bg-gray-700 transition"
                                aria-label="گزینه‌ها"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" className="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M6 12h.01M12 12h.01M18 12h.01" />
                                </svg>
                            </button>
                            {conversationMenu && (
                                <div className="absolute left-0 top-12 bg-gray-800 border border-gray-600 rounded shadow p-2 z-50 w-48">
                                    <button
                                        onClick={() => setConfirmClear(true)}
                                        className="flex items-center justify-start gap-1 text-xs text-red-400 hover:text-red-600 w-full p-1 rounded hover:bg-red-700/20 transition"
                                    >
                                        <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        className="w-4 h-4 text-red-500"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        strokeWidth="2"
                                        >
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        <span className="truncate text-right flex-1">پاکسازی کل گفتگو</span>
                                    </button>
                                </div>
                            )}
                        </div>
                        <button
                            onClick={onClose}
                            className="w-10 h-10 flex justify-center items-center rounded-full hover:bg-red-700 transition"
                            aria-label="بستن"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                className="w-6 h-6 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                strokeWidth="2"
                            >
                                <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div
                    className="flex-1 overflow-y-auto p-4 flex flex-col gap-2"
                    ref={messagesContainerRefDesktop}
                >
                    <div className="flex justify-center items-center py-2" dir="rtl">
                        {loadingOlder && hasMore && (
                            <svg
                            className="animate-spin h-5 w-5 text-green-600"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            >
                            <circle
                                className="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                strokeWidth="4"
                            ></circle>
                            <path
                                className="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8v8H4z"
                            ></path>
                            </svg>
                        )}
                    </div>
                    {renderMessages()}
                    <div ref={messagesEndRefDesktop}></div>
                </div>
                {sendError && (
                    <div className="flex items-center justify-between p-2 bg-red-800 border-t border-red-600 rounded-t-xl border-x border-x-red-600 mb-2">
                        <div className="flex-1 text-xs text-red-200 truncate">
                            {sendError}
                        </div>
                        <button
                        onClick={() => setSendError(null)}
                        className="text-red-500 text-lg hover:bg-red-700 rounded-full w-6 h-6 flex justify-center items-center"
                        >
                        ×
                        </button>
                    </div>
                )}
                {successMessage && (
                    <div className="flex items-center justify-between p-2 bg-green-800 border-t border-green-600 rounded-t-xl border-x border-x-green-600 mb-2">
                        <div className="flex-1 text-xs text-green-200 truncate">{successMessage}</div>
                        <button
                        onClick={() => setSuccessMessage(null)}
                        className="text-green-300 text-lg hover:bg-green-700 rounded-full w-6 h-6 flex justify-center items-center"
                        >
                        ×
                        </button>
                    </div>
                )}
                {editingMessageId && (
                    <div className="flex items-center justify-between p-2 bg-yellow-800 border-t border-yellow-600 rounded-t-xl border-x border-x-yellow-600">
                        <div className="flex-1 text-xs text-yellow-200 truncate">
                        در حال ویرایش:
                        <span className="block text-[10px] text-yellow-400 mt-1">
                            {
                            (() => {
                                const editingMsg = messages.find(x => x.id === editingMessageId)?.message || "";
                                return editingMsg.length <= 20 ? editingMsg : editingMsg.slice(0,20) + "...";
                            })()
                            }
                        </span>
                        </div>
                        <button
                        onClick={() => {
                            setEditingMessageId(null);
                            setNewMsg("");
                        }}
                        className="text-yellow-500 text-lg hover:bg-yellow-700 rounded-full w-6 h-6 flex justify-center items-center"
                        >
                        ×
                        </button>
                    </div>
                )}
                {replyTo && (
                    <div className="flex items-center justify-between p-2 bg-gray-800 border-t border-gray-600 rounded-t-xl border-x border-x-gray-600">
                        <div className="flex-1 text-xs text-gray-200 truncate">
                        در حال پاسخ به:
                        <span className="block text-[10px] text-gray-400 mt-1">
                            {
                            (() => {
                                const replyMsg = messages.find(x => x.id === replyTo)?.message || "مدیا";
                                return replyMsg.length <= 20 ? replyMsg : replyMsg.slice(0,20) + "...";
                            })()
                            }
                        </span>
                        </div>
                        <button
                        onClick={() => setReplyTo(null)}
                        className="text-red-500 text-lg hover:bg-gray-700 rounded-full w-6 h-6 flex justify-center items-center"
                        >
                        ×
                        </button>
                    </div>
                )}
                {fileSelected && file && (
                    <div className="flex items-center justify-between gap-2 p-2 bg-gray-800 border border-gray-700 rounded mt-2 mb-2">
                        <div className="flex items-center gap-2 overflow-hidden w-full">
                            <img
                                src={URL.createObjectURL(file)}
                                alt="preview"
                                className="w-12 h-12 rounded object-cover border border-green-500 flex-shrink-0"
                            />
                            <div className="flex-1 text-xs text-gray-300 break-words">
                                {uploading ? (
                                <div className="flex items-center gap-2">
                                    در حال آپلود...
                                    <span className="text-green-400">{uploadProgress}%</span>
                                </div>
                                ) : (
                                <>یک تصویر آماده ارسال داری، کپشن بنویس یا ارسال بزن.</>
                                )}
                            </div>
                        </div>
                        {!uploading && (
                            <button
                                onClick={() => {
                                setFile(null);
                                setFileSelected(false);
                                }}
                                className="bg-red-600 text-white text-sm px-2 rounded"
                            >
                                ×
                            </button>
                        )}
                    </div>
                )}
                <div className="flex items-center gap-2 p-2 border-t border-gray-700 bg-gray-800 rounded-xl shadow-inner mx-2 my-2 relative">
                    <textarea
                        ref={textareaRefDesktop}
                        value={newMsg}
                        onChange={(e) => setNewMsg(e.target.value)}
                        placeholder="پیام..."
                        rows={1}
                        className="flex-1 bg-transparent p-2 resize-none focus:outline-none text-sm placeholder-gray-400"
                        style={{ fontFamily: `"Vazir", "Noto Color Emoji", "Apple Color Emoji", sans-serif` }}
                        onKeyDown={(e) => {
                        if (e.key === "Enter" && !e.shiftKey) {
                            e.preventDefault();
                            handleSend();
                        }
                        }}
                    />
                    <label
                        className={`w-8 h-8 flex justify-center items-center rounded-full 
                            ${fileSelected ? "bg-green-600" : "bg-gray-700 hover:bg-green-600"} 
                            cursor-pointer transition
                            ${editingMessageId ? "opacity-50 cursor-not-allowed" : ""}
                            `}
                    >
                        📎
                        <input
                        type="file"
                        accept="image/jpeg,image/jpg,image/png"
                        className="hidden"
                        onChange={(e) => {
                            if (editingMessageId) return;
                            if (e.target.files[0]) {
                                const imgUrl = URL.createObjectURL(e.target.files[0]);
                                setEditingImage(imgUrl); 
                            }
                        }}
                        />
                    </label>
                    <button
                        onClick={handleSend}
                        className="p-2 rounded-full disabled:opacity-50 transition transform active:scale-90 flex justify-center items-center"
                        disabled={(newMsg.trim() === "" && !file) || uploading}
                        aria-label="ارسال"
                    >
                        {uploading ? (
                            <svg
                            className="animate-spin h-5 w-5 text-green-600"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            >
                            <circle
                                className="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                strokeWidth="4"
                            ></circle>
                            <path
                                className="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8v8H4z"
                            ></path>
                            </svg>
                        ) : (
                            <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            className="w-6 h-6 transform rotate-180 text-green-600"
                            fill="green"
                            >
                            <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z" />
                            </svg>
                        )}
                    </button>
                </div>
            </section>

            <div className={`fixed inset-x-0 top-0 bottom-0 z-50 flex flex-col md:hidden bg-gray-900 transition-all duration-300
                ${isClosing ? 'opacity-0 translate-y-full' : isMobileChatOpen ? 'opacity-100 translate-y-0' : 'translate-y-full'}
            `}>
                <div className="flex justify-between items-center p-4 border-b border-gray-700">
                <div className="flex items-center gap-2">
                    <img
                        src={friendAvatar}
                        onError={(e) => { e.target.src = "/default.png"; }}
                        className="w-8 h-8 rounded-full border border-green-500"
                        alt=""
                    />
                    <div>
                    <p className="font-semibold text-base">{friendName}</p>
                    <p className="text-xs text-green-400">
                        {friendStatus.is_online
                            ? "آنلاین"
                            : `آخرین بازدید: ${formatLastSeen(friendStatus.last_seen)}`}
                    </p>
                    </div>
                </div>
                <div className="flex items-center gap-2">
                    <div className="relative">
                        <button
                            onClick={(e) => {
                            e.stopPropagation();
                            setConversationMenu(!conversationMenu);
                            }}
                            className="w-10 h-10 flex justify-center items-center rounded-full hover:bg-gray-700 transition"
                            aria-label="گزینه‌ها"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" className="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M6 12h.01M12 12h.01M18 12h.01" />
                            </svg>
                        </button>
                        {conversationMenu && (
                            <div className="absolute left-0 top-12 bg-gray-800 border border-gray-600 rounded shadow p-2 z-50 w-48">
                                <button
                                    onClick={() => setConfirmClear(true)}
                                    className="flex items-center justify-start gap-1 text-xs text-red-400 hover:text-red-600 w-full p-1 rounded hover:bg-red-700/20 transition"
                                >
                                    <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    className="w-4 h-4 text-red-500"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    strokeWidth="2"
                                    >
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    <span className="truncate text-right flex-1">پاکسازی کل گفتگو</span>
                                </button>
                            </div>
                        )}
                    </div>
                    <button
                        onClick={onClose}
                        className="w-10 h-10 flex justify-center items-center rounded-full hover:bg-red-700 transition"
                        aria-label="بستن"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            className="w-6 h-6 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            strokeWidth="2"
                        >
                            <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                </div>
                <div
                    className="flex-1 overflow-y-auto p-4 flex flex-col gap-2"
                    ref={messagesContainerRefMobile}
                >
                    <div className="flex justify-center items-center py-2" dir="rtl">
                        {loadingOlder && hasMore && (
                            <svg
                            className="animate-spin h-5 w-5 text-green-600"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            >
                            <circle
                                className="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                strokeWidth="4"
                            ></circle>
                            <path
                                className="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8v8H4z"
                            ></path>
                            </svg>
                        )}
                    </div>
                {renderMessages()}
                <div ref={messagesEndRefMobile}></div>
                </div>
                {sendError && (
                    <div className="flex items-center justify-between p-2 bg-red-800 border-t border-red-600 rounded-t-xl border-x border-x-red-600 mb-2">
                        <div className="flex-1 text-xs text-red-200 truncate">
                            {sendError}
                        </div>
                        <button
                        onClick={() => setSendError(null)}
                        className="text-red-500 text-lg hover:bg-red-700 rounded-full w-6 h-6 flex justify-center items-center"
                        >
                        ×
                        </button>
                    </div>
                )}
                {successMessage && (
                    <div className="flex items-center justify-between p-2 bg-green-800 border-t border-green-600 rounded-t-xl border-x border-x-green-600 mb-2">
                        <div className="flex-1 text-xs text-green-200 truncate">{successMessage}</div>
                        <button
                        onClick={() => setSuccessMessage(null)}
                        className="text-green-300 text-lg hover:bg-green-700 rounded-full w-6 h-6 flex justify-center items-center"
                        >
                        ×
                        </button>
                    </div>
                )}
                {editingMessageId && (
                    <div className="flex items-center justify-between p-2 bg-yellow-800 border-t border-yellow-600 rounded-t-xl border-x border-x-yellow-600">
                        <div className="flex-1 text-xs text-yellow-200 truncate">
                        در حال ویرایش:
                        <span className="block text-[10px] text-yellow-400 mt-1">
                            {
                            (() => {
                                const editingMsg = messages.find(x => x.id === editingMessageId)?.message || "";
                                return editingMsg.length <= 20 ? editingMsg : editingMsg.slice(0,20) + "...";
                            })()
                            }
                        </span>
                        </div>
                        <button
                        onClick={() => {
                            setEditingMessageId(null);
                            setNewMsg("");
                        }}
                        className="text-yellow-500 text-lg hover:bg-yellow-700 rounded-full w-6 h-6 flex justify-center items-center"
                        >
                        ×
                        </button>
                    </div>
                )}
                {replyTo && (
                    <div className="flex items-center justify-between p-2 bg-gray-800 border-t border-gray-600 rounded-t-xl border-x border-x-gray-600">
                        <div className="flex-1 text-xs text-gray-200 truncate">
                        در حال پاسخ به:
                        <span className="block text-[10px] text-gray-400 mt-1">
                            {
                            (() => {
                                const replyMsg = messages.find(x => x.id === replyTo)?.message || "مدیا";
                                return replyMsg.length <= 20 ? replyMsg : replyMsg.slice(0,20) + "...";
                            })()
                            }
                        </span>
                        </div>
                        <button
                        onClick={() => setReplyTo(null)}
                        className="text-red-500 text-lg hover:bg-gray-700 rounded-full w-6 h-6 flex justify-center items-center"
                        >
                        ×
                        </button>
                    </div>
                )}
                {fileSelected && file && (
                    <div className="flex items-center justify-between gap-2 p-2 bg-gray-800 border border-gray-700 rounded mt-2 mb-2">
                        <div className="flex items-center gap-2 overflow-hidden w-full">
                            <img
                                src={URL.createObjectURL(file)}
                                alt="preview"
                                className="w-12 h-12 rounded object-cover border border-green-500 flex-shrink-0"
                            />
                            <div className="flex-1 text-xs text-gray-300 break-words">
                                {uploading ? (
                                <div className="flex items-center gap-2">
                                    در حال آپلود...
                                    <span className="text-green-400">{uploadProgress}%</span>
                                </div>
                                ) : (
                                <>یک تصویر آماده ارسال داری، کپشن بنویس یا ارسال بزن.</>
                                )}
                            </div>
                        </div>
                        {!uploading && (
                            <button
                                onClick={() => {
                                setFile(null);
                                setFileSelected(false);
                                }}
                                className="bg-red-600 text-white text-sm px-2 rounded"
                            >
                                ×
                            </button>
                        )}
                    </div>
                )}
                <div className="flex items-center gap-2 p-2 border-t border-gray-700 bg-gray-800 rounded-xl shadow-inner mx-2 my-2 relative">
                    <textarea
                        ref={textareaRefMobile}
                        value={newMsg}
                        onChange={(e) => setNewMsg(e.target.value)}
                        placeholder="پیام..."
                        rows={1}
                        className="flex-1 bg-transparent p-2 resize-none focus:outline-none text-sm placeholder-gray-400"
                        style={{ fontFamily: `"Vazir", "Noto Color Emoji", "Apple Color Emoji", sans-serif` }}
                        onKeyDown={(e) => {
                        if (e.key === "Enter" && !e.shiftKey) {
                            e.preventDefault();
                            handleSend();
                        }
                        }}
                    />
                    <label
                        className={`w-8 h-8 flex justify-center items-center rounded-full 
                            ${fileSelected ? "bg-green-600" : "bg-gray-700 hover:bg-green-600"} 
                            cursor-pointer transition
                            ${editingMessageId ? "opacity-50 cursor-not-allowed" : ""}
                            `}
                    >
                        📎
                        <input
                        type="file"
                        accept="image/jpeg,image/jpg,image/png"
                        className="hidden"
                        onChange={(e) => {
                            if (editingMessageId) return;
                            if (e.target.files[0]) {
                                const imgUrl = URL.createObjectURL(e.target.files[0]);
                                setEditingImage(imgUrl); 
                            }
                        }}
                        />
                    </label>
                    <button
                        onClick={handleSend}
                        className="p-2 rounded-full disabled:opacity-50 transition transform active:scale-90 flex justify-center items-center"
                        disabled={(newMsg.trim() === "" && !file) || uploading}
                        aria-label="ارسال"
                    >
                        {uploading ? (
                            <svg
                            className="animate-spin h-5 w-5 text-green-600"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            >
                            <circle
                                className="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                strokeWidth="4"
                            ></circle>
                            <path
                                className="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8v8H4z"
                            ></path>
                            </svg>
                        ) : (
                            <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            className="w-6 h-6 transform rotate-180 text-green-600"
                            fill="green"
                            >
                            <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z" />
                            </svg>
                        )}
                    </button>
                </div>
            </div>
            {confirmClear && (
                <div
                    className="fixed inset-0 bg-black/70 backdrop-blur flex justify-center items-center z-[9999]"
                    onClick={() => setConfirmClear(false)}
                >
                    <div
                    className="bg-gray-800 rounded p-4 shadow-xl border border-gray-600 w-80"
                    onClick={(e) => e.stopPropagation()}
                    >
                    <h2 className="text-sm text-white mb-2 flex items-center gap-1">
                        <svg className="w-4 h-4 text-red-500" fill="none" stroke="currentColor" strokeWidth="2"
                            viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        تایید پاکسازی گفتگو
                    </h2>
                    <p className="text-xs text-gray-300 mb-4">آیا مطمئن هستید که می‌خواهید تمام پیام‌ها پاک شوند؟</p>
                    <div className="flex gap-2">
                        <button
                        className="flex-1 bg-red-700 hover:bg-red-800 text-white rounded p-1"
                        onClick={() => {
                            handleClearConversation();
                            setConfirmClear(false);
                        }}
                        >
                        پاکسازی
                        </button>
                        <button
                        className="flex-1 bg-gray-600 hover:bg-gray-700 text-white rounded p-1"
                        onClick={() => setConfirmClear(false)}
                        >
                        انصراف
                        </button>
                    </div>
                    </div>
                </div>
            )}     
            {confirmDelete && (
                <div className="fixed inset-0 bg-black/70 backdrop-blur-sm flex justify-center items-center z-[9999]">
                    <div className="bg-gray-800 rounded p-4 shadow-xl border border-gray-600 w-80">
                    <h2 className="text-sm text-white mb-2">آیا از حذف پیام مطمئن هستید؟</h2>
                    <div className="text-xs text-gray-300 p-2 bg-gray-700 rounded mb-4">
                        {confirmDelete.message?.slice(0,40) || "مدیا"}
                    </div>
                    <div className="flex justify-between gap-2">
                        <button
                        className="flex-1 bg-red-700 hover:bg-red-800 text-white rounded p-1"
                        onClick={() => handleDeleteMessage(confirmDelete.id)}
                        >
                        حذف
                        </button>
                        <button
                        className="flex-1 bg-gray-600 hover:bg-gray-700 text-white rounded p-1"
                        onClick={() => setConfirmDelete(null)}
                        >
                        انصراف
                        </button>
                    </div>
                    </div>
                </div>
            )}
            {modalImageSrc && (
                <div
                    className="fixed inset-0 bg-black/90 backdrop-blur-sm flex justify-center items-center z-[9999]"
                    onClick={closeImageModal} 
                >
                    <div
                    className="relative max-w-full max-h-full bg-gray-900 rounded-lg overflow-hidden shadow-xl border border-gray-700"
                    onClick={(e) => e.stopPropagation()} 
                    >
                    <div className="flex justify-between items-center p-2 bg-gray-800 border-b border-gray-700">
                        <span className="text-white text-sm">مشاهده تصویر</span>
                        <button
                        onClick={closeImageModal}
                        className="text-white text-xl hover:text-red-500"
                        >
                        ×
                        </button>
                    </div>
                    <div className="p-2 flex justify-center items-center">
                        <img
                        src={modalImageSrc}
                        alt="preview"
                        className="rounded shadow max-h-[80vh] max-w-[90vw] object-contain"
                        />
                    </div>
                    </div>
                </div>
            )}
            {editingImage && (
                <ImageEditorModal
                    src={editingImage}
                    onClose={() => {setEditingImage(null); }}
                    onSend={(editedBlob) => {
                        setFile(editedBlob);
                        setFileSelected(true);
                        setTimeout(() => {
                        setEditingImage(null); 
                        }, 50);
                        setTimeout(() => {
                        textareaRefDesktop.current?.focus();
                        textareaRefMobile.current?.focus();
                        }, 100);
                    }}
                />
            )}
        </>
    );
}
