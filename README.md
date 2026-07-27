# ⚡ BrainBlitz: Ultimate Quiz Bowl Arena
### Assumption College of Davao — Real-Time Multiplayer Battle Engine

BrainBlitz is a high-stakes, real-time multiplayer quiz bowl platform built for auditorium-scale engagement. Designed with a sleek dark mode aesthetic, vibrant neon accents, and glassmorphism UI, it transforms classroom learning into a cinematic arena battle.

---

## 🔑 Default Seeded Accounts & Credentials

Run `php artisan db:seed` to seed the default admin and student test accounts:

| Role | Username | Password | Full Name | Default Nickname |
| :--- | :--- | :--- | :--- | :--- |
| **👑 Admin (Host)** | `admin` | `admin1234` | BrainBlitz Admin | Master Host |
| **⚔️ Student 1** | `student1` | `password123` | Alex Hunter | `ShadowBlitz` |
| **⚔️ Student 2** | `student2` | `password123` | Sarah Connor | `NeonVortex` |

---

## 🚀 Deployment Instructions

To launch the arena in local development mode, run these 2 terminal sessions:

### Terminal 1: Application Server & Dev Assets
```bash
php artisan serve
npm run dev
```

### Terminal 2: Real-Time Reverb Relay (WebSockets)
Powers real-time synchronization between Admin Command Center, TV Display, and Student devices.
```bash
php artisan reverb:start
```
---

## 🎮 Student Mechanics & How to Play

1. **Enter Arena Room**:
   * Visit `/student/join` or click **"Join Game Arena"** from the Student Dashboard.
   * Enter the 6-character uppercase **Room Code** (e.g. `HNYVIR`). Spaces are automatically trimmed.

2. **Waiting Room & Ready Protocol**:
   * Once inside the waiting room, click **"I'M READY FOR THE BLITZ!"** to signal synchronization to the host.
   * View live squad size and ready player count in real-time.

3. **Live Arena Battle & Speed Bonus Scoring**:
   * Questions automatically stream to your device with color-coded answer choices.
   * **Scoring Formula**:
     * Base Score for Correct Answer: **1,000 points**.
     * Speed Bonus: Up to **900 bonus points** depending on response speed:  
       $$\text{Points} = 1000 + \left\lfloor 900 \times \frac{\text{Remaining Seconds}}{\text{Time Limit}} \right\rfloor$$
     * Incorrect or missed answers yield **0 points**.

4. **Battle Recap & Personal Leaderboard**:
   * View your final rank, total performance score, accuracy percentage, and full answer key breakdown after the battle concludes.

---

## 👑 Admin Mechanics (Host Capabilities)

1. **Quiz Arena Construction**:
   * **Manage Topics**: Create and organize subject categories (e.g. Mathematics, Science, History).
   * **Quiz Builder**: Set title, time per question (10s - 120s), max participants, and mode (*Single Topic* or *Randomized Topic*).
   * **Question Builder**: Create multiple-choice questions, set custom time limits, and toggle correct answer options.

2. **Arena Room Launch & 1-Click Battle Start**:
   * Launching a quiz generates a unique 6-character room PIN code.
   * Clicking **"Launch The Blitz"** in the Lobby automatically starts Question 1 and streams it live to all joined devices.

3. **Command Center Controls & TV Display Mode**:
   * **Live Response Meter**: Tracks live submissions in real-time ($X / N$ combatants).
   * **Open TV Display**: Launch the TV Audience View in a separate window/monitor for projector display (Press `F11` for true fullscreen immersion).
   * **Next Question / End Game**: Effortlessly navigate through questions or wrap up the battle.
   * **⚠️ FORCE START SYSTEM**: High-visibility emergency override button to start questions even if some combatants are idle.

4. **🏆 Battle History Archive & Data Export**:
   * **Persistent Navigation Bar**: Access **🏆 History** anytime from the admin header.
   * **Historical Records**: Review past quiz bowl sessions, total participants joined, **🥇 1st Place Champion**, **🥈 2nd Place**, **🥉 3rd Place**, and **🔻 Lowest Scorer ("Scored Last")**.
   * **CSV Export**: Click **"Export CSV Report"** to download student scores, ranks, and accuracy metrics for grading.

---

## 🏗️ Hardware Setup Recommendation

1. **Host Laptop**: Connect to local Wi-Fi/LAN and open `/admin/dashboard`.
2. **Projector / TV Display**: Connect secondary display to Host Laptop. Open `/tv/{roomId}/lobby` and press `F11` for fullscreen immersion.
3. **Student Devices**: Mobile phones, tablets, or laptops connected to the same network.

---

## 🛠️ Tech Stack
* **Framework**: Laravel 12
* **Real-time WebSockets**: Laravel Reverb + Echo
* **Frontend**: Alpine.js + Tailwind CSS + Vanilla CSS
* **Database**: MySQL 8.0+
* **Typography**: Outfit (Google Fonts)

---
*Created with precision for Assumption College of Davao.*
