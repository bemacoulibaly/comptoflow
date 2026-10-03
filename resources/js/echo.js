import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key:         import.meta.env.VITE_REVERB_APP_KEY  ?? 'comptoflow',
    wsHost:      import.meta.env.VITE_REVERB_HOST     ?? '127.0.0.1',
    wsPort:      import.meta.env.VITE_REVERB_PORT     ?? 8080,
    wssPort:     import.meta.env.VITE_REVERB_PORT     ?? 8080,
    forceTLS:   (import.meta.env.VITE_REVERB_SCHEME   ?? 'http') === 'https',
    enabledTransports: ['ws','wss'],
});

export function initialiserTempsReel(societeId) {
    if (!societeId || !window.Echo) return;
    const canal = window.Echo.private('societe.' + societeId);

    canal.listen('.ecriture.validee', (e) => {
        notif('✅ ' + e.message, 'success', '/ecritures/' + e.ecriture.id);
    });

    canal.listen('.facture.creee', (e) => {
        notif('📄 ' + e.message, 'info', '/factures/' + e.facture.id);
    });

    window.Echo.join('presence.societe.' + societeId)
        .here(users => majPresence(users))
        .joining(u  => majPresence(null, u, 'join'))
        .leaving(u  => majPresence(null, u, 'leave'));
}

function notif(msg, type, lien) {
    const div = document.createElement('div');
    div.className = 'fixed bottom-5 right-5 z-50 ' + (type==='success'?'bg-emerald-600':'bg-blue-600') + ' text-white text-sm rounded-xl shadow-lg px-4 py-3 max-w-xs cursor-pointer flex items-start gap-3';
    div.innerHTML = '<span class="flex-1 leading-snug">' + msg + '</span><button class="text-white/70 hover:text-white text-lg">×</button>';
    div.addEventListener('click', () => { window.location.href = lien; div.remove(); });
    div.querySelector('button').addEventListener('click', e => { e.stopPropagation(); div.remove(); });
    document.body.appendChild(div);
    setTimeout(() => div.remove(), 5000);
}

function majPresence(users) {
    const zone = document.getElementById('presence-zone');
    if (!zone || !users) return;
    const autres = users.filter(u => u.id !== window.CURRENT_USER_ID);
    zone.innerHTML = autres.map(u => '<span title="' + u.nom + '" class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 text-[10px] font-semibold">' + u.initiales + '</span>').join('');
}
