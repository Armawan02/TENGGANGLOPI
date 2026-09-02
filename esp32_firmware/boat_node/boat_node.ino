#include <Wire.h>
#include <TinyGPS++.h>
#include <HardwareSerial.h>
#include <SPI.h>
#include <LoRa.h>

// --- DEKLARASI PIN ---
#define LORA_SS 5
#define LORA_RST 14
#define LORA_DIO0 26

#define TRIG_PIN 32
#define ECHO_PIN 33
#define BUZZER_PIN 27 // Pin Alarm Lokal

#define GPS_RX 16
#define GPS_TX 17
const uint32_t GPSBaud = 9600;

// --- OBJEK SENSOR ---
TinyGPSPlus gps;
HardwareSerial gpsSerial(1);
const int MPU_ADDR = 0x68;

// --- TIMER PENGIRIMAN ---
unsigned long waktuKirimTerakhir = 0;
const int intervalKirim = 2000; // 2 Detik agar lebih Realtime

// --- STATUS BUZZER DARURAT ---
bool buzzerDarurat = false;
bool statusBahaya = false;
unsigned long waktuMulaiBuzzer = 0;
bool sedangBunyiBuzzer = false;


void setup() {
  Serial.begin(115200);
  Wire.begin(); 

  // Inisialisasi seed acak untuk dummy BME280
  randomSeed(analogRead(0));

  Serial.println("--- INISIALISASI END-NODE 1 (TENGGANG LOPI) ---");

  // 1. Init LoRa
  LoRa.setPins(LORA_SS, LORA_RST, LORA_DIO0);
  if (!LoRa.begin(433E6)) {
    Serial.println("❌ LoRa Gagal!");
    while (1);
  }
  LoRa.receive(); // Wajib agar bisa mendengarkan pesan dari Gateway
  Serial.println("✅ LoRa Siap.");

  // 2. Dummy BME280
  Serial.println("✅ Dummy BME280 Siap.");

  // 3. Init MPU6050
  Wire.beginTransmission(MPU_ADDR);
  Wire.write(0x6B); 
  Wire.write(0);
  Wire.endTransmission(true);
  Serial.println("✅ MPU6050 Siap.");

  // 4. Init HC-SR04
  pinMode(TRIG_PIN, OUTPUT);
  pinMode(ECHO_PIN, INPUT);
  Serial.println("✅ HC-SR04 Siap.");

  // 5. Init GPS
  gpsSerial.begin(GPSBaud, SERIAL_8N1, GPS_RX, GPS_TX);
  Serial.println("✅ GPS NEO-6M Siap.");

  // 6. Init Buzzer
  pinMode(BUZZER_PIN, OUTPUT);
  digitalWrite(BUZZER_PIN, LOW); // Pastikan buzzer mati di awal
  Serial.println("✅ Buzzer Siap.");

  Serial.println("=========================================");
}

