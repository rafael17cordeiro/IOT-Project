#  Smart School – IoT Project

This project was developed for the **Internet Technologies** course at ESTG / IPLeiria. It aims to implement a smart monitoring and control system for school environments using IoT technologies.

##  Project Overview

- **Monitors** room temperature and **detects** motion
- **Controls** a servo motor (simulated A/C)
- **Displays** real-time and historical data on a web dashboard
- **Captures** images when motion is detected

##  Architecture

- **Arduino 1010 WiFi**  
  - Sends temperature (DHT11) via JSON (HTTP POST)  
  - Receives motion events to blink LEDs (green/red)

- **Raspberry Pi 4**  
  - Reads motion sensor (PIR)  
  - Activates servo motor if temperature <15°C or >25°C  
  - Captures image using a smartphone (DroidCam)  
  - Sends data to API using Python (with multithreading)

- **Web Server (XAMPP)**  
  - API (PHP) + dynamic dashboard  
  - Admin and user access levels

##  Features

- Real-time temperature and motion data
- LED indicators for people entering/leaving
- Smart A/C simulation (servo motor)
- Webcam photo capture
- Dashboard with graphs (admin only)

## 🛠️ Tools

- Python, C++, PHP  
- Libraries: `gpiozero`, `RPi.GPIO`, `requests`, `threading`, `cv2`  
- Visual Studio Code, XAMPP, PuTTY

##  Authors

Rafael dos Santos Cordeiro  
Guilherme Seco Filipe Quaresma Pimentel  
ESTG / IPLeiria — June 2025
