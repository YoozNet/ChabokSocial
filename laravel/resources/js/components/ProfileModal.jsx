import React, { useState, useRef } from "react";
import Cropper from "react-easy-crop";
import { createImage, getCroppedImg } from "./utils/cropImage";

export default function ProfileModal({ user, onClose, onUpdated }) {
  const [name, setName] = useState(user.name || "");
  const [password, setPassword] = useState("");
  const [passwordConfirm, setPasswordConfirm] = useState("");
  const [totp, setTotp] = useState("");
  const [avatar, setAvatar] = useState(null);

  const [errors, setErrors] = useState({});
  const [loading, setLoading] = useState(false);

  const [localMessage, setLocalMessage] = useState("");
  const [localMessageType, setLocalMessageType] = useState("success");

  const [selectedFile, setSelectedFile] = useState(null);
  const [croppedAreaPixels, setCroppedAreaPixels] = useState(null);
  const [cropModalOpen, setCropModalOpen] = useState(false);
  const [crop, setCrop] = useState({ x: 0, y: 0 });
  const [zoom, setZoom] = useState(1);

  const modalRef = useRef(null);

  const onCropComplete = (croppedArea, croppedPixels) => {
    setCroppedAreaPixels(croppedPixels);
  };

  const handleSubmit = async () => {
    const newErrors = {};

    if (name === user.name && !password && !avatar) {
      newErrors.general = "هیچ تغییری برای ذخیره وارد نشده است.";
    }

    if (password && password !== passwordConfirm) {
      newErrors.passwordConfirm = "رمزها مطابقت ندارند.";
    }

    if (!totp.trim()) {
      newErrors.totp = "لطفا کد دو مرحله‌ای را وارد کنید.";
    } else if (!/^\d{6}$/.test(totp)) {
      newErrors.totp = "کد دو مرحله‌ای باید دقیقا ۶ رقم باشد.";
    }

    if (avatar) {
      if (avatar.size > 20 * 1024 * 1024) {
        newErrors.avatar = "حجم فایل نباید بیشتر از 20 مگابایت باشد.";
      }
      const img = new Image();
      img.src = URL.createObjectURL(avatar);
      await new Promise((resolve) => {
        img.onload = () => {
          if (img.width > 1024 || img.height > 1024) {
            newErrors.avatar = "ابعاد آواتار نباید بزرگتر از 1024 در 1024 باشد.";
          }
          resolve();
        };
      });
    }

    setErrors(newErrors);

    if (Object.keys(newErrors).length > 0) {
      const message = Object.values(newErrors).join(" | ");
      setLocalMessage(message);
      setLocalMessageType("error");
      modalRef.current?.scrollTo({ top: 0, behavior: "smooth" });
      return;
    }

    setLoading(true);

    try {
      let res;
      const token = document.querySelector("meta[name=csrf-token]")?.content;

      if (avatar) {
        const formData = new FormData();
        formData.append("name", name);
        formData.append("totp_code", totp);
        if (password) {
          formData.append("password", password);
          formData.append("password_confirmation", passwordConfirm);
        }
        formData.append("avatar", avatar);

        res = await fetch("/profile/update", {
          method: "POST",
          headers: {
            "X-CSRF-TOKEN": token,
          },
          body: formData,
        });
      } else {
        const payload = {
          name,
          totp_code: totp,
        };
        if (password) {
          payload.password = password;
          payload.password_confirmation = passwordConfirm;
        }

        res = await fetch("/profile/update", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": token,
            "Accept": "application/json",
          },
          body: JSON.stringify(payload),
        });
      }

      const data = await res.json();
      if (data.status === "ok") {
        onUpdated(data);
        setLocalMessage(data.message || "پروفایل با موفقیت ذخیره شد");
        setLocalMessageType("success");
      } else {
        setLocalMessage(data.message || "خطا در ذخیره پروفایل");
        setLocalMessageType("error");
        setErrors({ general: data.message || "خطا در ذخیره پروفایل" });
        modalRef.current?.scrollTo({ top: 0, behavior: "smooth" });
      }
    } catch {
      setLocalMessage("مشکل در ارتباط با سرور");
      setLocalMessageType("error");
      setErrors({ general: "مشکل در ارتباط با سرور" });
      modalRef.current?.scrollTo({ top: 0, behavior: "smooth" });
    } finally {
      setLoading(false);
    }
  };

  return (
    <>
      <div className="fixed inset-0 z-[9999] flex justify-center items-center bg-black/60 backdrop-blur animate-fade-in">
        <div
          ref={modalRef}
          className="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl shadow-2xl w-full max-w-md p-4 max-h-[90vh] overflow-y-auto border border-indigo-700 animate-scale-in"
        >
          <div className="flex justify-between items-center border-b border-gray-700 pb-2 mb-2">
            <h3 className="text-lg font-bold">ویرایش پروفایل</h3>
            <button
              onClick={onClose}
              className="text-xl hover:text-red-500 transition"
            >
              ×
            </button>
          </div>

          {localMessage && (
            <div
              className={`w-full text-center py-2 rounded mb-3 text-white text-xs ${
                localMessageType === "success" ? "bg-green-600" : "bg-red-600"
              } animate-pulse`}
            >
              {localMessage}
            </div>
          )}

          <div className="space-y-3 text-white">
            <div>
              <label className="text-sm mb-1 block">نام</label>
              {errors.name && (
                <div className="text-xs text-red-400 mb-1 animate-shake">
                  {errors.name}
                </div>
              )}
              <input
                className="w-full p-2 rounded-xl bg-gray-800 text-white focus:ring focus:ring-indigo-500"
                value={name}
                onChange={(e) => setName(e.target.value)}
              />
            </div>
            <div>
              <label className="text-sm mb-1 block">رمز جدید</label>
              {errors.password && (
                <div className="text-xs text-red-400 mb-1 animate-shake">
                  {errors.password}
                </div>
              )}
              <input
                type="password"
                className="w-full p-2 rounded-xl bg-gray-800 text-white focus:ring focus:ring-indigo-500"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
              />
            </div>
            <div>
              <label className="text-sm mb-1 block">تکرار رمز</label>
              {errors.passwordConfirm && (
                <div className="text-xs text-red-400 mb-1 animate-shake">
                  {errors.passwordConfirm}
                </div>
              )}
              <input
                type="password"
                className="w-full p-2 rounded-xl bg-gray-800 text-white focus:ring focus:ring-indigo-500"
                value={passwordConfirm}
                onChange={(e) => setPasswordConfirm(e.target.value)}
              />
            </div>
            <div>
              <label className="text-sm mb-1 block">کد دو مرحله‌ای</label>
              {errors.totp && (
                <div className="text-xs text-red-400 mb-1 animate-shake">
                  {errors.totp}
                </div>
              )}
              <input
                className="w-full p-2 rounded-xl bg-gray-800 text-white focus:ring focus:ring-indigo-500"
                placeholder="123456"
                value={totp}
                onChange={(e) => setTotp(e.target.value)}
              />
            </div>
            <div>
              <label className="text-sm mb-1 block">آواتار (حداکثر 1024x1024)</label>
              {errors.avatar && (
                <div className="text-xs text-red-400 mb-1 animate-shake">
                  {errors.avatar}
                </div>
              )}
              <input
                type="file"
                accept="image/jpeg,image/jpg,image/png"
                className="w-full"
                onChange={(e) => {
                  const file = e.target.files[0];
                  if (file) {
                    if (
                      !["image/jpeg", "image/png", "image/jpg"].includes(
                        file.type
                      )
                    ) {
                      setLocalMessage("فقط فرمت jpg یا png مجاز است");
                      setLocalMessageType("error");
                      return;
                    }
                    setSelectedFile(file);
                    setCropModalOpen(true);
                  }
                }}
              />
            </div>
          </div>

          <div className="flex justify-end gap-2 mt-4">
            <button
              disabled={loading}
              onClick={onClose}
              className="px-3 py-1 rounded-xl bg-red-600 hover:bg-red-700 transition"
            >
              بستن
            </button>
            <button
              disabled={loading}
              onClick={handleSubmit}
              className="px-3 py-1 rounded-xl bg-green-600 hover:bg-green-700 transition"
            >
              {loading ? "درحال ذخیره..." : "ذخیره"}
            </button>
          </div>
        </div>
      </div>

      {cropModalOpen && (
        <div className="fixed inset-0 z-[99999] flex justify-center items-center bg-black/70 backdrop-blur">
          <div className="bg-gray-900 rounded-2xl shadow-xl p-4 max-w-[90vw] max-h-[90vh]">
            <h3 className="text-white mb-2 text-center font-bold">برش تصویر</h3>
            <div className="relative w-[300px] h-[300px] bg-gray-800 rounded overflow-hidden">
              <Cropper
                image={selectedFile ? URL.createObjectURL(selectedFile) : null}
                crop={crop}
                zoom={zoom}
                aspect={1}
                onCropChange={setCrop}
                onCropComplete={onCropComplete}
                onZoomChange={setZoom}
              />
            </div>
            <div className="flex justify-end gap-2 mt-2">
              <button
                onClick={() => setCropModalOpen(false)}
                className="bg-red-600 px-3 py-1 rounded-xl"
              >
                انصراف
              </button>
              <button
                onClick={async () => {
                  const croppedImage = await getCroppedImg(
                    URL.createObjectURL(selectedFile),
                    croppedAreaPixels
                  );
                  const blob = await fetch(croppedImage).then((r) => r.blob());
                  const file = new File([blob], selectedFile.name, {
                    type: blob.type,
                  });
                  setAvatar(file);
                  setCropModalOpen(false);
                  modalRef.current?.scrollTo({ top: 0 });
                }}
                className="bg-green-600 px-3 py-1 rounded-xl"
              >
                تایید
              </button>
            </div>
          </div>
        </div>
      )}
    </>
  );
}
