import cv2
import time
from ultralytics import YOLO

# Load model AI yang sudah dilatih 
# (Kamu butuh file 'best.pt' hasil training kamu sendiri)
try:
    # PERBAIKAN: Coba load best.pt terlebih dahulu di sini
    model = YOLO('yolov8n.pt') 
except FileNotFoundError:
    print("File 'best.pt' tidak ditemukan! Ganti dengan 'yolov8n.pt' untuk tes awal.")
    model = YOLO('yolov8n.pt')

def prediksi_model_ai(frame):
    """
    Fungsi ini memanggil model AI asli (YOLOv8) untuk membaca frame kamera.
    """
    results = model.predict(frame, conf=0.5, verbose=False)
    hasil_deteksi = []
    
    for result in results:
        boxes = result.boxes
        for box in boxes:
            # 1. Ambil koordinat Bounding Box
            x1, y1, x2, y2 = map(int, box.xyxy[0])
            
            # 2. Ambil ID Kelas dan Nilai Kepercayaan (Confidence)
            cls_id = int(box.cls[0])
            conf = float(box.conf[0])
            
            # 3. Dapatkan nama kelas (Misal: 'Padi_Siap_Panen')
            nama_class = model.names[cls_id]
            
            # 4. Logika Estimasi Hari Panen Berdasarkan Kelas
            if nama_class == "Padi_Vegetatif":
                estimasi_hari = "60-90"
            elif nama_class == "Padi_Berbunga":
                estimasi_hari = "30-40"
            elif nama_class == "Padi_Siap_Panen":
                estimasi_hari = "0-7"
            else:
                estimasi_hari = "Bukan Padi"
                
            hasil_deteksi.append((nama_class, estimasi_hari, (x1, y1, x2, y2), conf))
            
    return hasil_deteksi

def main():
    cap = cv2.VideoCapture(0)

    if not cap.isOpened():
        print("Error: Tidak dapat membuka webcam.")
        return

    print("Membuka kamera AI... Tekan 'q' pada jendela untuk keluar.")

    while True:
        ret, frame = cap.read()
        if not ret:
            print("Gagal mengambil frame.")
            break
        
        deteksi_semua_objek = prediksi_model_ai(frame)
        
        for deteksi in deteksi_semua_objek:
            tanaman, estimasi, box, conf = deteksi
            x1, y1, x2, y2 = box

            # Gambar Kotak (Bounding Box)
            cv2.rectangle(frame, (x1, y1), (x2, y2), (0, 255, 0), 2)

            # Siapkan Teks
            label_tanaman = f"{tanaman} ({conf*100:.0f}%)"
            label_panen = f"Panen: {estimasi} Hari"

            # Buat background hitam transparan untuk teks agar mudah dibaca
            cv2.rectangle(frame, (x1, y1 - 45), (x1 + 250, y1), (0, 0, 0), -1)

            # Tambahkan Teks
            cv2.putText(frame, label_tanaman, (x1 + 5, y1 - 25), cv2.FONT_HERSHEY_SIMPLEX, 0.6, (0, 255, 0), 2)
            cv2.putText(frame, label_panen, (x1 + 5, y1 - 5), cv2.FONT_HERSHEY_SIMPLEX, 0.6, (0, 255, 255), 2)

        cv2.imshow('Sistem Deteksi Panen Padi AI', frame)

        if cv2.waitKey(1) & 0xFF == ord('q'):
            break

    cap.release()
    cv2.destroyAllWindows()

if __name__ == "__main__":
    main()