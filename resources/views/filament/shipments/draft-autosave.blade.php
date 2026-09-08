{{--
    Saves the in-progress form to the browser's localStorage as the agent types, and
    offers to restore it if the page is reopened before a save — reload, crash, or an
    accidental navigation away all lose nothing.
--}}
<div
    wire:ignore
    x-data="{
        draftKey: @js($draftKey),
        maxAgeMs: 3 * 24 * 60 * 60 * 1000,
        banner: null,
        saveTimer: null,

        init() {
            this.checkForDraft();

            this.$watch('$wire.data', () => {
                clearTimeout(this.saveTimer);
                this.saveTimer = setTimeout(() => this.saveDraft(), 800);
            });

            window.addEventListener('beforeunload', () => this.saveDraft());

            // Dispatched by the page after a successful create/save — nothing left to protect.
            window.addEventListener('shipment-draft-saved', () => this.clearDraft());
        },

        readStore() {
            try {
                const raw = localStorage.getItem(this.draftKey);

                return raw ? JSON.parse(raw) : null;
            } catch (e) {
                return null;
            }
        },

        saveDraft() {
            try {
                localStorage.setItem(this.draftKey, JSON.stringify({
                    savedAt: Date.now(),
                    data: this.$wire.data,
                }));
            } catch (e) {
                // Storage full or unavailable (private browsing) — typing still works,
                // it just isn't protected.
            }
        },

        clearDraft() {
            try {
                localStorage.removeItem(this.draftKey);
            } catch (e) {}

            this.banner = null;
        },

        checkForDraft() {
            const stored = this.readStore();

            if (! stored || ! stored.data) return;

            if (Date.now() - stored.savedAt > this.maxAgeMs) {
                this.clearDraft();

                return;
            }

            // Nothing to offer if it matches what's already loaded (e.g. right after
            // this same save cleared and re-wrote the key before the page finished).
            if (JSON.stringify(stored.data) === JSON.stringify(this.$wire.data)) return;

            this.banner = stored;
        },

        restore() {
            if (! this.banner) return;

            this.$wire.set('data', this.banner.data);
            this.banner = null;
        },
    }"
    x-init="init()"
>
    <div
        x-show="banner"
        x-cloak
        x-transition
        class="mb-4 flex items-start justify-between gap-4 rounded-lg border border-warning-300 bg-warning-50 px-4 py-3 text-sm dark:border-warning-800 dark:bg-warning-950"
    >
        <div class="text-warning-800 dark:text-warning-200">
            <p class="font-semibold">Brouillon non enregistré trouvé</p>
            <p>Des informations saisies précédemment n'ont pas été enregistrées. Voulez-vous les restaurer ?</p>
        </div>
        <div class="flex shrink-0 gap-2">
            <button
                type="button"
                x-on:click="restore()"
                class="rounded-md bg-warning-600 px-3 py-1.5 font-semibold text-white transition-colors hover:bg-warning-500"
            >Restaurer</button>
            <button
                type="button"
                x-on:click="clearDraft()"
                class="rounded-md border border-warning-300 px-3 py-1.5 font-semibold text-warning-800 transition-colors hover:bg-warning-100 dark:text-warning-200 dark:hover:bg-warning-900"
            >Ignorer</button>
        </div>
    </div>
</div>
