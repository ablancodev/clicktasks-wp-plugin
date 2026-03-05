/**
 * CT.Filters - Filter bar management
 */
CT.Filters = {
    init() {
        ['ct-filter-status', 'ct-filter-priority', 'ct-filter-assigned'].forEach(id => {
            document.getElementById(id)?.addEventListener('change', () => {
                CT.Router.loadView();
            });
        });

        // Toggle filter bar
        document.getElementById('ct-filter-toggle')?.addEventListener('click', () => {
            const bar = document.getElementById('ct-filters-bar');
            if (bar.style.display === 'none') {
                bar.style.display = '';
            } else {
                bar.style.display = 'none';
            }
        });

        this.loadUsers();
    },

    async loadUsers() {
        const res = await CT.Ajax.post('ct_get_users');
        if (!res.ok) return;

        const select = document.getElementById('ct-filter-assigned');
        res.data.forEach(u => {
            const opt = document.createElement('option');
            opt.value = u.id;
            opt.textContent = u.name;
            select.appendChild(opt);
        });
    },

    populateStatuses(statuses) {
        const select = document.getElementById('ct-filter-status');
        while (select.options.length > 1) select.remove(1);

        (statuses || []).forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.name;
            opt.textContent = s.name;
            select.appendChild(opt);
        });
    },

    getValues() {
        return {
            filter_status:   document.getElementById('ct-filter-status')?.value || '',
            filter_priority: document.getElementById('ct-filter-priority')?.value || '',
            filter_assigned: document.getElementById('ct-filter-assigned')?.value || '',
        };
    },
};
