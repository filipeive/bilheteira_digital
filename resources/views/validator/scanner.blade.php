<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#0D0B07">
    <title>Scanner — Concerto Renúncia</title>
    <link rel="manifest" href="/manifest.json">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode/html5-qrcode.min.js" type="text/javascript"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
        :root {
            --gold: #D4AF37;
            --gold-light: #F5E6A3;
            --dark-bg: #0D0B07;
            --dark-surface: #1A1610;
            --dark-card: #231F18;
            --dark-border: #3D362A;
            --text-primary: #F5F0E8;
            --text-secondary: #B8A890;
            --text-muted: #8A7D6B;
            --green: #10B981;
            --orange: #F59E0B;
            --red: #EF4444;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--dark-bg);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            max-height: 100vh;
        }
        h1,h2,h3 { font-family: 'Bebas Neue', cursive; letter-spacing: 0.05em; }
        .mono { font-family: 'JetBrains Mono', monospace; }

        /* Header */
        .scanner-header {
            background: var(--dark-surface);
            border-bottom: 1px solid var(--dark-border);
            padding: 12px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }
        .counter {
            background: rgba(212, 175, 55, 0.15);
            border: 1px solid rgba(212, 175, 55, 0.3);
            padding: 6px 14px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .counter-number {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--gold);
        }

        /* Scanner area */
        .scanner-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        /* Manual input */
        .manual-input-area {
            width: 100%;
            max-width: 400px;
            margin-bottom: 24px;
        }
        .manual-input-area input {
            width: 100%;
            padding: 14px 18px;
            background: var(--dark-card);
            border: 2px solid var(--dark-border);
            border-radius: 12px;
            color: var(--text-primary);
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.2rem;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            transition: border-color 0.3s;
        }
        .manual-input-area input:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(212,175,55,0.2);
        }
        .manual-input-area input::placeholder {
            color: var(--text-muted);
            text-transform: none;
            letter-spacing: normal;
            font-size: 0.95rem;
        }
        .scan-btn {
            width: 100%;
            max-width: 400px;
            padding: 16px;
            background: linear-gradient(135deg, var(--gold), #B8960C);
            color: var(--dark-bg);
            font-family: 'Bebas Neue', cursive;
            font-size: 1.4rem;
            letter-spacing: 0.1em;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 12px;
        }
        .scan-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(212,175,55,0.3); }
        .scan-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
        .scan-btn.secondary {
            background: var(--dark-card);
            border: 1px solid var(--gold);
            color: var(--gold);
        }
        .scan-btn.active {
            background: linear-gradient(135deg, var(--gold), #B8960C);
            color: var(--dark-bg);
        }
        #qr-reader video {
            width: 100% !important;
            border-radius: 10px;
        }
        .camera-status {
            display: none;
            max-width: 400px;
            margin: -8px 0 16px;
            padding: 10px 12px;
            border-radius: 10px;
            background: rgba(245, 158, 11, 0.12);
            border: 1px solid rgba(245, 158, 11, 0.28);
            color: #FBBF24;
            font-size: 0.82rem;
            line-height: 1.5;
        }

        /* Result overlay */
        .result-overlay {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100;
            animation: fadeIn 0.2s ease;
        }
        .result-overlay.show { display: flex; }

        .result-overlay.valid { background: rgba(16, 185, 129, 0.95); }
        .result-overlay.already_used { background: rgba(245, 158, 11, 0.95); }
        .result-overlay.invalid,
        .result-overlay.cancelled,
        .result-overlay.not_confirmed { background: rgba(239, 68, 68, 0.95); }

        .result-content {
            text-align: center;
            padding: 40px;
            animation: scaleIn 0.3s ease;
        }
        .result-icon { display: flex; justify-content: center; margin-bottom: 16px; }
        .result-icon svg { width: 92px; height: 92px; }
        .result-title {
            font-family: 'Bebas Neue', cursive;
            font-size: 3rem;
            letter-spacing: 0.1em;
            margin-bottom: 8px;
        }
        .result-message { font-size: 1.1rem; opacity: 0.9; margin-bottom: 16px; }
        .result-detail {
            background: rgba(0,0,0,0.2);
            border-radius: 12px;
            padding: 16px;
            margin-top: 16px;
        }
        .result-detail p { margin-bottom: 4px; font-size: 0.95rem; }

        /* Login form */
        .login-container {
            max-width: 360px;
            width: 100%;
            padding: 0 20px;
        }
        .login-card {
            background: var(--dark-card);
            border: 1px solid var(--dark-border);
            border-radius: 16px;
            padding: 32px;
            text-align: center;
        }
        .login-card input {
            width: 100%;
            padding: 12px 16px;
            background: var(--dark-bg);
            border: 1px solid var(--dark-border);
            border-radius: 8px;
            color: var(--text-primary);
            font-family: 'Montserrat', sans-serif;
            font-size: 0.95rem;
            margin-bottom: 12px;
            transition: border-color 0.3s;
        }
        .login-card input:focus { outline: none; border-color: var(--gold); }

        /* History */
        .history-list {
            width: 100%;
            max-width: 400px;
            margin-top: 20px;
            max-height: 200px;
            overflow-y: auto;
        }
        .history-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 14px;
            background: var(--dark-card);
            border: 1px solid var(--dark-border);
            border-radius: 8px;
            margin-bottom: 6px;
            font-size: 0.85rem;
        }
        .history-item .code { font-family: 'JetBrains Mono', monospace; color: var(--gold); font-weight: 600; }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes scaleIn { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    </style>
</head>
<body>
    <div id="app">
        <!-- Login Screen -->
        <div id="loginScreen" style="flex:1; display:flex; align-items:center; justify-content:center;">
            <div class="login-container">
                <div class="login-card">
                    <h2 style="font-size: 2rem; color: var(--gold); margin-bottom: 4px; display: inline-flex; align-items: center; gap: 10px;"><i data-lucide="scan-qr-code" class="w-8 h-8"></i> SCANNER</h2>
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 24px;">Concerto Renúncia</p>
                    <form id="loginForm">
                        <input type="email" id="loginEmail" placeholder="Email" value="porteiro@alphaproducoes.mz" required>
                        <input type="password" id="loginPassword" placeholder="Password" value="password" required>
                        <button type="submit" class="scan-btn" style="margin-top: 8px; display: inline-flex; align-items: center; justify-content: center; gap: 8px;"><i data-lucide="lock" class="w-5 h-5"></i> ENTRAR</button>
                    </form>
                    <p id="loginError" style="color: var(--red); font-size: 0.8rem; margin-top: 8px; display: none;"></p>
                </div>
            </div>
        </div>

        <!-- Scanner Screen -->
        <div id="scannerScreen" style="display:none; flex:1; flex-direction:column;">
            <div class="scanner-header">
                <h1 style="font-size: 1.3rem; color: var(--gold); display: inline-flex; align-items: center; gap: 8px;"><i data-lucide="scan-qr-code" class="w-5 h-5"></i> SCANNER</h1>
                <div class="counter">
                    <span style="font-size: 0.75rem; color: var(--text-muted);">ENTRADAS</span>
                    <span class="counter-number" id="entryCount">0</span>
                </div>
            </div>

            <div class="scanner-body">
                <div style="text-align:center; margin-bottom: 20px;">
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">
                        Digite ou leia o código do bilhete
                    </p>
                </div>

                <div style="width: 100%; max-width: 400px; display: flex; gap: 10px; margin-bottom: 16px;">
                    <button class="scan-btn secondary" id="toggleCameraBtn" style="font-size: 1.1rem; padding: 12px; margin-bottom: 0; display: inline-flex; align-items: center; justify-content: center; gap: 8px;" onclick="toggleCamera()">
                        <i data-lucide="camera" class="w-5 h-5"></i> CÂMARA
                    </button>
                    <button class="scan-btn active" id="toggleManualBtn" style="font-size: 1.1rem; padding: 12px; margin-bottom: 0; display: inline-flex; align-items: center; justify-content: center; gap: 8px;" onclick="toggleManual()">
                        <i data-lucide="keyboard" class="w-5 h-5"></i> MANUAL
                    </button>
                </div>

                <div id="qr-reader" style="width: 100%; max-width: 400px; display: none; border: 2px solid var(--dark-border); border-radius: 12px; overflow: hidden; margin-bottom: 24px;"></div>
                <div id="cameraStatus" class="camera-status"></div>

                <div class="manual-input-area" id="manualInputArea">
                    <input type="text" id="ticketInput" placeholder="Ex: REN-XXXXXX" autofocus
                           maxlength="10" autocomplete="off" autocorrect="off" spellcheck="false">
                </div>

                <button class="scan-btn" id="validateBtn" onclick="validateTicket()">
                    <span style="display: inline-flex; align-items: center; justify-content: center; gap: 8px;"><i data-lucide="search" class="w-5 h-5"></i> VALIDAR BILHETE</span>
                </button>

                <p id="manualHint" style="color: var(--text-muted); font-size: 0.8rem; text-align: center; margin-top: 8px;">
                    Pressione Enter para validar rapidamente
                </p>

                <!-- Recent History -->
                <div class="history-list" id="historyList"></div>
            </div>
        </div>

        <!-- Result Overlay -->
        <div class="result-overlay" id="resultOverlay">
            <div class="result-content">
                <div class="result-icon" id="resultIcon"></div>
                <div class="result-title" id="resultTitle"></div>
                <div class="result-message" id="resultMessage"></div>
                <div class="result-detail" id="resultDetail" style="display:none;">
                    <p id="resultName"></p>
                    <p id="resultType"></p>
                    <p id="resultCode"></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        let csrfToken = '{{ csrf_token() }}';
        let entryCount = 0;
        let history = [];
        let html5QrCode = null;
        let isCameraRunning = false;
        let isProcessingScan = false;

        // Audio feedback
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        let audioCtx = null;

        function playTone(freq, duration = 200) {
            try {
                if (!audioCtx) audioCtx = new AudioCtx();
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.frequency.value = freq;
                gain.gain.value = 0.3;
                osc.start();
                osc.stop(audioCtx.currentTime + duration / 1000);
            } catch (e) { /* Audio not supported */ }
        }

        function vibrate(pattern) {
            try { navigator.vibrate && navigator.vibrate(pattern); } catch (e) {}
        }

        // Login
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            const errorEl = document.getElementById('loginError');

            try {
                const loginRes = await fetch('/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ email, password }),
                });

                if (loginRes.ok || loginRes.status === 204) {
                    // Session auth successful
                    document.getElementById('loginScreen').style.display = 'none';
                    const scanner = document.getElementById('scannerScreen');
                    scanner.style.display = 'flex';
                    document.getElementById('ticketInput').focus();
                    loadEntryCount();
                    lucide.createIcons();
                } else {
                    const data = await loginRes.json();
                    errorEl.textContent = data.message || 'Credenciais inválidas.';
                    errorEl.style.display = 'block';
                }
            } catch (err) {
                errorEl.textContent = 'Erro de conexão. Tente novamente.';
                errorEl.style.display = 'block';
            }
        });

        // Validate ticket
        async function validateTicket() {
            const input = document.getElementById('ticketInput');
            const code = input.value.trim().toUpperCase();
            if (!code) return;

            const btn = document.getElementById('validateBtn');
            btn.disabled = true;
            btn.innerHTML = '<span style="display:inline-flex;align-items:center;justify-content:center;gap:8px;"><i data-lucide="loader-circle" class="w-5 h-5"></i> A VALIDAR...</span>';
            lucide.createIcons();

            try {
                const res = await fetch('/validar/bilhete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ ticket_code: code }),
                });

                const data = await res.json();
                showResult(data);
                addToHistory(code, data.status);

                if (data.status === 'valid') {
                    entryCount++;
                    document.getElementById('entryCount').textContent = entryCount;
                }
            } catch (err) {
                showResult({
                    status: 'invalid',
                    message: 'Erro de rede. Verifique a conexão.',
                    ticket: null,
                });
            }

            input.value = '';
            btn.disabled = false;
            btn.innerHTML = '<span style="display:inline-flex;align-items:center;justify-content:center;gap:8px;"><i data-lucide="search" class="w-5 h-5"></i> VALIDAR BILHETE</span>';
            lucide.createIcons();
        }

        // Enter key shortcut
        document.getElementById('ticketInput').addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                validateTicket();
            }
        });

        // Show result overlay
        function showResult(data) {
            const overlay = document.getElementById('resultOverlay');
            const icon = document.getElementById('resultIcon');
            const title = document.getElementById('resultTitle');
            const message = document.getElementById('resultMessage');
            const detail = document.getElementById('resultDetail');

            // Remove previous classes
            overlay.className = 'result-overlay show ' + data.status;

            switch (data.status) {
                case 'valid':
                    icon.innerHTML = '<i data-lucide="check-circle"></i>';
                    title.textContent = 'ENTRE!';
                    playTone(880, 200);
                    vibrate([100]);
                    break;
                case 'already_used':
                    icon.innerHTML = '<i data-lucide="ban"></i>';
                    title.textContent = 'JÁ USADO';
                    playTone(220, 400);
                    vibrate([200, 100, 200]);
                    break;
                case 'not_confirmed':
                    icon.innerHTML = '<i data-lucide="alert-triangle"></i>';
                    title.textContent = 'NÃO CONFIRMADO';
                    playTone(330, 300);
                    vibrate([300]);
                    break;
                default:
                    icon.innerHTML = '<i data-lucide="x-circle"></i>';
                    title.textContent = 'INVÁLIDO';
                    playTone(165, 500);
                    vibrate([200, 100, 200, 100, 200]);
                    break;
            }

            message.textContent = data.message;

            if (data.ticket) {
                detail.style.display = 'block';
                document.getElementById('resultName').innerHTML = '<i data-lucide="user" style="width:16px;height:16px;display:inline-block;vertical-align:text-bottom;"></i> ' + data.ticket.buyer_name;
                document.getElementById('resultType').innerHTML = '<i data-lucide="ticket" style="width:16px;height:16px;display:inline-block;vertical-align:text-bottom;"></i> ' + data.ticket.ticket_type;
                document.getElementById('resultCode').innerHTML = '<span class="mono">' + data.ticket.code + '</span>';
            } else {
                detail.style.display = 'none';
            }

            lucide.createIcons();

            // Auto-hide after 3 seconds
            setTimeout(() => {
                overlay.classList.remove('show');
                document.getElementById('ticketInput').focus();
            }, 3000);
        }

        // History
        function addToHistory(code, status) {
            const statusMap = {
                'valid': { icon: 'check-circle', color: '#10B981' },
                'already_used': { icon: 'ban', color: '#F59E0B' },
                'invalid': { icon: 'x-circle', color: '#EF4444' },
                'cancelled': { icon: 'ban', color: '#EF4444' },
                'not_confirmed': { icon: 'alert-triangle', color: '#F59E0B' },
            };

            const s = statusMap[status] || statusMap['invalid'];
            const time = new Date().toLocaleTimeString('pt-MZ', { hour: '2-digit', minute: '2-digit' });

            history.unshift({ code, status: s, time });
            if (history.length > 20) history.pop();

            const list = document.getElementById('historyList');
            list.innerHTML = history.map(h => `
                <div class="history-item">
                    <span class="code">${h.code}</span>
                    <span style="color: ${h.status.color};"><i data-lucide="${h.status.icon}" style="width:18px;height:18px;"></i></span>
                    <span style="color: var(--text-muted); font-size: 0.75rem;">${h.time}</span>
                </div>
            `).join('');
            lucide.createIcons();
        }

        async function toggleCamera() {
            document.getElementById('manualInputArea').style.display = 'none';
            document.getElementById('validateBtn').style.display = 'none';
            document.getElementById('manualHint').style.display = 'none';
            document.getElementById('qr-reader').style.display = 'block';
            setCameraStatus('');

            document.getElementById('toggleCameraBtn').classList.add('active');
            document.getElementById('toggleCameraBtn').classList.remove('secondary');
            document.getElementById('toggleManualBtn').classList.remove('active');
            document.getElementById('toggleManualBtn').classList.add('secondary');

            if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                setCameraStatus('A câmera do navegador só abre em HTTPS ou localhost. Publique esta página com HTTPS para validar por câmera.');
                return;
            }

            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode('qr-reader');
            }

            if (!isCameraRunning) {
                try {
                    await html5QrCode.start(
                        { facingMode: 'environment' },
                        { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1 },
                        onScanSuccess,
                        onScanFailure
                    );
                    isCameraRunning = true;
                } catch (error) {
                    setCameraStatus('Não foi possível abrir a câmera. Verifique permissões do navegador e se outra aplicação não está a usar a câmera.');
                }
            }
        }

        async function toggleManual() {
            document.getElementById('manualInputArea').style.display = 'block';
            document.getElementById('validateBtn').style.display = 'block';
            document.getElementById('manualHint').style.display = 'block';
            document.getElementById('qr-reader').style.display = 'none';
            setCameraStatus('');

            document.getElementById('toggleManualBtn').classList.add('active');
            document.getElementById('toggleManualBtn').classList.remove('secondary');
            document.getElementById('toggleCameraBtn').classList.remove('active');
            document.getElementById('toggleCameraBtn').classList.add('secondary');

            if (html5QrCode && isCameraRunning) {
                await html5QrCode.stop();
                isCameraRunning = false;
            }
            document.getElementById('ticketInput').focus();
        }

        function onScanSuccess(decodedText, decodedResult) {
            if (isProcessingScan) return;
            
            // Expected payload is JSON or a raw code
            let code = decodedText;
            try {
                const payload = JSON.parse(decodedText);
                if (payload.ticket_code) {
                    code = payload.ticket_code;
                }
            } catch (e) {
                // Not JSON, use as is
            }

            document.getElementById('ticketInput').value = code;
            isProcessingScan = true;
            
            // Pause scanner
            if (html5QrCode && isCameraRunning) {
                html5QrCode.pause(true);
            }

            validateTicket().then(() => {
                setTimeout(() => {
                    isProcessingScan = false;
                    if (html5QrCode && isCameraRunning) {
                        html5QrCode.resume();
                    }
                }, 3000);
            });
        }

        function onScanFailure(error) {
            // handle scan failure, usually better to ignore and keep scanning.
        }

        async function loadEntryCount() {
            try {
                // Count from confirmed list endpoint
                const res = await fetch('/validar/confirmados', {
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json' },
                });
                if (res.ok) {
                    const data = await res.json();
                    // Entry count is separate from confirmed count
                }
            } catch (e) {}
        }

        function setCameraStatus(message) {
            const status = document.getElementById('cameraStatus');
            if (!message) {
                status.style.display = 'none';
                status.textContent = '';
                return;
            }
            status.style.display = 'block';
            status.textContent = message;
        }

        lucide.createIcons();
    </script>
</body>
</html>
