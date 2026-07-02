/**
 * cp-widgets.js — CodePuno
 * Campana de notificaciones con polling AJAX cada 30 segundos
 * Se inyecta automáticamente en el header al cargar la página
 */

(function () {
    'use strict';

    // ── Estilos del widget ───────────────────────────────────────
    var css = `
    #cp-notif-btn {
        position:fixed;
        top: 14px;
        right: 16px;
        z-index: 9999;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: rgba(30,32,53,.95);
        border: 1px solid rgba(255,255,255,.1);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 16px rgba(0,0,0,.4);
        transition: background .15s, box-shadow .15s;
    }
    #cp-notif-btn:hover { background: rgba(124,58,237,.3); box-shadow: 0 4px 20px rgba(124,58,237,.35); }
    #cp-notif-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        background: #ef4444;
        color: #fff;
        border-radius: 99px;
        font-size: .6rem;
        font-weight: 700;
        min-width: 16px;
        height: 16px;
        padding: 0 4px;
        display: none;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }
    #cp-notif-panel {
        position: fixed;
        top: 58px;
        right: 12px;
        z-index: 9998;
        width: 340px;
        max-height: 480px;
        background: #161826;
        border: 1px solid rgba(255,255,255,.1);
        border-radius: 12px;
        box-shadow: 0 12px 40px rgba(0,0,0,.5);
        display: none;
        flex-direction: column;
        overflow: hidden;
    }
    #cp-notif-panel.visible { display: flex; }
    .cp-notif-hdr {
        padding: 12px 14px;
        border-bottom: 1px solid rgba(255,255,255,.08);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }
    .cp-notif-hdr span { font-weight: 600; font-size: .88rem; }
    .cp-notif-hdr button {
        background: none;
        border: none;
        color: #a78bfa;
        font-size: .72rem;
        cursor: pointer;
        padding: 0;
    }
    .cp-notif-hdr button:hover { text-decoration: underline; }
    #cp-notif-list {
        overflow-y: auto;
        flex: 1;
        padding: 6px 0;
    }
    .cp-notif-item {
        padding: 10px 14px;
        border-bottom: 1px solid rgba(255,255,255,.05);
        cursor: default;
        transition: background .1s;
    }
    .cp-notif-item:last-child { border-bottom: none; }
    .cp-notif-item:hover { background: rgba(255,255,255,.03); }
    .cp-notif-item.no-leida { background: rgba(124,58,237,.07); }
    .cp-notif-icon {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .85rem;
        flex-shrink: 0;
    }
    .cp-notif-titulo { font-size: .82rem; font-weight: 600; color: #e2e8f0; margin-bottom: 2px; }
    .cp-notif-msg    { font-size: .73rem; color: #94a3b8; line-height: 1.3; }
    .cp-notif-fecha  { font-size: .65rem; color: #64748b; margin-top: 3px; }
    .cp-notif-link   {
        display: inline-block;
        margin-top: 4px;
        font-size: .7rem;
        color: #a78bfa;
        background: rgba(124,58,237,.12);
        padding: 2px 8px;
        border-radius: 99px;
        cursor: pointer;
        border: none;
    }
    .cp-notif-link:hover { background: rgba(124,58,237,.25); }
    .cp-notif-vacia {
        padding: 32px 16px;
        text-align: center;
        color: #64748b;
        font-size: .82rem;
    }
    `;

    var style = document.createElement('style');
    style.textContent = css;
    document.head.appendChild(style);

    // ── HTML del widget ──────────────────────────────────────────
    var btn = document.createElement('div');
    btn.id = 'cp-notif-btn';
    btn.title = 'Notificaciones';
    btn.innerHTML = `
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#a78bfa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
        </svg>
        <div id="cp-notif-badge"></div>
    `;

    var panel = document.createElement('div');
    panel.id  = 'cp-notif-panel';
    panel.innerHTML = `
        <div class="cp-notif-hdr">
            <span>🔔 Notificaciones</span>
            <button onclick="cpNotifMarcarTodas()">Marcar todas como leídas</button>
        </div>
        <div id="cp-notif-list">
            <div class="cp-notif-vacia">Cargando...</div>
        </div>
    `;

    document.body.appendChild(btn);
    document.body.appendChild(panel);

    // ── Tipos de notificación (icono + color) ────────────────────
    var tipoConfig = {
        'GRUPO_NUEVO':   { icon: '🗂️', bg: 'rgba(124,58,237,.2)' },
        'MENSAJE_NUEVO': { icon: '💬', bg: 'rgba(6,182,212,.2)'  },
        'CONTACTO_RESP': { icon: '✅', bg: 'rgba(16,185,129,.2)' },
        'INFO':          { icon: 'ℹ️', bg: 'rgba(100,116,139,.2)'},
    };

    // ── Toggle del panel ─────────────────────────────────────────
    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        var visible = panel.classList.toggle('visible');
        if (visible) cpNotifCargar();
    });
    document.addEventListener('click', function (e) {
        if (!panel.contains(e.target) && e.target !== btn) {
            panel.classList.remove('visible');
        }
    });

    // ── Renderizar notificaciones ────────────────────────────────
    function cpNotifCargar() {
        if (typeof $ === 'undefined') return;
        $.getJSON(window.baseUrl + '/notificaciones/lista', function (data) {
            var list = document.getElementById('cp-notif-list');
            if (!data.notifs || data.notifs.length === 0) {
                list.innerHTML = '<div class="cp-notif-vacia">✨ No tienes notificaciones</div>';
                return;
            }
            list.innerHTML = '';
            data.notifs.forEach(function (n) {
                var cfg  = tipoConfig[n.tipo] || tipoConfig['INFO'];
                var item = document.createElement('div');
                item.className = 'cp-notif-item d-flex gap-2 align-items-start' + (n.leida ? '' : ' no-leida');

                var linkHtml = '';
                if (n.link && n.link_label) {
                    linkHtml = '<button class="cp-notif-link" onclick="cpNotifIr(\'' +
                        n.link.replace(/'/g,"\\'") + '\','+n.ide+')">→ ' +
                        n.link_label + '</button>';
                }

                item.innerHTML = `
                    <div class="cp-notif-icon" style="background:${cfg.bg}">${cfg.icon}</div>
                    <div style="flex:1;min-width:0;">
                        <div class="cp-notif-titulo">${n.titulo}</div>
                        <div class="cp-notif-msg">${n.mensaje || ''}</div>
                        ${linkHtml}
                        <div class="cp-notif-fecha">${n.fecha}</div>
                    </div>
                    ${!n.leida ? '<div style="width:6px;height:6px;border-radius:50%;background:#a78bfa;flex-shrink:0;margin-top:6px;"></div>' : ''}
                `;
                list.appendChild(item);
            });
        });
    }

    // ── Navegar desde notificación ───────────────────────────────
    window.cpNotifIr = function (link, ide) {
        // Marcar como leída
        $.post(window.baseUrl + '/notificaciones/marcar', { ide: ide });
        // Navegar en la SPA
        if (typeof cargarFuncion !== 'undefined') {
            cargarFuncion(link, 'Notificación', '', '');
        } else {
            window.location.href = link;
        }
        panel.classList.remove('visible');
    };

    // ── Marcar todas como leídas ─────────────────────────────────
    window.cpNotifMarcarTodas = function () {
        $.post(window.baseUrl + '/notificaciones/marcar', { ide: 'todas' }, function () {
            document.getElementById('cp-notif-badge').style.display = 'none';
            cpNotifCargar();
        });
    };

    // ── Polling: actualizar badge cada 30 segundos ───────────────
    function cpNotifActualizarBadge() {
        if (typeof $ === 'undefined') return;
        $.getJSON(window.baseUrl + '/notificaciones/count', function (data) {
            var badge = document.getElementById('cp-notif-badge');
            if (data.count > 0) {
                badge.textContent = data.count > 99 ? '99+' : data.count;
                badge.style.display = 'flex';
                // Animar el botón levemente
                btn.style.boxShadow = '0 0 0 3px rgba(239,68,68,.25)';
                setTimeout(function(){ btn.style.boxShadow = ''; }, 800);
            } else {
                badge.style.display = 'none';
            }
        });
    }

    // Esperar a que jQuery esté disponible antes de iniciar
    function esperarJquery() {
        if (typeof $ !== 'undefined' && typeof window.baseUrl !== 'undefined') {
            cpNotifActualizarBadge();
            setInterval(cpNotifActualizarBadge, 30000);
        } else {
            setTimeout(esperarJquery, 500);
        }
    }
    esperarJquery();

})();
