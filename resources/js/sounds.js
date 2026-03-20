class SoundEngine {
    constructor() {
        this.ctx = null;
        this.enabled = localStorage.getItem('sound_enabled') !== 'false';
    }

    init() {
        if (!this.ctx) {
            this.ctx = new (window.AudioContext || window.webkitAudioContext)();
        }
    }

    toggle() {
        this.enabled = !this.enabled;
        localStorage.setItem('sound_enabled', this.enabled);
        return this.enabled;
    }

    playTone(freq, type, duration, vol = 0.1) {
        if (!this.enabled) return;
        this.init();
        
        try {
            const osc = this.ctx.createOscillator();
            const gain = this.ctx.createGain();

            osc.type = type;
            osc.frequency.setValueAtTime(freq, this.ctx.currentTime);
            
            gain.gain.setValueAtTime(vol, this.ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.0001, this.ctx.currentTime + duration);

            osc.connect(gain);
            gain.connect(this.ctx.destination);

            osc.start();
            osc.stop(this.ctx.currentTime + duration);
        } catch(e) { console.warn(e); }
    }

    correct() {
        this.playTone(523.25, 'sine', 0.5); // C5
        setTimeout(() => this.playTone(659.25, 'sine', 0.5), 100); // E5
    }

    wrong() {
        this.playTone(220, 'sawtooth', 0.4, 0.05); // A3
        setTimeout(() => this.playTone(196, 'sawtooth', 0.4, 0.05), 150); // G3
    }

    tick() {
        this.playTone(880, 'sine', 0.05, 0.05);
    }

    fanfare() {
        [440, 554, 659, 880].forEach((f, i) => {
            setTimeout(() => this.playTone(f, 'square', 0.6, 0.03), i * 150);
        });
    }

    appear() {
        this.playTone(1046.50, 'sine', 0.1, 0.05);
    }
}

window.SoundEngine = new SoundEngine();
