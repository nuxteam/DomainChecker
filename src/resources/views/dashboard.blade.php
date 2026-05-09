<x-app-layout>

<div id="app" class="max-w-6xl mx-auto p-6">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-xl font-semibold">Dashboard</h1>
        <button @click="openSettings">
            ⚙ Settings
        </button>
        <button @click="openAdd"
                class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition text-sm">
            + Add Domain
        </button>
    </div>
    <div v-if="domains.length === 0" class="text-center text-gray-400 py-20">
        <div class="text-4xl mb-3">🌐</div>
        <p class="font-medium text-gray-600">No domains yet</p>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div v-for="d in domains" :key="d.id"
             class="bg-white border rounded-xl p-4 flex flex-col gap-3 shadow-sm hover:shadow-md transition">

            <div class="flex justify-between items-start">
                <div class="overflow-hidden">
                    <div class="font-semibold text-gray-900 truncate" :title="label(d)">@{{ label(d) }}</div>
                    <div class="text-xs text-gray-400 truncate">@{{ d.url }}</div>
                </div>
                <span v-if="d.auto_check"
                      class="text-xs bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded-full whitespace-nowrap ml-2">
                    ⏱ @{{ d.interval }}m
                </span>
            </div>

            <div class="flex items-center justify-between gap-2 mt-auto">
                <span v-if="!d.latest_check" class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">No data</span>
                <span v-else-if="isUp(d)" class="text-xs bg-green-100 text-green-700 font-semibold px-2 py-0.5 rounded-full flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full inline-block"></span> UP
                </span>
                <span v-else class="text-xs bg-red-100 text-red-700 font-semibold px-2 py-0.5 rounded-full flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full inline-block"></span> DOWN
                </span>

                <div class="text-xs text-gray-400">@{{ formatDate(d.latest_check?.created_at) }}</div>

                <div class="flex gap-2">
                    <button @click="checkNow(d)" title="Check now"
                            class="text-xs bg-gray-100 px-2 py-1 rounded hover:bg-gray-200 transition">Update</button>
                    <button @click="openInfo(d)" v-if="d.latest_check"
                            class="text-xs bg-gray-100 px-2 py-1 rounded hover:bg-gray-200 transition">Info</button>
                    <button @click="openEdit(d)"
                            class="text-xs bg-gray-100 px-2 py-1 rounded hover:bg-gray-200 transition">Edit</button>
                    <button @click="deleteDomain(d)"
                            class="text-xs bg-red-50 text-red-500 px-2 py-1 rounded hover:bg-red-100 transition">✕</button>
                </div>
            </div>
        </div>
    </div>

    <teleport to="body">
        <div v-show="modal.form" class="modal-overlay" style="display: none;" @click.self="closeModals">
            <div class="modal-box">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-semibold text-gray-900">@{{ editing ? 'Edit Domain' : 'Add Domain' }}</h2>
                    <button @click="closeModals" class="text-gray-400 hover:text-gray-700 text-lg">✕</button>
                </div>

                <div class="space-y-3 text-sm">
                    <div>
                        <label class="block text-gray-500 mb-1">Name <span class="text-gray-400">(optional)</span></label>
                        <input v-model="form.name" placeholder="My site"
                               class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black/20">
                    </div>
                    <div>
                        <label class="block text-gray-500 mb-1">URL <span class="text-red-400">*</span></label>
                        <input v-model="form.url" placeholder="https://example.com"
                               class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black/20">
                        <p v-if="errors.url" class="text-red-500 text-xs mt-1">@{{ errors.url[0] }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-gray-500 mb-1">Method</label>
                            <select v-model="form.method"
                                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black/20">
                                <option>GET</option>
                                <option>HEAD</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-500 mb-1">Timeout (sec)</label>
                            <input v-model.number="form.timeout" type="number" min="1" max="60"
                                   class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black/20">
                        </div>
                    </div>

                    <div class="flex items-center gap-3 py-1">

                        <label class="relative inline-flex items-center cursor-pointer">

                            <input type="checkbox"
                                v-model="form.auto_check"
                                class="sr-only peer">

                            <div class="w-8 h-5 bg-gray-200 rounded-full transition peer-checked:bg-gray-800"></div>

                            <div class="absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform"
                                :class="form.auto_check ? 'translate-x-4' : 'translate-x-0'"></div>

                        </label>

                        <span class="text-gray-600">Auto check</span>

                    </div>

                    <div v-if="form.auto_check" class="mt-3">
                        <label class="block text-gray-500 mb-1">Interval (minutes)</label>
                        <input v-model.number="form.interval"
                            type="number"
                            class="w-full border rounded-lg px-3 py-2">
                    </div>

                    <div v-if="form.auto_check" class="flex items-center gap-3 py-1">

                        <label class="relative inline-flex items-center cursor-pointer">

                            <input type="checkbox"
                                v-model="form.notify_on_down"
                                class="sr-only peer">

                            <div class="w-8 h-5 bg-gray-200 rounded-full transition peer-checked:bg-gray-800"></div>

                            <div class="absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform"
                                :class="form.notify_on_down ? 'translate-x-4' : 'translate-x-0'"></div>

                        </label>

                        <span class="text-gray-600 text-sm">Notify when DOWN</span>

                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button @click="closeModals"
                            class="px-4 py-2 rounded-lg border text-sm hover:bg-gray-50 transition">Cancel</button>
                    <button @click="submitForm" :disabled="saving"
                            class="px-4 py-2 rounded-lg bg-gray-800 text-white text-sm hover:bg-gray-800 transition disabled:opacity-50">
                        @{{ saving ? 'Saving...' : (editing ? 'Save' : 'Add Domain') }}
                    </button>
                </div>
            </div>
        </div>



        <div v-show="modal.info" class="modal-overlay" style="display: none;" @click.self="closeModals">
            <div class="modal-box" v-if="selected">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h2 class="font-semibold text-gray-900">@{{ label(selected) }}</h2>
                        <p class="text-xs text-gray-400 truncate max-w-xs">@{{ selected.url }}</p>
                    </div>
                    <button @click="closeModals" class="text-gray-400 hover:text-gray-700 text-lg ml-2">✕</button>
                </div>

                <div v-if="selected.latest_check" class="space-y-0">
                    <div class="mb-4">
                        <span v-if="isUp(selected)"
                              class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> UP — Available
                        </span>
                        <span v-else
                              class="inline-flex items-center gap-1.5 bg-red-100 text-red-700 text-xs font-semibold px-3 py-1 rounded-full">
                            <span class="w-2 h-2 bg-red-500 rounded-full"></span> DOWN — Unavailable
                        </span>
                    </div>

                    <div class="text-sm divide-y">
                        <div class="flex justify-between py-2">
                            <span class="text-gray-500">HTTP Code</span>
                            <span :class="codeColor(selected.latest_check.status_code)"
                                  class="font-mono font-semibold">
                                @{{ selected.latest_check.status_code ?? '—' }}
                            </span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-gray-500">Response Time</span>
                            <span class="font-mono font-semibold">@{{ selected.latest_check.response_time ? selected.latest_check.response_time + ' ms' : '—' }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-gray-500">Method</span>
                            <span class="font-mono">@{{ selected.method }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-gray-500">Auto check</span>
                            <span>@{{ selected.auto_check ? `Every ${selected.interval} min` : 'Off' }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-gray-500">Checked At</span>
                            <span class="text-gray-700">@{{ formatDate(selected.latest_check.created_at) }}</span>
                        </div>
                        <div v-if="selected.latest_check.error" class="flex justify-between py-2">
                            <span class="text-gray-500">Error</span>
                            <span class="text-red-500 text-xs max-w-[200px] text-right">@{{ selected.latest_check.error }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    <button
                        @click="loadHistory(selected)"
                        class="w-full bg-gray-100 hover:bg-gray-200 transition rounded-lg py-2 text-sm">
                        Load last 10 checks
                    </button>
                </div>

                <div v-if="loadingHistory" class="text-center text-sm text-gray-400 mt-3">
                    Loading...
                </div>

                <div v-if="history.length" class="mt-4 border rounded-lg overflow-hidden">

                    <div v-for="item in history"
                        class="border-b last:border-b-0 p-3 text-xs flex justify-between items-center">

                        <div>
                            <div :class="item.is_up ? 'text-green-600' : 'text-red-500'">
                                @{{ item.is_up ? 'UP' : 'DOWN' }}
                            </div>

                            <div class="text-gray-400">
                                @{{ formatDate(item.created_at) }}
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="font-mono">
                                @{{ item.status_code ?? 'ERR' }}
                            </div>

                            <div class="text-gray-400">
                                @{{ item.response_time ?? '—' }} ms
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <div v-if="rateLimitModal" style="display: none;" class="modal-overlay" @click.self="rateLimitModal = false">

            <div class="modal-box text-center">

                <h2 class="font-semibold text-red-600 mb-2">
                    Limit
                </h2>

                <p class="text-sm text-gray-600">
                    @{{ rateLimitMsg }}
                </p>

                <button
                    @click="rateLimitModal = false"
                    class="mt-4 px-4 py-2 bg-gray-800 text-white rounded-lg text-sm">
                    Close
                </button>

            </div>

        </div>

        <div v-show="modal.settings" class="modal-overlay" @click.self="closeModals">

            <div class="modal-box">

                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-semibold text-gray-900">Settings</h2>
                    <button @click="closeModals" class="text-gray-400 hover:text-gray-700 text-lg">✕</button>
                </div>

                <div class="space-y-4 text-sm">

                    <div>
                        <label class="block text-gray-500 mb-1">Telegram Bot Token</label>
                        <input v-model="settings.telegram_token"
                            placeholder="123456:ABC-DEF..."
                            class="w-full border rounded-lg px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-gray-500 mb-1">Telegram Chat ID</label>
                        <input v-model="settings.telegram_chat_id"
                            placeholder="123456789"
                            class="w-full border rounded-lg px-3 py-2">
                    </div>

                    <div class="text-xs text-gray-400">
                        Notifications will be sent when domains go DOWN.
                    </div>

                </div>

                <div class="flex justify-end gap-2 mt-5">

                    <button @click="closeModals"
                            class="px-4 py-2 rounded-lg border text-sm">
                        Cancel
                    </button>

                    <button @click="saveSettings"
                            class="px-4 py-2 rounded-lg bg-gray-800 text-white text-sm">
                        Save
                    </button>

                </div>

            </div>

        </div>
    </teleport>


</div>

<style>
.modal-overlay {
    position: fixed; inset: 0; z-index: 50;
    display: flex; align-items: center; justify-content: center; padding: 1rem;
    background: rgba(0,0,0,0.45);
    animation: fadeIn .2s ease;
}
.modal-box {
    background: #fff; width: 100%; max-width: 26rem;
    border-radius: 1rem; box-shadow: 0 20px 60px rgba(0,0,0,.2); padding: 1.5rem;
    animation: scaleIn .2s ease;
}
@keyframes fadeIn  { from { opacity: 0 } to { opacity: 1 } }
@keyframes scaleIn { from { transform: scale(.95) } to { transform: scale(1) } }
input[type=checkbox].sr-only:checked ~ div div { transform: translateX(1rem); }
</style>

<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>

<script>

const { createApp, ref, reactive } = Vue;

createApp({
    setup() {
    
        const domains = ref(@json($domains));
        const selected = ref(null);
        const editing  = ref(false);
        const saving   = ref(false);
        const errors   = ref({});
        const modal    = reactive({ form: false, info: false, settings: false });
        const history = ref([]);
        const loadingHistory = ref(false);
        const rateLimitModal = ref(false);
        const rateLimitMsg = ref('');
        const settings = ref({
            telegram_token: '',
            telegram_chat_id: ''
        });

        const defaultForm = () => ({
            name: '',
            url: '',
            method: 'GET',
            timeout: 5,
            auto_check: false,
            interval: 5,
            notify_on_down: false, 
        });
        const form = reactive(defaultForm());

        const normalizeUrl = (url) => {
            if (!url) return '';
            if (!url.startsWith('http')) return 'https://' + url;
            return url;
        };

        const label = (d) => {
            try {
                return d.name || new URL(normalizeUrl(d.url)).hostname;
            } catch {
                return d.url;
            }
        };
        const isUp   = d => d.latest_check && d.latest_check.status_code >= 200 && d.latest_check.status_code < 300;
        const codeColor = c => !c ? 'text-gray-400' : c < 300 ? 'text-green-600' : c < 400 ? 'text-yellow-500' : 'text-red-600';
        const formatDate = s => s ? new Date(s).toLocaleString('uk-UA', {
            day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit'
        }) : '—';

        function openAdd() {
            Object.assign(form, defaultForm());
            editing.value = false;
            selected.value = null;
            errors.value = {};
            modal.form = true;
        }

        function openEdit(d) {
            Object.assign(form, {
                name: d.name ?? '',
                url: d.url,
                method: d.method,
                timeout: d.timeout,
                auto_check: !!d.auto_check,
                interval: d.interval ?? 5,
                notify_on_down: !!d.notify_on_down, 
            });

            selected.value = d;
            editing.value = true;
            errors.value = {};
            modal.form = true;
        }

        function openInfo(d) {
            selected.value = d;
            modal.info = true;
        }

        async function openSettings() {
            const res = await fetch('/settings', {
                headers: { 'Accept': 'application/json' }
            });

            const data = await res.json();

            settings.value = {
                telegram_token: data.telegram_token ?? '',
                telegram_chat_id: data.telegram_chat_id ?? ''
            };

            modal.settings = true;
        }

        function closeModals() {
            modal.form = false;
            modal.info = false;
            modal.settings = false;
            selected.value = null;
            history.value = [];
        }

        async function submitForm() {
            saving.value = true;
            errors.value = {};
            const token = document.querySelector('meta[name=csrf-token]').content;

            try {
                const url    = editing.value ? `/domains/${selected.value.id}` : '/domains';
                const method = editing.value ? 'PUT' : 'POST';
                console.log(form);
                const res  = await fetch(url, {
                    method,
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                    body: JSON.stringify(form),
                });
                const data = await res.json();
                console.log(data);
                if (!res.ok) { errors.value = data.errors ?? {}; return; }

                if (editing.value) {
                    const idx = domains.value.findIndex(d => d.id === selected.value.id);
                    if (idx !== -1) domains.value[idx] = data.domain;
                } else {
                    domains.value.unshift(data.domain);
                }
                closeModals();
            } finally {
                saving.value = false;
            }
        }

        async function deleteDomain(d) {
            if (!confirm(`Delete ${label(d)}?`)) return;
            const token = document.querySelector('meta[name=csrf-token]').content;
            await fetch(`/domains/${d.id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': token } });
            domains.value = domains.value.filter(x => x.id !== d.id);
        }

        async function checkNow(d) {
            const token = document.querySelector('meta[name=csrf-token]').content;

            const res = await fetch(`/domains/${d.id}/check`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            });
            if (res.status == 429) {
                const data = await res.json().catch(() => ({}));
                rateLimitMsg.value = data.message || 'Too many requests';
                rateLimitModal.value = true;
                return;
            }

            const data = await res.json();

            
            const idx = domains.value.findIndex(x => x.id === d.id);

            if (idx !== -1 && data.domain) {
                domains.value[idx] = data.domain;
            }
        }

        async function loadHistory(d) {

            loadingHistory.value = true;

            const res = await fetch(`/domains/${d.id}/history`, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();

            history.value = data.history;

            loadingHistory.value = false;
        }

        async function saveSettings() {
            const token = document.querySelector('meta[name=csrf-token]').content;

            const res = await fetch('/settings', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(settings.value)
            });

            const data = await res.json().catch(() => null);

            console.log('STATUS:', res.status);
            console.log('DATA:', data);

            if (!res.ok) {
                alert('Ошибка сохранения');
                return;
            }

            modal.settings = false;
        }


        window.addEventListener('keydown', e => { if (e.key === 'Escape') closeModals(); });

        return {
    domains, selected, editing, saving, errors, modal, form,
    label, isUp, codeColor, formatDate,
    openAdd, openEdit, openInfo, closeModals, submitForm, deleteDomain,
    checkNow, history, loadingHistory, loadHistory,
    rateLimitModal, rateLimitMsg,
    settings, 
    openSettings,
    saveSettings
};
    }
}).mount('#app');
</script>

</x-app-layout>