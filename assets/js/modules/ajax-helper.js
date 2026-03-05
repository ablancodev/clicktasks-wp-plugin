/**
 * CT.Ajax - AJAX helper using fetch + FormData
 */
CT.Ajax = {
    async post(action, data = {}) {
        const fd = new FormData();
        fd.append('action', action);
        fd.append('nonce', ctData.nonce);

        Object.entries(data).forEach(([key, val]) => {
            if (val === null || val === undefined) return;
            if (typeof val === 'object') {
                fd.append(key, JSON.stringify(val));
            } else {
                fd.append(key, val);
            }
        });

        try {
            const resp = await fetch(ctData.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' });
            const json = await resp.json();

            if (!json.success) {
                const msg = json.data?.message || 'Error desconocido';
                console.error('CT Ajax Error:', msg);
                return { ok: false, error: msg };
            }

            return { ok: true, data: json.data };
        } catch (err) {
            console.error('CT Ajax Exception:', err);
            return { ok: false, error: err.message };
        }
    }
};
