#include <SPI.h>
#include <LoRa.h>
#include <WiFi.h>
#include <WiFiClientSecure.h>
#include <HTTPClient.h>
#include <WiFiManager.h> // Library tambahan untuk Captive Portal

// Definisi PIN LoRa (Sesuaikan jika Anda menggunakan pin yang berbeda)
#define ss 5
#define rst 14
#define dio0 26

// ===== PENGATURAN API LARAVEL =====
// Ganti IP di bawah ini dengan IP komputer tempat Laravel berjalan di jaringan lokal yang sama
// Catatan: Anda harus menjalankan laravel dengan perintah: php artisan serve --host=0.0.0.0
const char* serverName = "https://tengganglopi-two.vercel.app/api/telemetry";
// Jika Laravel sudah online, gunakan domainnya, misal: "https://domain-anda.com/api/telemetry"

void setup() {
  Serial.begin(115200);
  while (!Serial);

  Serial.println("--- INISIALISASI GATEWAY TENGGANG LOPI ---");

  // 1. Koneksi ke WiFi Menggunakan WiFiManager (Tanpa Hardcode)
  WiFiManager wm;
  
  // wm.resetSettings(); // Buka komentar ini JIKA ingin mereset WiFi yang tersimpan untuk debugging
  
  Serial.println("Mencari WiFi... Jika tidak ada, membuat Hotspot 'TENGGANGLOPI_GATEWAY'...");
  
  // Membuat Hotspot bernama "TENGGANGLOPI_GATEWAY" (tanpa password)
  bool res = wm.autoConnect("TENGGANGLOPI_GATEWAY");

  if(!res) {
    Serial.println("❌ Gagal terhubung ke WiFi atau waktu tunggu habis!");
    delay(3000);
    ESP.restart(); // Restart agar mencoba kembali
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
  LoRa.receive(); // Wajib agar gateway bersiap menerima
  
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
      http.setTimeout(15000); // Set timeout 15 detik untuk antisipasi cold-start server Vercel
      
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

        // --- CEK PERINTAH DARI DASHBOARD ---
        // Mencari kata kunci buzzerSignal:"ON" (bisa menyesuaikan dengan JSON dari Firebase)
        // Karena response JSON Firebase bisa berbentuk "buzzerSignal":"ON", kita gunakan indexOf
        // Hilangkan semua spasi dari payload agar indexOf lebih akurat
        String payloadClean = payload;
        payloadClean.replace(" ", "");

        if (payloadClean.indexOf("\"buzzerSignal\":\"ON\"") >= 0 || payloadClean.indexOf("\"buzzerDarurat\":\"ON\"") >= 0) {
          Serial.println("🚨 PERINTAH DITERIMA: NYALAKAN BUZZER DARURAT!");
          delay(100); // Jeda sebelum Tx LoRa
          LoRa.beginPacket();
          LoRa.print("BUZZER_ON");
          LoRa.endPacket();
          LoRa.receive(); // Kembali ke mode mendengarkan
        } else if (payloadClean.indexOf("\"buzzerSignal\":\"OFF\"") >= 0 || payloadClean.indexOf("\"buzzerDarurat\":\"OFF\"") >= 0 || payloadClean.indexOf("\"buzzerSignal\":null") >= 0) {
          // Jika dimatikan dari web (optional, jika ada tombol matikan)
          delay(100); // Jeda sebelum Tx LoRa
          LoRa.beginPacket();
          LoRa.print("BUZZER_OFF");
          LoRa.endPacket();
          LoRa.receive(); // Kembali ke mode mendengarkan
        }

      } else {
        Serial.print("Error code: ");
        Serial.print(httpResponseCode);
        Serial.print(" (");
        Serial.print(http.errorToString(httpResponseCode));
        Serial.println(")");
      }
      
      // Tutup koneksi
      http.end();
    }
    else {
      Serial.println("⚠️ Peringatan: WiFi Terputus! Data tidak diteruskan ke server.");
    }
  }
}
