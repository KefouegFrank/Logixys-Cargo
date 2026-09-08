{{--
    Keeps the in-progress form in localStorage as the agent types, and offers it back if
    the page is reopened before a save — a reload, a crash, or a stray tab close loses
    nothing. Only what differs from the record as loaded is stored, so a plain save
    followed by a reload has no draft to offer.
--}}
<div
    wire:ignore
    x-data="{
        draftKey: @js($draftKey),
        transientKeys: @js($transientKeys),
        maxAgeMs: 3 * 24 * 60 * 60 * 1000,
        debounceMs: 800,
        banner: null,
        saveTimer: null,
        baselineTimer: null,
        baseline: null,
        suppressed: false,

        init() {
            this.baseline = this.fingerprint(this.$wire.data);
            this.offerDraft();

            this.$watch('$wire.data', () => {
                clearTimeout(this.saveTimer);
                this.saveTimer = setTimeout(() => this.persist(), this.debounceMs);
            });

            window.addEventListener('beforeunload', () => {
                clearTimeout(this.saveTimer);
                this.persist();
            });

            // Dispatched by the page after a successful create/save.
            window.addEventListener('shipment-draft-saved', () => this.rebaseline());
        },

        // Plain, proxy-free copy of the form state without the sidebar's event fields:
        // those describe the save being posted, not the shipment, and their defaults move
        // with the clock, which alone made every reload look like unsaved work.
        snapshot(data) {
            try {
                const copy = JSON.parse(JSON.stringify(data ?? {}));

                this.transientKeys.forEach((key) => delete copy[key]);

                return copy;
            } catch (e) {
                return null;
            }
        },

        // Key order in the state is not stable between renders, so compare sorted.
        sortKeys(value) {
            if (Array.isArray(value)) return value.map((item) => this.sortKeys(item));

            if (value === null || typeof value !== 'object') return value;

            return Object.keys(value).sort().reduce((out, key) => {
                out[key] = this.sortKeys(value[key]);

                return out;
            }, {});
        },

        fingerprint(data) {
            const copy = this.snapshot(data);

            return copy === null ? null : JSON.stringify(this.sortKeys(copy));
        },

        readStore() {
            try {
                const raw = localStorage.getItem(this.draftKey);

                return raw ? JSON.parse(raw) : null;
            } catch (e) {
                return null;
            }
        },

        forget() {
            try {
                localStorage.removeItem(this.draftKey);
            } catch (e) {}
        },

        persist() {
            const current = this.fingerprint(this.$wire.data);

            if (this.suppressed || current === null || current === this.baseline) {
                this.forget();

                return;
            }

            try {
                localStorage.setItem(this.draftKey, JSON.stringify({
                    savedAt: Date.now(),
                    data: this.snapshot(this.$wire.data),
                }));
            } catch (e) {
                // Storage full or unavailable (private browsing) — typing still works,
                // it just isn't protected.
            }
        },

        rebaseline() {
            clearTimeout(this.saveTimer);
            clearTimeout(this.baselineTimer);
            this.banner = null;
            this.suppressed = true;
            this.baseline = this.fingerprint(this.$wire.data);
            this.forget();

            // Saving re-renders the form, which trips the watcher; nothing is written
            // back until that has landed and the baseline has moved on, or the state just
            // saved would be offered back as a draft on the next visit.
            this.baselineTimer = setTimeout(() => {
                this.baseline = this.fingerprint(this.$wire.data);
                this.suppressed = false;
                this.forget();
            }, 400);
        },

        dismiss() {
            this.banner = null;
            this.forget();
        },

        offerDraft() {
            const stored = this.readStore();

            if (! stored || ! stored.data || typeof stored.data !== 'object' || Array.isArray(stored.data)) {
                this.forget();

                return;
            }

            if (! Number.isFinite(stored.savedAt) || Date.now() - stored.savedAt > this.maxAgeMs) {
                this.forget();

                return;
            }

            if (JSON.stringify(this.sortKeys(stored.data)) === this.baseline) {
                this.forget();

                return;
            }

            this.banner = stored;
        },

        restore() {
            if (! this.banner) return;

            let merged;

            try {
                merged = JSON.parse(JSON.stringify(this.$wire.data ?? {}));
            } catch (e) {
                return;
            }

            // Only fields this form still has, so a draft left from an earlier version of
            // the screen can't push unknown keys into the component's state. The event
            // fields keep what is on screen now — the draft never carried them.
            Object.keys(this.banner.data)
                .filter((key) => key in merged && ! this.transientKeys.includes(key))
                .forEach((key) => { merged[key] = this.banner.data[key]; });

            this.$wire.set('data', merged);
            this.banner = null;
        },
    }"
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
                x-on:click="dismiss()"
                class="rounded-md border border-warning-300 px-3 py-1.5 font-semibold text-warning-800 transition-colors hover:bg-warning-100 dark:text-warning-200 dark:hover:bg-warning-900"
            >Ignorer</button>
        </div>
    </div>
</div>
