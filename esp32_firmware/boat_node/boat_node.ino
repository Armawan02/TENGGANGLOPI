#include <Wire.h>
#include <TinyGPS++.h>
#include <HardwareSerial.h>
#include <SPI.h>
#include <LoRa.h>
#include <WiFi.h> // Tambahkan WiFi untuk mengambil MAC Address ESP32

// --- DEKLARASI PIN ---
#define LORA_SS 5
#define LORA_RST 14
#define LORA_DIO0 26

#define TRIG_PIN 32
#define ECHO_PIN 33

#define GPS_RX 16
#define GPS_TX 17
const uint32_t GPSBaud = 9600;

// --- OBJEK SENSOR ---
TinyGPSPlus gps;
HardwareSerial gpsSerial(1);
const int MPU_ADDR = 0x68;

// --- TIMER PENGIRIMAN ---
unsigned long waktuKirimTerakhir = 0;
const int intervalKirim = 5000;  // Kirim data setiap 5000 milidetik (5 detik)

void setup() {
  Serial.begin(115200);
  Wire.begin();  // Mulai jalur I2C (SDA=21, SCL=22)

  // Inisialisasi WiFi Mode Station hanya untuk mendapatkan MAC Address
  WiFi.mode(WIFI_STA);

  // Inisialisasi seed acak untuk dummy BME280 menggunakan noise dari pin analog kosong
  randomSeed(analogRead(0));

  Serial.println("--- INISIALISASI END-NODE (SENSOR GABUNGAN + DUMMY BME280) ---");

  // 1. Init LoRa
  LoRa.setPins(LORA_SS, LORA_RST, LORA_DIO0);
  if (!LoRa.begin(433E6)) {
    Serial.println("❌ LoRa Gagal!");
    while (1)
      ;
  }
  Serial.println("✅ LoRa Siap.");

  // 2. BME280 (Simulasi)
  Serial.println("✅ BME280 Siap (Mode Simulasi Aktif).");

  // 3. Init MPU6500 (Raw Mode)
  Wire.beginTransmission(MPU_ADDR);
  Wire.write(0x6B);  // Wake up register
  Wire.write(0);
  Wire.endTransmission(true);
  Serial.println("✅ MPU6500 Siap.");

  // 4. Init HC-SR04
  pinMode(TRIG_PIN, OUTPUT);
  pinMode(ECHO_PIN, INPUT);
  Serial.println("✅ HC-SR04 Siap.");

  // 5. Init GPS
  gpsSerial.begin(GPSBaud, SERIAL_8N1, GPS_RX, GPS_TX);
  Serial.println("✅ GPS NEO-6M Siap.");
  
  Serial.println("MAC Address: " + WiFi.macAddress());
  Serial.println("=========================================");
}

void loop() {
  // MEMBACA GPS SECARA NON-STOP (Tanpa Delay)
  while (gpsSerial.available() > 0) {
    gps.encode(gpsSerial.read());
  }

  // JIKA WAKTU SUDAH MENCAPAI 5 DETIK, BACA SENSOR & KIRIM DATA
  if (millis() - waktuKirimTerakhir > intervalKirim) {

    // --- GENERATE DUMMY BME280 ---
    // Logika matematika untuk mensimulasikan nilai float dengan 2 desimal
    float suhu = 29.50 + (random(-150, 150) / 100.0);        // Fluktuasi 28.00 hingga 31.00
    float kelembapan = 80.00 + (random(-500, 500) / 100.0);  // Fluktuasi 75.00 hingga 85.00
    float tekanan = 1010.00 + (random(-200, 200) / 100.0);   // Fluktuasi 1008.00 hingga 1012.00

    // --- BACA HC-SR04 ---
    digitalWrite(TRIG_PIN, LOW);
    delayMicroseconds(2);
    digitalWrite(TRIG_PIN, HIGH);
    delayMicroseconds(10);
    digitalWrite(TRIG_PIN, LOW);
    // Timeout 30ms (30000us) agar tidak hang jika sensor terhalang
    long duration = pulseIn(ECHO_PIN, HIGH, 30000);
    float jarakAir = (duration * 0.0343) / 2;

    // --- BACA MPU6500 ---
    Wire.beginTransmission(MPU_ADDR);
    Wire.write(0x3B);
    Wire.endTransmission(false);
    Wire.requestFrom(MPU_ADDR, 6, true);
    int16_t accX = (Wire.read() << 8 | Wire.read());
    int16_t accY = (Wire.read() << 8 | Wire.read());
    int16_t accZ = (Wire.read() << 8 | Wire.read());
    float roll = atan2(accY, accZ) * 180.0 / PI;
    float pitch = atan2(-accX, sqrt(accY * accY + accZ * accZ)) * 180.0 / PI;

    // --- BACA GPS ---
    float lat = 0.000000;
    float lng = 0.000000;
    if (gps.location.isValid()) {
      lat = gps.location.lat();
      lng = gps.location.lng();
    }

    // --- SUSUN PAKET DATA (Format JSON untuk Laravel) ---
    String mac = WiFi.macAddress();
    String paketData = "{\"mac_address\":\"" + mac + "\", \"temperature\":" + String(suhu, 2) + ", \"humidity\":" + String(kelembapan, 2) + ", \"pressure\":" + String(tekanan, 2) + ", \"roll\":" + String(roll, 2) + ", \"pitch\":" + String(pitch, 2) + ", \"water_level\":" + String(jarakAir, 1) + ", \"latitude\":" + String(lat, 6) + ", \"longitude\":" + String(lng, 6) + "}";

    // Tampilkan di Serial Monitor Pengirim
    Serial.println("Mengirim Data: " + paketData);
    
    // --- TRANSMISI LORA ---
    LoRa.beginPacket();
    LoRa.print(paketData);
    LoRa.endPacket();

    // Reset timer
    waktuKirimTerakhir = millis();
  }
}