void loop() {
  // CEK PERINTAH DARI GATEWAY VIA LORA
  int packetSize = LoRa.parsePacket();
  if (packetSize) {
    String incoming = "";
    while (LoRa.available()) {
      incoming += (char)LoRa.read();
    }
    Serial.println("📥 Pesan dari Gateway: " + incoming);
    if (incoming.indexOf("BUZZER_ON") >= 0) {
      if (!buzzerDarurat) {
        buzzerDarurat = true;
        waktuMulaiBuzzer = millis();
        sedangBunyiBuzzer = true;
      }
    } else if (incoming.indexOf("BUZZER_OFF") >= 0) {
      buzzerDarurat = false;
    }
  }

  // MEMBACA GPS SECARA NON-STOP (Tanpa Delay)
  while (gpsSerial.available() > 0) {
    gps.encode(gpsSerial.read());
  }

  // JIKA WAKTU SUDAH MENCAPAI 5 DETIK, BACA SENSOR & KIRIM DATA
  if (millis() - waktuKirimTerakhir > intervalKirim) {
    
    // --- 1. SENSOR BME280 (Dummy & Placeholder ML) ---
    float suhu = 29.50 + (random(-150, 150) / 100.0);
    float kelembapan = 80.00 + (random(-500, 500) / 100.0);
    float tekanan = 1010.00 + (random(-200, 200) / 100.0);
    
    // Nanti baris ini akan diganti dengan fungsi prediksi TinyML (Random Forest)
    String statusCuaca = "AMAN"; 

    // --- 2. SENSOR HC-SR04 (Deteksi Kebocoran) ---
    digitalWrite(TRIG_PIN, LOW); delayMicroseconds(2);
    digitalWrite(TRIG_PIN, HIGH); delayMicroseconds(10);
    digitalWrite(TRIG_PIN, LOW);
    long duration = pulseIn(ECHO_PIN, HIGH, 30000); 
    float jarakAir = (duration * 0.0343) / 2;

    String statusKebocoran = "";
    if (jarakAir == 0 || jarakAir >= 50) {
      statusKebocoran = "AMAN";
    } else if (jarakAir >= 15 && jarakAir < 50) {
      statusKebocoran = "BOCOR";
    } else { // < 15 cm
      statusKebocoran = "TENGGELAM";
    }

    // --- 3. SENSOR MPU6050 (Stabilitas Perahu) ---
    Wire.beginTransmission(MPU_ADDR);
    Wire.write(0x3B);
    Wire.endTransmission(false);
    Wire.requestFrom(MPU_ADDR, 6, true);
    int16_t accX = (Wire.read() << 8 | Wire.read());
    int16_t accY = (Wire.read() << 8 | Wire.read());
    int16_t accZ = (Wire.read() << 8 | Wire.read());
    float roll = atan2(accY, accZ) * 180.0 / PI;
    float pitch = atan2(-accX, sqrt(accY * accY + accZ * accZ)) * 180.0 / PI;
    float kemiringanMaks = max(abs(roll), abs(pitch));

    String statusStabilitas = "";
    if (kemiringanMaks < 60) {
      statusStabilitas = "AMAN";
    } else if (kemiringanMaks >= 60 && kemiringanMaks <= 89) {
      statusStabilitas = "WASPADA";
    } else { // >= 90 derajat
      statusStabilitas = "TERBALIK";
    }

    // --- 4. PEMBACAAN GPS ---
    float lat = 0.000000;
    float lng = 0.000000;
    if (gps.location.isValid()) {
      lat = gps.location.lat();
      lng = gps.location.lng();
    }

    // --- 5. LOGIKA ALARM BUZZER ---
    // Update status bahaya berdasarkan pembacaan sensor
    statusBahaya = (statusKebocoran != "AMAN" || statusStabilitas != "AMAN" || statusCuaca != "AMAN");
    
    if (buzzerDarurat || statusBahaya) {
      Serial.println("⚠️ ALARM BUZZER AKTIF!");
    }

    // --- 6. SUSUN PAKET DATA (Format JSON) ---
    String paketData = "{\"ID\":\"Node-1\","
                       "\"Suhu\":" + String(suhu, 2) + ","
                       "\"Kelembapan\":" + String(kelembapan, 2) + ","
                       "\"Tekanan\":" + String(tekanan, 2) + ","
                       "\"Cuaca\":\"" + statusCuaca + "\","
                       "\"JarakAir\":" + String(jarakAir, 1) + ","
                       "\"Bocor\":\"" + statusKebocoran + "\","
                       "\"Kemiringan\":" + String(kemiringanMaks, 0) + ","
                       "\"Stabilitas\":\"" + statusStabilitas + "\","
                       "\"Lat\":" + String(lat, 6) + ","
                       "\"Lng\":" + String(lng, 6) + "}";

    // Tampilkan di Serial Monitor
    Serial.println("Mengirim Data: " + paketData);

    // --- 7. TRANSMISI LORA ---
    LoRa.beginPacket();
    LoRa.print(paketData);
    LoRa.endPacket();

    // KEMBALI MENDENGAR SETELAH MENGIRIM
    LoRa.receive(); 

    waktuKirimTerakhir = millis();
  }

  // --- KONTROL BUZZER (NON-BLOCKING) ---
  if (sedangBunyiBuzzer) {
    // Pola dari Dashboard: Bunyi persis 3 detik lalu mati otomatis
    if (millis() - waktuMulaiBuzzer < 3000) {
      digitalWrite(BUZZER_PIN, HIGH);
    } else {
      digitalWrite(BUZZER_PIN, LOW);
      sedangBunyiBuzzer = false; // Selesai 3 detik
    }
  } else if (statusBahaya) {
    // Pola dari Sensor Lokal: Bunyi terus-menerus
    digitalWrite(BUZZER_PIN, HIGH);
  } else {
    // Mati jika semuanya AMAN
    digitalWrite(BUZZER_PIN, LOW);
  }
}
