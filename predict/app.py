from flask import Flask, request, jsonify
import cv2
import numpy as np

# Import fungsi prediksi dari file index.py kamu
from index import prediksi_model_ai 

app = Flask(__name__)

@app.route('/detect', methods=['POST'])
def detect_padi():
    # 1. Cek apakah ada file gambar yang dikirim dari Laravel
    if 'image' not in request.files:
        return jsonify({'error': 'Tidak ada gambar yang dikirim (key: image)'}), 400

    file = request.files['image']
    
    try:
        # 2. Konversi file gambar yang di-upload ke format OpenCV (BGR)
        npimg = np.frombuffer(file.read(), np.uint8)
        img = cv2.imdecode(npimg, cv2.IMREAD_COLOR)
        
        if img is None:
             return jsonify({'error': 'File yang dikirim bukan gambar valid'}), 400

        # 3. Masukkan gambar ke dalam fungsi prediksi_model_ai dari index.py
        hasil_mentah = prediksi_model_ai(img)

        # 4. Format hasilnya menjadi list of dictionary agar bisa jadi JSON
        data_json = []
        for deteksi in hasil_mentah:
            tanaman, estimasi, box, conf = deteksi
            x1, y1, x2, y2 = box
            
            data_json.append({
                "kelas_tanaman": tanaman,
                "estimasi_panen_hari": estimasi,
                "confidence_score": round(conf, 2),
                "koordinat": {
                    "x1": x1, "y1": y1,
                    "x2": x2, "y2": y2
                }
            })

        # 5. Kembalikan response JSON ke Laravel
        return jsonify({
            'status': 'success',
            'total_terdeteksi': len(data_json),
            'data': data_json
        }), 200

    except Exception as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    # Jalankan server Flask di port 5000
    app.run(host='0.0.0.0', port=5000, debug=True)