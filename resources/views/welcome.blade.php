<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Chat Manager</title>
    <style>
        :root {
            --bg-dark: #111827;
            --bg-sidebar: #1f2937;
            --bg-chat: #0f172a;
            --bg-input: #1e293b;
            --border-color: #374151;
            --text-primary: #f9fafb;
            --text-secondary: #9ca3af;
            --accent-green: #10b981;
            --accent-red: #ef4444;
            --accent-blue: #3b82f6;
            --message-human: #1e3a5f;
            --message-ai: #1e3a3f;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg-dark);
            color: var(--text-primary);
            height: 100%;
            overflow: hidden;
        }

        #loginScreen {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            background: linear-gradient(135deg, var(--bg-dark) 0%, #1a1a2e 100%);
        }

        .login-container {
            background: var(--bg-sidebar);
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }

        .login-container h1 {
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
            background: linear-gradient(90deg, var(--accent-green), var(--accent-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .login-container p { color: var(--text-secondary); margin-bottom: 1.5rem; }

        .login-container input {
            width: 100%;
            padding: 1rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--bg-input);
            color: var(--text-primary);
            font-size: 1rem;
            margin-bottom: 1rem;
        }

        .login-container button {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(90deg, var(--accent-green), var(--accent-blue));
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
        }

        #mainApp { display: none; height: 100%; }

        .app-layout {
            display: grid;
            grid-template-columns: 300px 1fr 280px;
            height: 100%;
            overflow: hidden;
        }

        /* SIDEBAR */
        .sidebar {
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
        }

        .sidebar-header {
            padding: 0.75rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }

        .sidebar-header h2 { font-size: 1rem; font-weight: 600; }

        .header-actions { display: flex; gap: 0.4rem; align-items: center; }

        .refresh-btn {
            background: var(--accent-blue);
            border: none;
            padding: 0.35rem 0.6rem;
            border-radius: 5px;
            color: white;
            font-size: 0.7rem;
            cursor: pointer;
        }

        .logout-btn {
            background: var(--accent-red);
            border: none;
            padding: 0.35rem 0.5rem;
            border-radius: 5px;
            color: white;
            font-size: 0.65rem;
            cursor: pointer;
        }

        .search-box {
            padding: 0.5rem;
            border-bottom: 1px solid var(--border-color);
            flex-shrink: 0;
        }

        .search-box input {
            width: 100%;
            padding: 0.5rem 0.7rem;
            background: var(--bg-input);
            border: 1px solid var(--border-color);
            border-radius: 5px;
            color: var(--text-primary);
            font-size: 0.8rem;
        }

        .conversations-list { 
            flex: 1; 
            overflow-y: auto; 
        }

        .conversation-item {
            padding: 0.7rem 0.8rem;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: background 0.15s;
        }

        .conversation-item:hover { background: rgba(59, 130, 246, 0.1); }

        .conversation-item.active {
            background: rgba(59, 130, 246, 0.2);
            border-left: 3px solid var(--accent-blue);
        }

        .conversation-phone {
            font-weight: 600;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .status-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
        .status-dot.activo { background: var(--accent-green); }
        .status-dot.pausado { background: var(--accent-red); }

        .conversation-preview {
            font-size: 0.75rem;
            color: var(--text-secondary);
            margin-top: 0.15rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .conversation-meta {
            font-size: 0.65rem;
            color: var(--text-secondary);
            margin-top: 0.1rem;
        }

        /* CHAT PANEL - ALTURA FIJA */
        .chat-panel {
            background: var(--bg-chat);
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
        }

        .chat-header {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }

        .chat-header-info h3 { font-size: 0.95rem; }
        .chat-header-info span { font-size: 0.75rem; color: var(--text-secondary); }

        .chat-refresh-btn {
            background: var(--bg-input);
            border: 1px solid var(--border-color);
            padding: 0.35rem 0.6rem;
            border-radius: 5px;
            color: var(--text-secondary);
            font-size: 0.7rem;
            cursor: pointer;
        }

        /* CONTENEDOR DE MENSAJES - CON SCROLL */
        .messages-container { 
            flex: 1; 
            overflow-y: auto; 
            padding: 1rem;
            min-height: 0; /* IMPORTANTE para que flex funcione con overflow */
        }

        .no-chat-selected {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .message {
            max-width: 75%;
            padding: 0.65rem 0.85rem;
            border-radius: 10px;
            margin-bottom: 0.4rem;
        }

        .message.user {
            background: linear-gradient(135deg, var(--message-human), #2a4a6f);
            margin-right: auto;
            border-bottom-left-radius: 3px;
        }

        .message.bot {
            background: linear-gradient(135deg, var(--message-ai), #2a4a5f);
            margin-left: auto;
            border-bottom-right-radius: 3px;
        }

        .message-type {
            font-size: 0.6rem;
            text-transform: uppercase;
            color: var(--text-secondary);
            margin-bottom: 0.15rem;
        }

        .message-content { font-size: 0.85rem; line-height: 1.35; word-wrap: break-word; white-space: pre-wrap; }

        /* INPUT FIJO ABAJO */
        .chat-input-container {
            padding: 0.75rem 1rem;
            border-top: 1px solid var(--border-color);
            display: flex;
            gap: 0.5rem;
            flex-shrink: 0;
            background: var(--bg-chat);
        }

        .chat-input-container textarea {
            flex: 1;
            padding: 0.6rem 0.8rem;
            background: var(--bg-input);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 0.85rem;
            resize: none;
            font-family: inherit;
            max-height: 100px;
        }

        .send-btn {
            padding: 0.6rem 1rem;
            background: linear-gradient(90deg, var(--accent-green), var(--accent-blue));
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
        }

        .send-btn:disabled { opacity: 0.5; }

        /* DETAILS PANEL */
        .details-panel {
            background: var(--bg-sidebar);
            border-left: 1px solid var(--border-color);
            padding: 1rem;
            overflow-y: auto;
        }

        .details-panel h3 {
            font-size: 0.8rem;
            margin-bottom: 1rem;
            color: var(--text-secondary);
            text-transform: uppercase;
        }

        .detail-section { margin-bottom: 1.25rem; }
        .detail-label { font-size: 0.7rem; color: var(--text-secondary); text-transform: uppercase; margin-bottom: 0.3rem; }
        .detail-value { font-size: 0.95rem; font-weight: 500; }

        .status-toggle { display: flex; gap: 0.4rem; margin-top: 0.6rem; }

        .status-btn {
            flex: 1;
            padding: 0.5rem;
            border: 2px solid transparent;
            border-radius: 5px;
            font-weight: 600;
            font-size: 0.75rem;
            cursor: pointer;
        }

        .status-btn.activo { background: rgba(16, 185, 129, 0.2); color: var(--accent-green); }
        .status-btn.activo.selected { border-color: var(--accent-green); }
        .status-btn.pausado { background: rgba(239, 68, 68, 0.2); color: var(--accent-red); }
        .status-btn.pausado.selected { border-color: var(--accent-red); }

        .status-description { font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.5rem; }

        .loading { display: flex; align-items: center; justify-content: center; padding: 1.5rem; color: var(--text-secondary); font-size: 0.85rem; }

        @media (max-width: 1024px) {
            .app-layout { grid-template-columns: 260px 1fr; }
            .details-panel { display: none; }
        }

        @media (max-width: 768px) {
            .app-layout { grid-template-columns: 1fr; }
            .sidebar { display: none; }
        }
    </style>
</head>
<body>
    <div id="loginScreen">
        <div class="login-container">
            <h1>🚀 WhatsApp Manager</h1>
            <p>Ingresa tu clave de acceso</p>
            <input type="password" id="authKeyInput" placeholder="Clave..." autocomplete="off">
            <button onclick="login()">Ingresar</button>
            <p id="loginError" style="color: var(--accent-red); margin-top: 1rem; display: none;">Clave incorrecta</p>
        </div>
    </div>

    <div id="mainApp">
        <div class="app-layout">
            <div class="sidebar">
                <div class="sidebar-header">
                    <h2>💬 Chats</h2>
                    <div class="header-actions">
                        <button class="refresh-btn" onclick="loadConversations()" id="refreshConvsBtn">🔄 Actualizar</button>
                        <button class="logout-btn" onclick="logout()">Salir</button>
                    </div>
                </div>
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Buscar..." oninput="filterConversations()">
                </div>
                <div class="conversations-list" id="conversationsList">
                    <div class="loading">Cargando...</div>
                </div>
            </div>

            <div class="chat-panel">
                <div class="chat-header" id="chatHeader" style="display: none;">
                    <div class="chat-header-info">
                        <h3 id="currentPhone">-</h3>
                        <span id="currentStatus">-</span>
                    </div>
                    <button class="chat-refresh-btn" onclick="refreshCurrentChat()">🔄 Actualizar</button>
                </div>
                <div class="messages-container" id="messagesContainer">
                    <div class="no-chat-selected">👈 Selecciona una conversación</div>
                </div>
                <div class="chat-input-container" id="chatInputContainer" style="display: none;">
                    <textarea id="messageInput" placeholder="Escribe tu mensaje..." rows="1" onkeydown="handleKeyDown(event)"></textarea>
                    <button class="send-btn" id="sendBtn" onclick="sendMessage()">Enviar</button>
                </div>
            </div>

            <div class="details-panel" id="detailsPanel">
                <h3>Detalles</h3>
                <div id="detailsContent"><p style="color: var(--text-secondary);">Selecciona una conversación</p></div>
            </div>
        </div>
    </div>

    <script>
        let authKey = '';
        let conversations = [];
        let currentSessionId = null;
        let cachedMessages = {};
        const API_BASE = '/api';
        const STORAGE_KEY = 'whatsiu_auth_key';

        window.addEventListener('DOMContentLoaded', () => {
            const savedKey = localStorage.getItem(STORAGE_KEY);
            if (savedKey) {
                // Si hay clave guardada, entrar directo sin validar
                authKey = savedKey;
                document.getElementById('loginScreen').style.display = 'none';
                document.getElementById('mainApp').style.display = 'block';
                // Cargar conversaciones en background
                loadConversations();
            }
        });

        async function validateAndEnter() {
            // Ya no se usa - se reemplazó con entrada directa
        }

        async function login() {
            authKey = document.getElementById('authKeyInput').value;
            try {
                const response = await fetch(`${API_BASE}/conversations`, {
                    headers: { 'X-Auth-Key': authKey }
                });
                if (response.ok) {
                    const data = await response.json();
                    conversations = data.data || [];
                    localStorage.setItem(STORAGE_KEY, authKey);
                    document.getElementById('loginScreen').style.display = 'none';
                    document.getElementById('mainApp').style.display = 'block';
                    renderConversations();
                } else {
                    document.getElementById('loginError').style.display = 'block';
                }
            } catch (error) {
                document.getElementById('loginError').style.display = 'block';
            }
        }

        function logout() {
            localStorage.removeItem(STORAGE_KEY);
            location.reload();
        }

        async function loadConversations() {
            const btn = document.getElementById('refreshConvsBtn');
            btn.disabled = true;
            btn.textContent = '⏳...';
            
            try {
                const response = await fetch(`${API_BASE}/conversations`, {
                    headers: { 'X-Auth-Key': authKey }
                });
                if (response.ok) {
                    const data = await response.json();
                    conversations = data.data || [];
                    renderConversations();
                }
            } catch (error) {
                console.error('Error:', error);
            } finally {
                btn.disabled = false;
                btn.textContent = '🔄 Actualizar';
            }
        }

        function renderConversations() {
            const search = document.getElementById('searchInput').value.toLowerCase();
            const filtered = conversations.filter(c => c.session_id.toLowerCase().includes(search));
            const list = document.getElementById('conversationsList');
            
            if (filtered.length === 0) {
                list.innerHTML = '<div class="loading">No hay conversaciones</div>';
                return;
            }

            list.innerHTML = filtered.map(conv => {
                const lastMessage = conv.last_message?.content || 'Sin mensajes';
                const preview = lastMessage.substring(0, 35) + (lastMessage.length > 35 ? '...' : '');
                const isActive = conv.session_id === currentSessionId;

                return `
                    <div class="conversation-item ${isActive ? 'active' : ''}" onclick="selectConversation('${conv.session_id}')">
                        <div class="conversation-phone">
                            <span class="status-dot ${conv.estado}"></span>
                            +${conv.session_id}
                        </div>
                        <div class="conversation-preview">${escapeHtml(preview)}</div>
                        <div class="conversation-meta">${conv.message_count} msgs</div>
                    </div>
                `;
            }).join('');
        }

        function filterConversations() { renderConversations(); }

        async function selectConversation(sessionId) {
            currentSessionId = sessionId;
            renderConversations();
            document.getElementById('chatHeader').style.display = 'flex';
            document.getElementById('chatInputContainer').style.display = 'flex';
            document.getElementById('currentPhone').textContent = '+' + sessionId;
            
            if (cachedMessages[sessionId]) {
                renderMessages(cachedMessages[sessionId].messages);
                renderDetails(sessionId, cachedMessages[sessionId].estado, cachedMessages[sessionId].messages.length);
            } else {
                await loadMessages(sessionId);
            }
        }

        async function refreshCurrentChat() {
            if (currentSessionId) {
                delete cachedMessages[currentSessionId];
                await loadMessages(currentSessionId);
            }
        }

        async function loadMessages(sessionId) {
            const container = document.getElementById('messagesContainer');
            container.innerHTML = '<div class="loading">Cargando...</div>';

            try {
                const response = await fetch(`${API_BASE}/conversations/${sessionId}`, {
                    headers: { 'X-Auth-Key': authKey }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    cachedMessages[sessionId] = {
                        messages: data.data.messages,
                        estado: data.data.estado
                    };
                    renderMessages(data.data.messages);
                    renderDetails(sessionId, data.data.estado, data.data.messages.length);
                }
            } catch (error) {
                container.innerHTML = '<div class="loading">Error al cargar</div>';
            }
        }

        function renderMessages(messages) {
            const container = document.getElementById('messagesContainer');
            
            if (!messages || messages.length === 0) {
                container.innerHTML = '<div class="no-chat-selected">No hay mensajes</div>';
                return;
            }

            container.innerHTML = messages.map(msg => {
                const type = msg.message?.type || 'unknown';
                const content = msg.message?.content || '';
                // human = usuario WhatsApp (izquierda), ai = bot/sistema (derecha)
                const isBot = type === 'ai';
                return `
                    <div class="message ${isBot ? 'bot' : 'user'}">
                        <div class="message-type">${isBot ? '🤖 Bot' : '👤 Usuario'}</div>
                        <div class="message-content">${escapeHtml(content)}</div>
                    </div>
                `;
            }).join('');

            container.scrollTop = container.scrollHeight;
        }

        function renderDetails(sessionId, estado, messageCount) {
            document.getElementById('currentStatus').textContent = estado === 'pausado' ? '⏸️ Pausado' : '✅ Activo';
            
            document.getElementById('detailsContent').innerHTML = `
                <div class="detail-section">
                    <div class="detail-label">Teléfono</div>
                    <div class="detail-value">+${sessionId}</div>
                </div>
                <div class="detail-section">
                    <div class="detail-label">Mensajes</div>
                    <div class="detail-value">${messageCount}</div>
                </div>
                <div class="detail-section">
                    <div class="detail-label">Agente IA</div>
                    <div class="status-toggle">
                        <button class="status-btn activo ${estado === 'activo' ? 'selected' : ''}" onclick="updateEstado('activo')">✅ Activo</button>
                        <button class="status-btn pausado ${estado === 'pausado' ? 'selected' : ''}" onclick="updateEstado('pausado')">⏸️ Pausado</button>
                    </div>
                    <div class="status-description">
                        ${estado === 'pausado' ? '⚠️ Agente pausado' : '🤖 Agente activo'}
                    </div>
                </div>
            `;
        }

        async function updateEstado(newEstado) {
            if (!currentSessionId) return;
            try {
                const response = await fetch(`${API_BASE}/conversations/${currentSessionId}/estado`, {
                    method: 'PUT',
                    headers: { 'X-Auth-Key': authKey, 'Content-Type': 'application/json' },
                    body: JSON.stringify({ estado: newEstado })
                });
                if (response.ok) {
                    if (cachedMessages[currentSessionId]) {
                        cachedMessages[currentSessionId].estado = newEstado;
                    }
                    const conv = conversations.find(c => c.session_id === currentSessionId);
                    if (conv) conv.estado = newEstado;
                    renderConversations();
                    renderDetails(currentSessionId, newEstado, cachedMessages[currentSessionId]?.messages?.length || 0);
                }
            } catch (error) { console.error('Error:', error); }
        }

        async function sendMessage() {
            const input = document.getElementById('messageInput');
            const message = input.value.trim();
            if (!message || !currentSessionId) return;

            const sendBtn = document.getElementById('sendBtn');
            sendBtn.disabled = true;
            input.disabled = true;

            try {
                const response = await fetch(`${API_BASE}/conversations/${currentSessionId}/send`, {
                    method: 'POST',
                    headers: { 'X-Auth-Key': authKey, 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message })
                });

                if (response.ok) {
                    input.value = '';
                    delete cachedMessages[currentSessionId];
                    await loadMessages(currentSessionId);
                } else {
                    alert('Error al enviar');
                }
            } catch (error) {
                alert('Error al enviar');
            } finally {
                sendBtn.disabled = false;
                input.disabled = false;
                input.focus();
            }
        }

        function handleKeyDown(event) {
            if (event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); sendMessage(); }
        }

        function escapeHtml(text) { 
            if (!text) return '';
            const div = document.createElement('div'); 
            div.textContent = text; 
            return div.innerHTML; 
        }

        document.getElementById('authKeyInput').addEventListener('keydown', (e) => { if (e.key === 'Enter') login(); });
    </script>
</body>
</html>
