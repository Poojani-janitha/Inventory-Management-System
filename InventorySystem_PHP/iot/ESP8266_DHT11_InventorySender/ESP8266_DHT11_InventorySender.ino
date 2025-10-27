#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <DHT.h>

// WiFi
const char* ssid     = "FOT_WiFi";   // open network (no password)
const char* password = "";           // empty for open network

// Server
const char* host = "10.50.52.47";   // your computer running XAMPP
const int   port = 80;                // default HTTP localhost
const char* ingestPath = "http://10.50.52.47/Inventory-Management-System/InventorySystem_PHP/iot/latest.php";

// DHT11
#define DHTPIN 2           // GPIO2 (D4 on many NodeMCU boards)
#define DHTTYPE DHT11
DHT dht(DHTPIN, DHTTYPE);

unsigned long lastSendMs = 0;
const unsigned long sendIntervalMs = 15000; // 15 seconds

void setup() {
  Serial.begin(115200);
  delay(100);
  dht.begin();

  // Connect to WiFi (open network)
  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);
  Serial.print("Connecting to WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println();
  Serial.print("Connected. IP: ");
  Serial.println(WiFi.localIP());
}

void loop() {
  if (millis() - lastSendMs >= sendIntervalMs) {
    lastSendMs = millis();

    float h = dht.readHumidity();
    float t = dht.readTemperature(); // Celsius

    if (isnan(h) || isnan(t)) {
      Serial.println("Failed to read from DHT sensor!");
      return;
    }

    if (WiFi.status() == WL_CONNECTED) {
      WiFiClient client;
      HTTPClient http;

      String url = String("http://") + host + ":" + port +
             "/Inventory-Management-System/InventorySystem_PHP/iot/ingest.php" +
             "?temp=" + String(t,1) + "&hum=" + String(h,1);

if (http.begin(client, url)) {

        int httpCode = http.GET();
        Serial.print("HTTP code: ");
        Serial.println(httpCode);   // Prints: "HTTP code: 404" connection okey but wrong ingest path
        if (httpCode > 0) {
          String payload = http.getString();
          Serial.println(payload);
        }
        http.end();
      } else {
        Serial.println("HTTP begin failed"); // Shows this message when wifi connection fail
      }
    }
  }
}


