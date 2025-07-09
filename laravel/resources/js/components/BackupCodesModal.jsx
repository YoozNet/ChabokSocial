import { useEffect, useRef, useState } from "react";

export default function BackupCodesModal({ onClose }) {
  const [codes, setCodes] = useState({});
  const [loading, setLoading] = useState(false);
  const [initLoading, setInitLoading] = useState(true);
  const modalRef = useRef();

  const fetchCodes = () => {
    setInitLoading(true);
    fetch('/backup-codes', {
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
      }
    })
      .then(res => res.json())
      .then(data => {
        setCodes(data.slots || {});
        setInitLoading(false);
      });
  };

  useEffect(() => {
    fetchCodes();
  }, []);

  useEffect(() => {
    const handler = (e) => {
      if (modalRef.current && !modalRef.current.contains(e.target)) {
        onClose();
      }
    };
    const timer = setTimeout(() => {
      document.addEventListener("mousedown", handler);
    }, 200);
    return () => {
      clearTimeout(timer);
      document.removeEventListener("mousedown", handler);
    };
  }, []);

  const generateCodes = () => {
    setLoading(true);
    fetch('/backup-codes/generate', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
      }
    })
      .then(async res => {
        if(res.status === 409){
          alert("کدهای بکاپ قبلاً ساخته شده‌اند.");
          return;
        }
        const data = await res.json();
        setCodes(data.slots);
      })
      .finally(() => {
        setLoading(false);
      });
  };

  const regenerateCodes = () => {
    setLoading(true);
    fetch('/backup-codes/regenerate', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
      }
    })
      .then(res => res.json())
      .then(data => {
        setCodes(data.slots);
      })
      .finally(() => {
        setLoading(false);
      });
  };

  const downloadTxt = () => {
    const allCodes = Object.entries(codes)
      .map(([slot, versions]) => {
        return `${slot}: ${versions[0]?.code || "-"}`;
      })
      .join("\n");
    const blob = new Blob([allCodes], { type: "text/plain;charset=utf-8" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "backup_codes.txt";
    a.click();
    URL.revokeObjectURL(url);
  };

  const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text).then(() => {
      alert(`کد ${text} کپی شد`);
    });
  };

  const flattened = [];
  for (let i = 1; i <= 10; i++) {
    const versions = codes[i] || [];
    const active = versions.find(v => v.status === "active");
    const expired = versions.find(v => v.status === "expired");
    flattened.push({
      slot: i,
      active,
      expired
    });
  }
  const rows = [];
  for (let i = 0; i < flattened.length; i += 3) {
    rows.push(flattened.slice(i, i + 3));
  }

  return (
    <div className="fixed inset-0 bg-black/50 backdrop-blur-sm z-[9999] flex justify-center items-center">
      <div
        ref={modalRef}
        className="bg-gray-800 text-white rounded-xl shadow-xl p-4 max-w-2xl w-full relative animate-fade-in"
      >
        <button
          onClick={onClose}
          className="absolute top-2 left-2 text-xl"
        >
          &times;
        </button>
        <div className="text-sm font-bold mb-2">کدهای بکاپ</div>
        <div className="text-xs text-gray-300 mb-4">
          از این کدها در صورتی که دسترسی به تایید دو مرحله‌ای را از دست دادید استفاده کنید.
        </div>
        <div className="flex gap-2 mb-4 flex-wrap">
          <button
            onClick={fetchCodes}
            disabled={initLoading}
            className="bg-purple-600 px-4 py-2 rounded hover:bg-purple-700 transition text-xs"
          >
            بروزرسانی
          </button>
          {initLoading ? (
            <span className="text-xs text-gray-400">در حال بارگذاری...</span>
          ) : Object.keys(codes).length === 0 ? (
            <button
              onClick={generateCodes}
              disabled={loading}
              className="bg-green-600 px-4 py-2 rounded hover:bg-green-700 transition text-xs"
            >
              {loading ? "در حال تولید..." : "تولید کدها"}
            </button>
          ) : (
            <button
              onClick={regenerateCodes}
              disabled={loading}
              className="bg-yellow-600 px-4 py-2 rounded hover:bg-yellow-700 transition text-xs"
            >
              {loading ? "در حال تولید..." : "تولید مجدد"}
            </button>
          )}
          <button
            onClick={downloadTxt}
            disabled={Object.keys(codes).length === 0}
            className="bg-blue-600 px-4 py-2 rounded hover:bg-blue-700 transition text-xs"
          >
            دانلود فایل
          </button>
        </div>
        <div className="space-y-2 text-xs font-mono">
          {rows.map((row, idx) => (
            <div key={idx} className="flex justify-between gap-2">
              {row.map(({ slot, active, expired }) => (
                <div
                  key={slot}
                  className="flex flex-col items-center flex-1 cursor-pointer"
                  onClick={() => active && copyToClipboard(active.code)}
                >
                  {active ? (
                    <span className="text-green-400 text-sm">{`${slot}. ${active.code}`}</span>
                  ) : (
                    <span className="text-gray-500">{`${slot}. -`}</span>
                  )}
                  {expired && (
                    <span className="text-red-500 text-xs line-through mt-1">
                      {expired.code}
                    </span>
                  )}
                </div>
              ))}
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}
