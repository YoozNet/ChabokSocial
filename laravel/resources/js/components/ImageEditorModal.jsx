import React, { useRef } from "react";
import {
  Cropper,
  CropperRef
} from "react-advanced-cropper";
import "react-advanced-cropper/dist/style.css";

export default function ImageEditorModal({ src, onClose, onSend }) {
  const cropperRef = useRef(null);

  const handleSend = () => {
    if (cropperRef.current) {
      const canvas = cropperRef.current.getCanvas();
      if (canvas) {
        canvas.toBlob((blob) => {
          if (blob) {
            const namedBlob = new File([blob], "cropped.jpg", { type: "image/jpeg" });
            onSend(namedBlob);
            onClose();
          }
        });
      }
    }
  };

  return (
    <div className="fixed inset-0 bg-black/80 z-[9999] flex justify-center items-center p-4 overflow-auto">
      <div className="bg-gray-900 rounded-xl w-full max-w-4xl h-[90vh] p-4 flex flex-col relative">
        <Cropper
          ref={cropperRef}
          src={src}
          stencilProps={{
            movable: true,
            resizable: true,
            lines: true,
            handlers: true,
          }}
          defaultTransforms={[]}
          className="h-full w-full"
        />
        <div className="flex justify-between mt-4">
          <button
            onClick={onClose}
            className="bg-red-600 text-white rounded px-4 py-1"
          >
            بستن
          </button>
          <button
            onClick={handleSend}
            className="bg-green-600 text-white rounded px-4 py-1"
          >
            ادامه
          </button>
        </div>
      </div>
    </div>
  );
}
