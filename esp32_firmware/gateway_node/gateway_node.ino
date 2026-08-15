#include <SPI.h>
#include <LoRa.h>
#include <WiFi.h>
#include <WiFiClientSecure.h>
#include <HTTPClient.h>

// Definisi PIN LoRa (Sesuaikan jika Anda menggunakan pin yang berbeda)
#define ss 5
#define rst 14
#define dio0 26

// ===== PENGATURAN WIFI =====
const char* ssid = "NAMA_WIFI_ANDA";
const char* password = "PASSWORD_WIFI_ANDA";

// ===== PENGATURAN API LARAVEL =====
// Ganti IP di bawah ini dengan IP komputer tempat Laravel berjalan di jaringan lokal yang sama
// Catatan: Anda harus menjalankan laravel dengan perintah: php artisan serve --host=0.0.0.0
const char* serverName = "https://tengganglopi-two.vercel.app/api/telemetry";
// Jika Laravel sudah online, gunakan domainnya, misal: "https://domain-anda.com/api/telemetry"

void setup() {
  Serial.begin(115200);
  while (!Serial);

  Serial.println("--- INISIALISASI GATEWAY TENGGANG LOPI ---");

  // 1. Koneksi ke WiFi
  WiFi.begin(ssid, password);
  Serial.print("Connecting to WiFi");
  while(WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.print("✅ WiFi Terhubung. IP Address: ");
  Serial.println(WiFi.localIP());

  // 2. Inisialisasi LoRa
  LoRa.setPins(ss, rst, dio0);
  if (!LoRa.begin(433E6)) {
    Serial.println("❌ LoRa Gateway Gagal!");
    while (1) delay(10);
  }
  
  Serial.println("✅ LoRa Gateway Siap! Menunggu telemetri dari laut...");
  Serial.println("==================================================");
}

void loop() {
  int packetSize = LoRa.parsePacket();
  
  // Jika ada paket data yang masuk dari LoRa Perahu
  if (packetSize) {
    String dataMasuk = "";
    while (LoRa.available()) {
      dataMasuk += (char)LoRa.read();
    }

    Serial.print("\n[DATA MASUK] ");
    Serial.print(dataMasuk);
    Serial.print(" | RSSI: ");
    Serial.print(LoRa.packetRssi());
    Serial.println(" dBm");

    // Kirim data ke Server Laravel menggunakan HTTP POST
    if(WiFi.status() == WL_CONNECTED){
      WiFiClientSecure client;
      client.setInsecure(); // Bypass sertifikat SSL untuk Vercel (karena menggunakan HTTPS)
      
      HTTPClient http;
      
      // Memulai koneksi HTTP dengan SSL Client
      http.begin(client, serverName);
      
      // Memberitahu server bahwa data yang dikirim adalah JSON
      http.addHeader("Content-Type", "application/json");
      http.addHeader("Accept", "application/json");

      // Mengirim POST Request berisi string JSON yang diterima dari LoRa Perahu
      int httpResponseCode = http.POST(dataMasuk);

      Serial.print("HTTP Response code: ");
      Serial.println(httpResponseCode);
      
      if (httpResponseCode > 0) {
        String payload = http.getString();
        Serial.println("Server Response: " + payload);
      } else {
        Serial.print("Error code: ");
        Serial.println(httpResponseCode);
      }
      
      // Tutup koneksi
      http.end();
    }
    else {
      Serial.println("⚠️ Peringatan: WiFi Terputus! Data tidak diteruskan ke server.");
    }
  }
}
