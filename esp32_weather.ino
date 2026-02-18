#include <WiFi.h>
#include <HTTPClient.h>
#include <DHT.h>

// DHT Settings
#define DHTPIN 4
#define DHTTYPE DHT11

DHT dht(DHTPIN, DHTTYPE);

// =============================================
// CHANGE THESE TO YOUR OWN WIFI CREDENTIALS
const char* ssid     = "YOUR_WIFI_NAME";
const char* password = "YOUR_WIFI_PASSWORD";
// =============================================

// CHANGE THIS TO YOUR PC's LOCAL IP ADDRESS
// (find it by typing ipconfig in cmd, look for IPv4 Address)
const char* serverURL = "http://192.168.1.100/weather/save.php";

void setup() {
  Serial.begin(115200);
  dht.begin();

  // Connect to WiFi
  WiFi.begin(ssid, password);
  Serial.print("Connecting to WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nConnected! ESP32 IP: ");
  Serial.println(WiFi.localIP());
}

void loop() {
  float tempC = dht.readTemperature();
  float hum   = dht.readHumidity();

  if (isnan(tempC) || isnan(hum)) {
    Serial.println("DHT read failed, retrying...");
    delay(3000);
    return;
  }

  float tempF = tempC * 9.0 / 5.0 + 32.0;

  Serial.printf("Temp: %.1f C / %.1f F  |  Humidity: %.1f%%\n", tempC, tempF, hum);

  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(serverURL);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");

    String postData = "tempC=" + String(tempC, 1)
                    + "&tempF=" + String(tempF, 1)
                    + "&humidity=" + String(hum, 1);

    int responseCode = http.POST(postData);
    Serial.println("Server response: " + String(responseCode));
    http.end();
  } else {
    Serial.println("WiFi disconnected, attempting reconnect...");
    WiFi.begin(ssid, password);
  }

  delay(5000); // Send data every 5 seconds
}
