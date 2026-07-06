<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#0D0B07">
    <title>Scanner de Vendas — Concerto Renúncia</title>
    <link rel="icon" type="image/png" href="{{ asset('alpha-logo-gold.png') }}">
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
            --blue: #3B82F6;
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
            border-bottom: 2px solid rgba(212,175,55,0.3);
            padding: 12px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }
        .header-badge {
            background: linear-gradient(135deg, rgba(16,185,129,0.25), rgba(16,185,129,0.1));
            border: 1px solid rgba(16,185,129,0.4);
            padding: 5px 14px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.78rem;
            color: #34D399;
            font-weight: 600;
        }
        .counter-number {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
            font-size: 1.1rem;
            color: #34D399;
        }

        /* Body */
        .scanner-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }
        .mode-badge {
            background: linear-gradient(135deg, rgba(16,185,129,0.15), rgba(16,185,129,0.05));
            border: 1px solid rgba(16,185,129,0.3);
            border-radius: 10px;
            padding: 10px 20px;
            margin-bottom: 20px;
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        .mode-badge p { color: #34D399; font-size: 0.85rem; font-weight: 600; }
        .mode-badge span { color: var(--text-muted); font-size: 0.75rem; }

        /* Input */
        .manual-input-area { width: 100%; max-width: 400px; margin-bottom: 14px; }
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
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(16,185,129,0.2);
        }
        .manual-input-area input::placeholder {
            color: var(--text-muted);
            text-transform: none;
            letter-spacing: normal;
            font-size: 0.95rem;
        }

        /* Buttons */
        .scan-btn {
            width: 100%;
            max-width: 400px;
            padding: 16px;
            background: linear-gradient(135deg, #10B981, #059669);
            color: #fff;
            font-family: 'Bebas Neue', cursive;
            font-size: 1.4rem;
            letter-spacing: 0.1em;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 12px;
            display: block;
        }
        .scan-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(16,185,129,0.3); }
        .scan-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
        .scan-btn.secondary {
            background: var(--dark-card);
            border: 1px solid var(--green);
            color: var(--green);
        }
        .scan-btn.active {
            background: linear-gradient(135deg, #10B981, #059669);
            color: #fff;
        }

        #qr-reader video { width: 100% !important; border-radius: 10px; }
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
        .result-overlay.confirmed  { background: rgba(16, 185, 129, 0.95); }
        .result-overlay.already_confirmed { background: rgba(59, 130, 246, 0.95); }
        .result-overlay.not_found,
        .result-overlay.cancelled  { background: rgba(239, 68, 68, 0.95); }
        .result-overlay.already_used { background: rgba(245, 158, 11, 0.95); }

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

        /* History */
        .history-list {
            width: 100%;
            max-width: 400px;
            margin-top: 16px;
            max-height: 180px;
            overflow-y: auto;
        }
        .history-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            background: var(--dark-card);
            border: 1px solid var(--dark-border);
            border-radius: 8px;
            margin-bottom: 5px;
            font-size: 0.83rem;
        }
        .history-item .code { font-family: 'JetBrains Mono', monospace; color: var(--gold); font-weight: 600; }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes scaleIn { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    </style>
</head>
<body>
    <div id="app" style="display:flex; flex-direction:column; height:100vh;">

        <!-- Scanner Screen -->
        <div id="scannerScreen" style="display:flex; flex:1; flex-direction:column;">
            <div class="scanner-header">
                <div style="display:flex; align-items:center; gap:12px;">
                    <a href="{{ route('admin.dashboard') }}" style="color:var(--text-muted); text-decoration:none; display:inline-flex; align-items:center; gap:4px; font-size:0.8rem;">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    </a>
                    <h1 style="font-size: 1.3rem; color: #34D399; display: inline-flex; align-items: center; gap: 8px;">
                        <i data-lucide="shopping-cart" class="w-5 h-5"></i> SCANNER DE VENDAS
                    </h1>
                </div>
                <div class="header-badge">
                    <button onclick="toggleFullScreen()" style="background:none; border:none; color:#34D399; cursor:pointer; margin-right:4px;" aria-label="Ecrã inteiro">
                        <i data-lucide="maximize" class="w-4 h-4"></i>
                    </button>
                    <span style="font-size: 0.75rem; color: var(--text-muted);">VENDIDOS</span>
                    <span class="counter-number" id="saleCount">0</span>
                </div>
            </div>

            <div class="scanner-body">
                <!-- Mode description -->
                <div class="mode-badge">
                    <p><i data-lucide="check-circle" style="display:inline-block;width:14px;height:14px;vertical-align:middle;"></i> CONFIRMAÇÃO DE VENDA</p>
                    <span>Leia ou insira o código do bilhete para confirmar a venda física</span>
                </div>

                <!-- Camera / Manual toggle -->
                <div style="width: 100%; max-width: 400px; display: flex; gap: 10px; margin-bottom: 16px;">
                    <button class="scan-btn secondary" id="toggleCameraBtn"
                        style="font-size: 1.1rem; padding: 12px; margin-bottom: 0; display: inline-flex; align-items: center; justify-content: center; gap: 8px;"
                        onclick="toggleCamera()">
                        <i data-lucide="camera" class="w-5 h-5"></i> CÂMARA
                    </button>
                    <button class="scan-btn active" id="toggleManualBtn"
                        style="font-size: 1.1rem; padding: 12px; margin-bottom: 0; display: inline-flex; align-items: center; justify-content: center; gap: 8px;"
                        onclick="toggleManual()">
                        <i data-lucide="keyboard" class="w-5 h-5"></i> MANUAL
                    </button>
                </div>

                <div id="qr-reader" style="width: 100%; max-width: 400px; display: none; border: 2px solid rgba(16,185,129,0.3); border-radius: 12px; overflow: hidden; margin-bottom: 24px;"></div>
                <div id="cameraStatus" class="camera-status"></div>

                <div class="manual-input-area" id="manualInputArea">
                    <input type="text" id="ticketInput" placeholder="Ex: REN-XXXXXX" autofocus
                           maxlength="10" autocomplete="off" autocorrect="off" spellcheck="false">
                </div>

                <button class="scan-btn" id="confirmBtn" onclick="confirmSale()">
                    <span style="display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                        <i data-lucide="check-circle" class="w-5 h-5"></i> CONFIRMAR VENDA
                    </span>
                </button>

                <p id="manualHint" style="color: var(--text-muted); font-size: 0.8rem; text-align: center; margin-top: 8px;">
                    Pressione Enter para confirmar rapidamente
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
                    <p id="resultPrice"></p>
                    <p id="resultCode"></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        let csrfToken = '{{ csrf_token() }}';
        let saleCount = 0;
        let scanHistory = [];
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
            } catch (e) {}
        }

        function vibrate(pattern) {
            try { navigator.vibrate && navigator.vibrate(pattern); } catch (e) {}
        }

        // Confirm sale API call
        async function confirmSale() {
            const input = document.getElementById('ticketInput');
            const code = input.value.trim().toUpperCase();
            if (!code) return;

            const btn = document.getElementById('confirmBtn');
            btn.disabled = true;
            btn.innerHTML = '<span style="display:inline-flex;align-items:center;justify-content:center;gap:8px;"><i data-lucide="loader-circle" class="w-5 h-5"></i> A CONFIRMAR...</span>';
            lucide.createIcons();

            try {
                const res = await fetch('{{ route("admin.sale.confirm") }}', {
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

                if (data.status === 'confirmed') {
                    saleCount++;
                    document.getElementById('saleCount').textContent = saleCount;
                }
            } catch (err) {
                showResult({ status: 'not_found', message: 'Erro de rede. Verifique a conexão.' });
            }

            input.value = '';
            btn.disabled = false;
            btn.innerHTML = '<span style="display:inline-flex;align-items:center;justify-content:center;gap:8px;"><i data-lucide="check-circle" class="w-5 h-5"></i> CONFIRMAR VENDA</span>';
            lucide.createIcons();
        }

        // Enter key
        document.getElementById('ticketInput').addEventListener('keydown', (e) => {
            if (e.key === 'Enter') { e.preventDefault(); confirmSale(); }
        });

        // Show result overlay
        function showResult(data) {
            const overlay = document.getElementById('resultOverlay');
            const icon = document.getElementById('resultIcon');
            const title = document.getElementById('resultTitle');
            const message = document.getElementById('resultMessage');
            const detail = document.getElementById('resultDetail');

            overlay.className = 'result-overlay show ' + data.status;

            switch (data.status) {
                case 'confirmed':
                    icon.innerHTML = '<i data-lucide="check-circle"></i>';
                    title.textContent = 'VENDA CONFIRMADA!';
                    playTone(880, 150);
                    setTimeout(() => playTone(1100, 150), 180);
                    vibrate([100]);
                    break;
                case 'already_confirmed':
                    icon.innerHTML = '<i data-lucide="info"></i>';
                    title.textContent = 'JÁ CONFIRMADO';
                    playTone(440, 300);
                    vibrate([100, 50, 100]);
                    break;
                case 'already_used':
                    icon.innerHTML = '<i data-lucide="scan-line"></i>';
                    title.textContent = 'BILHETE USADO';
                    playTone(330, 300);
                    vibrate([200, 100, 200]);
                    break;
                case 'cancelled':
                    icon.innerHTML = '<i data-lucide="x-circle"></i>';
                    title.textContent = 'CANCELADO';
                    playTone(165, 500);
                    vibrate([300, 100, 300]);
                    break;
                default:
                    icon.innerHTML = '<i data-lucide="search-x"></i>';
                    title.textContent = 'NÃO ENCONTRADO';
                    playTone(165, 500);
                    vibrate([200, 100, 200, 100, 200]);
            }

            message.textContent = data.message;

            if (data.ticket) {
                detail.style.display = 'block';
                document.getElementById('resultName').innerHTML =
                    '<i data-lucide="user" style="width:16px;height:16px;display:inline-block;vertical-align:text-bottom;"></i> ' + data.ticket.buyer_name;
                document.getElementById('resultType').innerHTML =
                    '<i data-lucide="ticket" style="width:16px;height:16px;display:inline-block;vertical-align:text-bottom;"></i> ' + data.ticket.ticket_type;
                document.getElementById('resultPrice').innerHTML =
                    '<i data-lucide="banknote" style="width:16px;height:16px;display:inline-block;vertical-align:text-bottom;"></i> ' + data.ticket.price + ' MZN';
                document.getElementById('resultCode').innerHTML =
                    '<span class="mono">' + data.ticket.code + '</span>';
            } else {
                detail.style.display = 'none';
            }

            lucide.createIcons();

            setTimeout(() => {
                overlay.classList.remove('show');
                document.getElementById('ticketInput').focus();
            }, 3000);
        }

        // History list
        function addToHistory(code, status) {
            const statusMap = {
                'confirmed':         { icon: 'check-circle', color: '#10B981', label: 'Confirmado' },
                'already_confirmed': { icon: 'info',         color: '#3B82F6', label: 'Já confirmado' },
                'already_used':      { icon: 'scan-line',    color: '#F59E0B', label: 'Usado' },
                'cancelled':         { icon: 'x-circle',     color: '#EF4444', label: 'Cancelado' },
                'not_found':         { icon: 'search-x',     color: '#EF4444', label: 'Não encontrado' },
            };

            const s = statusMap[status] || statusMap['not_found'];
            const time = new Date().toLocaleTimeString('pt-MZ', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

            scanHistory.unshift({ code, status: s, time });
            if (scanHistory.length > 20) scanHistory.pop();

            const list = document.getElementById('historyList');
            list.innerHTML = scanHistory.map(h => `
                <div class="history-item">
                    <span class="code">${h.code}</span>
                    <span style="color: ${h.status.color}; font-size:0.78rem;">${h.status.label}</span>
                    <span style="color: var(--text-muted); font-size: 0.72rem;">${h.time}</span>
                </div>
            `).join('');
            lucide.createIcons();
        }

        // Camera toggle
        async function toggleCamera() {
            document.getElementById('manualInputArea').style.display = 'none';
            document.getElementById('confirmBtn').style.display = 'none';
            document.getElementById('manualHint').style.display = 'none';
            document.getElementById('qr-reader').style.display = 'block';
            setCameraStatus('');

            document.getElementById('toggleCameraBtn').classList.add('active');
            document.getElementById('toggleCameraBtn').classList.remove('secondary');
            document.getElementById('toggleManualBtn').classList.remove('active');
            document.getElementById('toggleManualBtn').classList.add('secondary');

            if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                setCameraStatus('A câmera só funciona em HTTPS. Use o modo manual ou publique com HTTPS.');
                return;
            }

            if (!html5QrCode) { html5QrCode = new Html5Qrcode('qr-reader'); }

            if (!isCameraRunning) {
                try {
                    await html5QrCode.start(
                        { facingMode: 'environment' },
                        { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1 },
                        onScanSuccess,
                        () => {}
                    );
                    isCameraRunning = true;
                } catch (error) {
                    setCameraStatus('Não foi possível abrir a câmera. Verifique as permissões do navegador.');
                }
            }
        }

        async function toggleManual() {
            document.getElementById('manualInputArea').style.display = 'block';
            document.getElementById('confirmBtn').style.display = 'block';
            document.getElementById('manualHint').style.display = 'block';
            document.getElementById('qr-reader').style.display = 'none';
            setCameraStatus('');

            document.getElementById('toggleManualBtn').classList.add('active');
            document.getElementById('toggleManualBtn').classList.remove('secondary');
            document.getElementById('toggleCameraBtn').classList.remove('active');
            document.getElementById('toggleCameraBtn').classList.add('secondary');

            if (html5QrCode && isCameraRunning) { await html5QrCode.stop(); isCameraRunning = false; }
            document.getElementById('ticketInput').focus();
        }

        function onScanSuccess(decodedText) {
            if (isProcessingScan) return;
            let code = decodedText;
            try {
                const payload = JSON.parse(decodedText);
                if (payload.ticket_code) code = payload.ticket_code;
            } catch (e) {}

            // Extract code from pipe-separated payload (e.g. "REN-XXXXXX|signature")
            if (code.includes('|')) code = code.split('|')[0];

            document.getElementById('ticketInput').value = code;
            isProcessingScan = true;
            if (html5QrCode && isCameraRunning) html5QrCode.pause(true);

            confirmSale().then ? confirmSale().then(() => {
                setTimeout(() => {
                    isProcessingScan = false;
                    if (html5QrCode && isCameraRunning) html5QrCode.resume();
                }, 3000);
            }) : setTimeout(() => {
                isProcessingScan = false;
                if (html5QrCode && isCameraRunning) html5QrCode.resume();
            }, 3000);
        }

        function setCameraStatus(message) {
            const status = document.getElementById('cameraStatus');
            if (!message) { status.style.display = 'none'; status.textContent = ''; return; }
            status.style.display = 'block';
            status.textContent = message;
        }

        function toggleFullScreen() {
            if (!document.fullscreenElement && !document.webkitFullscreenElement) {
                (document.documentElement.requestFullscreen || document.documentElement.webkitRequestFullscreen).call(document.documentElement);
            } else {
                (document.exitFullscreen || document.webkitExitFullscreen).call(document);
            }
        }

        lucide.createIcons();
    </script>
</body>
</html>
