/**
 * CT.Sidebar - Sidebar tree navigation (dark sidebar, light theme)
 */
CT.Sidebar = {
    treeData: [],

    init() {
        this.load();
        this.bindMobile();
    },

    async load() {
        const res = await CT.Ajax.post('ct_get_navigation');
        if (!res.ok) return;

        this.treeData = res.data;
        this.render();
    },

    render() {
        const container = document.getElementById('ct-sidebar-tree');
        const canManage = ctData.caps.manageWorkspaces;

        const wsColors = ['#8B5CF6', '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#EC4899', '#6366F1', '#14B8A6'];
        let html = '';

        this.treeData.forEach((ws, idx) => {
            const color = wsColors[idx % wsColors.length];
            html += `
            <div class="ct-tree-workspace ct-mb-1">
                <div class="ct-tree-item ct-flex ct-items-center ct-px-2 ct-gap-1.5 ct-group" data-type="workspace" data-id="${ws.id}" style="height:30px">
                    <button class="ct-chevron ct-expanded ct-text-[#71717A] ct-p-0.5 ct-shrink-0" data-toggle="workspace-${ws.id}">
                        <i class="fa-solid fa-chevron-right ct-text-[9px]"></i>
                    </button>
                    <i class="fa-solid fa-rocket ct-text-xs ct-shrink-0" style="color:${color}"></i>
                    <span class="ct-text-[13px] ct-text-ct-sidebar-t ct-flex-1 ct-truncate ct-tree-label">${CT.Utils.escHtml(ws.title)}</span>
                    ${canManage ? `<span class="ct-tree-actions ct-flex ct-gap-1 ct-shrink-0">
                        <button class="ct-text-[#71717A] hover:ct-text-white ct-text-xs" data-action="edit-workspace" data-id="${ws.id}" title="Editar">✎</button>
                        <button class="ct-text-[#71717A] hover:ct-text-white ct-text-xs" data-action="add-folder" data-id="${ws.id}" title="+Carpeta">+</button>
                    </span>` : ''}
                </div>
                <div class="ct-tree-children ct-ml-2" id="workspace-${ws.id}">`;

            (ws.folders || []).forEach(folder => {
                html += `
                <div class="ct-tree-folder">
                    <div class="ct-tree-item ct-flex ct-items-center ct-px-2 ct-gap-1.5 ct-group" data-type="folder" data-id="${folder.id}" style="height:28px">
                        <button class="ct-chevron ct-expanded ct-text-[#71717A] ct-p-0.5 ct-shrink-0" data-toggle="folder-${folder.id}">
                            <i class="fa-solid fa-chevron-right ct-text-[9px]"></i>
                        </button>
                        <i class="fa-solid fa-folder ct-text-xs ct-text-[#71717A] ct-shrink-0"></i>
                        <span class="ct-text-[13px] ct-text-ct-sidebar-t ct-flex-1 ct-truncate ct-tree-label">${CT.Utils.escHtml(folder.title)}</span>
                        ${canManage ? `<span class="ct-tree-actions ct-flex ct-gap-1 ct-shrink-0">
                            <button class="ct-text-[#71717A] hover:ct-text-white ct-text-xs" data-action="edit-folder" data-id="${folder.id}" data-ws="${ws.id}" title="Editar">✎</button>
                            <button class="ct-text-[#71717A] hover:ct-text-white ct-text-xs" data-action="add-list" data-id="${folder.id}" title="+Lista">+</button>
                        </span>` : ''}
                    </div>
                    <div class="ct-tree-children ct-ml-3" id="folder-${folder.id}">`;

                (folder.lists || []).forEach(list => {
                    const active = CT.Router.currentListId === list.id ? 'ct-active' : '';
                    html += `
                    <div class="ct-tree-item ct-flex ct-items-center ct-px-2 ct-gap-1.5 ${active}" data-type="list" data-id="${list.id}" style="height:28px">
                        <span class="ct-w-2 ct-h-2 ct-rounded-full ct-shrink-0" style="background:#7C3AED"></span>
                        <span class="ct-text-[13px] ct-text-ct-sidebar-t ct-flex-1 ct-truncate ct-tree-label">${CT.Utils.escHtml(list.title)}</span>
                        ${canManage ? `<span class="ct-tree-actions ct-shrink-0">
                            <button class="ct-text-[#71717A] hover:ct-text-white ct-text-xs" data-action="edit-list" data-id="${list.id}" data-folder="${folder.id}" title="Editar">✎</button>
                        </span>` : ''}
                    </div>`;
                });

                html += `</div></div>`;
            });

            html += `</div></div>`;
        });

        container.innerHTML = html;
        this.bindEvents();
    },

    bindEvents() {
        CT.Utils.delegate('#ct-sidebar-tree', '[data-toggle]', 'click', (e, btn) => {
            e.stopPropagation();
            const target = document.getElementById(btn.dataset.toggle);
            if (!target) return;
            if (target.style.display === 'none') {
                target.style.display = '';
                btn.classList.add('ct-expanded');
            } else {
                target.style.display = 'none';
                btn.classList.remove('ct-expanded');
            }
        });

        CT.Utils.delegate('#ct-sidebar-tree', '[data-type="list"]', 'click', (e, el) => {
            if (e.target.closest('[data-action]')) return;
            const id = parseInt(el.dataset.id);
            document.querySelectorAll('.ct-tree-item.ct-active').forEach(i => i.classList.remove('ct-active'));
            el.classList.add('ct-active');
            CT.Router.navigateToList(id);
            this.closeMobile();
        });

        CT.Utils.delegate('#ct-sidebar-tree', '[data-action]', 'click', (e, btn) => {
            e.stopPropagation();
            const action = btn.dataset.action;
            const id = parseInt(btn.dataset.id);

            switch (action) {
                case 'edit-workspace': {
                    const ws = this.treeData.find(w => w.id === id);
                    if (ws) CT.Forms.workspaceForm(ws);
                    break;
                }
                case 'add-folder':
                    CT.Forms.folderForm(id);
                    break;
                case 'edit-folder': {
                    const wsId = parseInt(btn.dataset.ws);
                    const ws = this.treeData.find(w => w.id === wsId);
                    const folder = ws?.folders?.find(f => f.id === id);
                    if (folder) CT.Forms.folderForm(wsId, folder);
                    break;
                }
                case 'add-list':
                    CT.Forms.listForm(id);
                    break;
                case 'edit-list': {
                    const folderId = parseInt(btn.dataset.folder);
                    let listData = null;
                    for (const ws of this.treeData) {
                        for (const f of ws.folders || []) {
                            const l = f.lists?.find(li => li.id === id);
                            if (l) { listData = l; break; }
                        }
                        if (listData) break;
                    }
                    if (listData) CT.Forms.listForm(folderId, listData);
                    break;
                }
            }
        });
    },

    bindMobile() {
        document.getElementById('ct-sidebar-toggle')?.addEventListener('click', () => {
            document.getElementById('ct-sidebar').dataset.open = 'true';
            document.getElementById('ct-sidebar-overlay').style.display = '';
        });

        document.getElementById('ct-sidebar-close')?.addEventListener('click', () => this.closeMobile());
        document.getElementById('ct-sidebar-overlay')?.addEventListener('click', () => this.closeMobile());
    },

    closeMobile() {
        document.getElementById('ct-sidebar').dataset.open = 'false';
        document.getElementById('ct-sidebar-overlay').style.display = 'none';
    },
};
