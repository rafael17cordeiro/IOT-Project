import time
import requests
from gpiozero import Servo
import RPi.GPIO as GPIO
from datetime import datetime
import cv2
from threading import Thread, Lock

# === CONFIGURAÇÃO DE PINOS ===
PIR_PIN = 27
SERVO_PIN = 17
LED_PIN = 18  # LED no GPIO 18

# === CONFIGURAÇÃO GERAL ===
GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)
GPIO.setup(PIR_PIN, GPIO.IN)
GPIO.setup(LED_PIN, GPIO.OUT)

servo = Servo(SERVO_PIN)

# === URLS DA API ===
URL_API = "https://iot.dei.estg.ipleiria.pt/ti/ti085/api/api.php"
URL_TEMPERATURA = "https://iot.dei.estg.ipleiria.pt/ti/ti085/api/files/Temperatura/valor.txt"
URL_CONTADOR = "https://iot.dei.estg.ipleiria.pt/ti/ti085/api/files/ContadorPessoas/valor.txt"
UPLOAD_URL = "https://iot.dei.estg.ipleiria.pt/ti/ti085/api/upload_camera.php"
WEBCAM_URL = "http://10.20.228.29:4747/video"  # Altere consoante o telemóvel

# === CONTROLO DE THREAD DO SERVO ===
servo_lock = Lock()
servo_em_movimento = False

# === FUNÇÕES ===

def tirar_e_enviar_foto():
    try:
        cap = cv2.VideoCapture(WEBCAM_URL)
        ret, frame = cap.read()
        cap.release()

        if not ret:
            print("Não foi possível capturar imagem da webcam.")
            return

        local_path = "webcam.jpg"
        cv2.imwrite(local_path, frame)

        with open(local_path, "rb") as f:
            files = { "webcam": ("webcam.jpg", f, "image/jpeg") }
            resp = requests.post(UPLOAD_URL, files=files)

        if resp.status_code == 200:
            print(f"[{datetime.now():%Y-%m-%d %H:%M:%S}] Foto enviada com sucesso!")
        else:
            print(f"[{datetime.now():%Y-%m-%d %H:%M:%S}] Erro ao enviar foto: {resp.status_code} – {resp.text}")
    except Exception as e:
        print("Erro ao tirar ou enviar foto:", e)

def post_api(valor: int) -> None:
    payload = {
        "nome": "SensorPessoas",
        "valor": valor,
        "hora": datetime.now().strftime("%Y-%m-%d %H:%M:%S"),
    }

    try:
        resp = requests.post(URL_API, data=payload, timeout=5)
        print(f"{'Entrada' if valor == 1 else 'Saída'} enviada. Estado: {resp.status_code}")
    except requests.RequestException as exc:
        print("ERRO: não consegui contactar a API ->", exc)

def obter_contador_pessoas() -> int:
    try:
        resposta = requests.get(URL_CONTADOR, timeout=5)
        if resposta.status_code == 200:
            return int(resposta.text.strip())
        else:
            print(f"Erro ao obter contador: {resposta.status_code}")
    except Exception as e:
        print("Erro ao obter contador de pessoas:", e)
    return -1

def obter_temperatura() -> float:
    try:
        resposta = requests.get(URL_TEMPERATURA, timeout=5)
        if resposta.status_code == 200:
            return float(resposta.text.strip())
        else:
            print(f"Erro na resposta de temperatura: {resposta.status_code}")
    except Exception as e:
        print("Erro ao obter temperatura:", e)
    return None

def oscilar_servo():
    global servo_em_movimento
    if servo_em_movimento:
        return  # Já está a oscilar, não fazer nada

    def movimento():
        global servo_em_movimento
        with servo_lock:
            servo_em_movimento = True
            print("Servo a oscilar...")
            for _ in range(3):
                servo.min()
                time.sleep(0.5)
                servo.max()
                time.sleep(0.5)
            servo.value = 0
            print("Oscilação concluída")
            servo_em_movimento = False

    Thread(target=movimento).start()

# === LOOP PRINCIPAL ===
prev_state = GPIO.input(PIR_PIN)
contador = 0
modo_entrada = True

try:
    while True:
        current_state = GPIO.input(PIR_PIN)
        if current_state == 1 and prev_state == 0:
            valor = 1 if modo_entrada else 0
            post_api(valor)
            contador += 1

            if valor == 1:
                tirar_e_enviar_foto()

            if contador == 3:
                contador = 0
                modo_entrada = not modo_entrada

        prev_state = current_state

        temp = obter_temperatura()
        if temp is not None:
            print(f"Temperatura: {temp:.1f}°C")
            if temp <= 15 or temp >= 25:
                oscilar_servo()
            else:
                if not servo_em_movimento:
                    servo.value = 0
                print("Ar Condicionado DESLIGADO")

        contador_remoto = obter_contador_pessoas()
        if contador_remoto >= 20:
            GPIO.output(LED_PIN, GPIO.HIGH)
            print("LED ACESO - Contador atingiu 20 ou mais")
        else:
            GPIO.output(LED_PIN, GPIO.LOW)

        time.sleep(0.4)

except KeyboardInterrupt:
    print("Programa terminado pelo utilizador.")

finally:
    GPIO.cleanup()
    print("GPIO limpo. Fim.")
