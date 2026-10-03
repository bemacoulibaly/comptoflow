

<div id="panneau-vocal"
     class="hidden fixed bottom-20 right-5 z-50 w-80 bg-white dark:bg-gray-800
            border border-gray-200 dark:border-gray-700 rounded-2xl shadow-2xl overflow-hidden">
    <div class="px-4 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center gap-2">
        <i class="ti ti-microphone text-white text-lg"></i>
        <span class="text-white text-sm font-medium flex-1">Assistant ComptoFlow</span>
        <button id="btn-fermer-vocal" class="text-white/70 hover:text-white"><i class="ti ti-x"></i></button>
    </div>
    <div class="p-4 space-y-3">
        <div id="vocal-reponse" class="text-xs text-gray-600 dark:text-gray-300 min-h-12 leading-relaxed hidden"></div>
        <div id="vocal-placeholder" class="text-xs text-gray-400 text-center py-3">
            <i class="ti ti-message-question text-2xl block mb-1"></i>
            Posez une question sur vos finances
        </div>
        <div class="flex gap-2">
            <input id="vocal-input" type="text" placeholder="Ex: Quel est notre solde ?"
                   class="input text-xs flex-1">
            <button id="btn-micro-rec" title="Dicter"
                    class="w-9 h-9 rounded-lg bg-purple-100 dark:bg-purple-900/50 flex items-center justify-center text-purple-600 hover:bg-purple-200 transition flex-shrink-0">
                <i class="ti ti-microphone text-sm"></i>
            </button>
            <button id="btn-envoyer-vocal" class="btn-primary text-xs px-3 flex-shrink-0">
                <i class="ti ti-send"></i>
            </button>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
(function() {
    var panneau = document.getElementById('panneau-vocal');
    var btnOpen = document.getElementById('btn-assistant-vocal');
    var btnClose= document.getElementById('btn-fermer-vocal');
    var input   = document.getElementById('vocal-input');
    var btnEnv  = document.getElementById('btn-envoyer-vocal');
    var btnMic  = document.getElementById('btn-micro-rec');
    var repEl   = document.getElementById('vocal-reponse');
    var phEl    = document.getElementById('vocal-placeholder');
    var CSRF    = (document.querySelector('meta[name="csrf-token"]') || {}).content;

    if (btnOpen) btnOpen.addEventListener('click', function() {
        panneau.classList.toggle('hidden');
        if (!panneau.classList.contains('hidden')) input.focus();
    });
    if (btnClose) btnClose.addEventListener('click', function() { panneau.classList.add('hidden'); });

    function envoyerQuestion(q) {
        if (!q.trim()) return;
        phEl.style.display = 'none';
        repEl.classList.remove('hidden');
        repEl.innerHTML = '<i class="ti ti-loader-2 animate-spin mr-1"></i> Analyse...';
        btnEnv.disabled = true;

        fetch('/assistant/vocal', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'},
            body: JSON.stringify({question: q})
        })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            repEl.innerHTML = '<strong class="text-gray-700 dark:text-gray-200">Q :</strong> ' + q + '<br><br>'
                            + '<strong class="text-purple-600">A :</strong> ' + d.reponse;
            if (window.speechSynthesis && !d.erreur) {
                var utt = new SpeechSynthesisUtterance(d.reponse);
                utt.lang = 'fr-FR';
                speechSynthesis.speak(utt);
            }
        })
        .catch(function() { repEl.textContent = 'Erreur de connexion.'; })
        .finally(function() { btnEnv.disabled = false; });
    }

    if (btnEnv) btnEnv.addEventListener('click', function() { envoyerQuestion(input.value); });
    if (input)  input.addEventListener('keydown', function(e) { if (e.key === 'Enter') envoyerQuestion(input.value); });

    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        var Reco = window.SpeechRecognition || window.webkitSpeechRecognition;
        var reco = new Reco();
        reco.lang = 'fr-FR';
        if (btnMic) btnMic.addEventListener('click', function() {
            btnMic.classList.add('bg-red-100','text-red-600');
            btnMic.classList.remove('bg-purple-100','text-purple-600');
            reco.start();
        });
        reco.onresult = function(e) {
            var t = e.results[0][0].transcript;
            input.value = t;
            envoyerQuestion(t);
        };
        reco.onend = function() {
            btnMic.classList.remove('bg-red-100','text-red-600');
            btnMic.classList.add('bg-purple-100','text-purple-600');
        };
    } else if (btnMic) {
        btnMic.style.opacity = '0.4';
        btnMic.style.cursor  = 'not-allowed';
        btnMic.title = 'Non supporté par ce navigateur';
    }
})();
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\xampp\htdocs\Comptoflow\resources\views/partials/assistant-vocal.blade.php ENDPATH**/ ?>