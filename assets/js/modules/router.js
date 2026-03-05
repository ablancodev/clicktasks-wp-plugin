/**
 * CT.Router - Internal state manager
 */
CT.Router = {
    currentView: 'kanban',
    currentListId: null,
    currentListData: null,

    init() {
        const btns = document.querySelectorAll('#ct-view-toggle button');
        btns.forEach(btn => {
            btn.addEventListener('click', () => {
                this.currentView = btn.dataset.view;
                this.updateToggle();
                this.loadView();
            });
        });
    },

    updateToggle() {
        document.querySelectorAll('#ct-view-toggle button').forEach(btn => {
            const isActive = btn.dataset.view === this.currentView;
            if (isActive) {
                btn.className = 'ct-flex ct-items-center ct-gap-1.5 ct-px-2.5 ct-py-1 ct-text-[13px] ct-font-semibold ct-rounded-md ct-bg-white ct-text-ct-indigo ct-shadow-sm';
            } else {
                btn.className = 'ct-flex ct-items-center ct-gap-1.5 ct-px-2.5 ct-py-1 ct-text-[13px] ct-rounded-md ct-text-ct-text-s hover:ct-text-ct-text';
            }
        });
    },

    async navigateToList(listId) {
        this.currentListId = listId;

        const res = await CT.Ajax.post('ct_get_list', { id: listId });
        if (!res.ok) return;

        this.currentListData = res.data;

        document.getElementById('ct-btn-new-task').style.display = '';
        document.getElementById('ct-filter-toggle').style.display = '';
        document.getElementById('ct-filters-bar').style.display = 'none';
        CT.Filters.populateStatuses(res.data.statuses);
        this.updateBreadcrumb();
        this.loadView();
    },

    updateBreadcrumb() {
        const bc = document.getElementById('ct-breadcrumb');
        if (!this.currentListData) {
            bc.innerHTML = '';
            return;
        }

        const tree = CT.Sidebar.treeData || [];
        let wsName = '', folderName = '';

        for (const ws of tree) {
            for (const f of ws.folders || []) {
                for (const l of f.lists || []) {
                    if (l.id === this.currentListId) {
                        wsName = ws.title;
                        folderName = f.title;
                    }
                }
            }
        }

        const parts = [wsName, folderName, this.currentListData.title].filter(Boolean);
        bc.innerHTML = parts.map((n, i) => {
            const isLast = i === parts.length - 1;
            const cls = isLast ? 'ct-text-ct-text ct-font-semibold' : 'ct-text-ct-text-s';
            return `<span class="${cls}">${CT.Utils.escHtml(n)}</span>`;
        }).join('<i class="fa-solid fa-chevron-right ct-text-[9px] ct-text-ct-muted ct-mx-1"></i>');
    },

    async loadView() {
        if (!this.currentListId) return;

        CT.Utils.hide('#ct-empty-state');
        CT.Utils.hide('#ct-kanban-view');
        CT.Utils.hide('#ct-list-view');
        CT.Utils.show('#ct-loading');

        const filters = CT.Filters.getValues();
        const res = await CT.Ajax.post('ct_get_tasks', {
            list_id: this.currentListId,
            ...filters,
        });

        CT.Utils.hide('#ct-loading');

        if (!res.ok) return;

        if (this.currentView === 'kanban') {
            CT.Utils.show('#ct-kanban-view');
            CT.Kanban.render(res.data, this.currentListData.statuses);
        } else {
            CT.Utils.show('#ct-list-view');
            CT.ListView.render(res.data, this.currentListData.statuses);
        }
    },
};
