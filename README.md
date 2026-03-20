# ⚡ BrainBlitz: Ultimate Quiz Arena
### Assumption College of Davao - Multiplayer Battle Engine

BrainBlitz is a real-time, high-stakes multiplayer quiz bowl system built for large-scale auditorium engagement. Designed with a deep dark aesthetic and glassmorphism effects, it turns any curriculum into a cinematic battle experience.

## 🚀 Deployment Instructions

To launch the arena, you must have 3 terminal sessions running simultaneously on your host machine:

### Terminal 1: Application Server
Handle HTTP requests and serve the Blade interface.
```bash
php artisan serve
```

### Terminal 2: Reverb Relay (WebSockets)
Powers the real-time synchronization between the Admin, TV, and Students.
```bash
php artisan reverb:start
```

### Terminal 3: Tactical Worker (Queues)
Handles background processing for score calculations and state updates.
```bash
php artisan queue:work
```

---

## 🏗️ Hardware Configuration
1. **Admin Laptop**: Connect to the local network and open the Dashboard.
2. **HDMI Display / TV**: Connect to the Admin Laptop. Open the **TV Display URL** and drag it to the secondary monitor. Press `F11` for true fullscreen immersion.
3. **Student Devices**: Any smartphone or tablet with a browser connected to the same network.

---

## 🎮 Battle Protocol (How to Play)
1. **Host Login**: Use the Master Credentials provided below to access the Command Center.
2. **Terrain Construction**: Create **Topics** and craft a **Quiz**.
3. **Initiation**: Launch the Quiz to create a **Room Code**.
4. **Entrance**: Instruct students to visit `[APP_URL]/student/join` and enter the 6-character code.
5. **Engagement**: The Admin controls the flow (Start Game, Next Question). The **TV Arena** updates automatically.
6. **Victory**: Final scores and the top 3 podium are revealed on the TV at the end of the session.

---

## 🔑 Master Credentials
- **Role**: Admin
- **Username**: `admin`
- **Password**: `admin1234`

---

## 🛠️ Tech Stack & Requirements
- **Framework**: Laravel 12
- **Real-time**: Laravel Reverb + Echo
- **Frontend**: Alpine.js + Tailwind CSS
- **Database**: MySQL 8.0+
- **Font**: Outfit (Google Fonts)

---
*Created with cinematic precision for the Assumption College of Davao.*
