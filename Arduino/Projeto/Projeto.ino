#include <WiFiNINA.h>
#include <ArduinoHttpClient.h>
#include <DHT.h>
#include <NTPClient.h>
#include <WiFiUdp.h>

// Definições do sensor TEMPERATURA / HUMIDADE 
#define DHTPIN 1
#define DHTTYPE DHT11
DHT dht(DHTPIN, DHTTYPE);





// Conexão WiFi e servidor
char SSID[] = "labs";
char PASS_WIFI[] = "1nv3nt@r2023_IPLEIRIA";
char URL[] = "iot.dei.estg.ipleiria.pt";
int PORTO = 80;

// Objetos de conexão
WiFiClient clienteWifi;
HttpClient clienteHTTP(clienteWifi, URL, PORTO);

// NTP
WiFiUDP clienteUDP;
char NTP_SERVER[] = "ntp.ipleiria.pt";
NTPClient clienteNTP(clienteUDP, NTP_SERVER, 3600); // UTC+1


#define LED_VERMELHO 3
#define LED_VERDE 5



void setup() {

  pinMode(LED_VERDE, OUTPUT);
  pinMode(LED_VERMELHO, OUTPUT);

  pinMode(LED_BUILTIN, OUTPUT);
  Serial.begin(115200);
  while (!Serial);

  Serial.print("A ligar à rede WiFi: ");
  Serial.println(SSID);
  WiFi.begin(SSID, PASS_WIFI);
  while (WiFi.status() != WL_CONNECTED) {
    Serial.print(".");
    delay(1000);
  }

  Serial.println("\nConectado!");
  dht.begin();
  clienteNTP.begin();
  clienteNTP.update();
}

String formatarDataHora(unsigned long epochTime) {
  // Conversão manual do epochTime para data e hora
  int ano, mes, dia, hora, minuto, segundo;
  unsigned long rawTime = epochTime;

  // Calcular segundo, minuto, hora
  segundo = rawTime % 60;
  rawTime /= 60;
  minuto = rawTime % 60;
  rawTime /= 60;
  hora = rawTime % 24;
  rawTime /= 24;

  char buffer[25];
  sprintf(buffer, "2025/6/20 %02d:%02d:%02d", hora, minuto, segundo);
  return String(buffer);
}

String getFromAPI(String nome) {
  String URLPath = "/ti/ti085/api/api.php?nome=" + nome;  // GET com parâmetro ?nome=...
  
  clienteHTTP.get(URLPath);  // Envia o pedido GET

  while (clienteHTTP.connected()) {
    if (clienteHTTP.available()) {
      int statusCode = clienteHTTP.responseStatusCode();
      String responseBody = clienteHTTP.responseBody();
      //Serial.println("Status Code: " + String(statusCode) + "; Resposta: " + responseBody);
      clienteHTTP.stop();
      return responseBody;  // Retorna a resposta da API
    }
  }

  clienteHTTP.stop();
  return "";  // Em caso de erro
}





void post2API(String nome, String valor, String hora) {
  String URLPath = "https://iot.dei.estg.ipleiria.pt/ti/ti085/api/api.php"; 
  String contentType = "application/x-www-form-urlencoded";
  String body = "&nome=" + nome + "&valor=" + valor + "&hora=" + hora;

  clienteHTTP.post(URLPath, contentType, body);
  while (clienteHTTP.connected()) {
    if (clienteHTTP.available()) {
      int responseStatusCode = clienteHTTP.responseStatusCode();
      String responseBody = clienteHTTP.responseBody();
      //Serial.println("Status Code: " + String(responseStatusCode) + "; Resposta: " + responseBody);
    }
  }
  clienteHTTP.stop(); // <- Importante para encerrar conexão
}





void loop() {
  float temperatura = dht.readTemperature();

  if (isnan(temperatura)) {
    Serial.println("Erro: Falha na leitura da temperatura!");
    delay(2000);
    return;
  }

  clienteNTP.update();
  String horaAtual = formatarDataHora(clienteNTP.getEpochTime());
  String valorTemperatura = String(temperatura, 1);

  // Apenas imprime a temperatura
  Serial.println("Temperatura: " + valorTemperatura + "°C");

  // Envia os dados para a API
  post2API("Temperatura", valorTemperatura, horaAtual);

  // Lê o estado do sensor de pessoas da API
  String valorSensor = getFromAPI("SensorPessoas");
  valorSensor.trim(); // remove espaços e \n

  if (valorSensor == "1") {
    digitalWrite(LED_VERDE, HIGH);
    digitalWrite(LED_VERMELHO, LOW);
    Serial.println("Ultima Pessoa entrou");
  } else if (valorSensor == "0") {
    digitalWrite(LED_VERDE, LOW);
    digitalWrite(LED_VERMELHO, HIGH);
    Serial.println("Ultima Pessoa saiu");
  } else {
    Serial.println("Erro: Falha na leitura do estado do sensor de pessoas!");
  }

  delay(2000);
}


