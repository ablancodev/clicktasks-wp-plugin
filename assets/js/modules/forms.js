/**
 * CT.Forms - CRUD modal forms
 */
CT.Forms = {
    users: null,

    init() {
        document.getElementById('ct-btn-new-task')?.addEventListener('click', () => this.taskForm());

        document.getElementById('ct-btn-add-workspace')?.addEventListener('click', () => this.workspaceForm());
    },

    /* ---- Workspace ---- */
    workspaceForm(existing = null) {
        CT.Modal.form({
            title: existing ? 'Editar Workspace' : 'Nuevo Workspace',
            fields: CT.Modal.inputField('title', ctData.i18n.title, existing?.title || '', 'text', 'required autofocus'),
            deleteAction: existing ? async () => {
                await CT.Ajax.post('ct_delete_workspace', { id: existing.id });
                CT.Sidebar.load();
            } : null,
            async onSubmit(fd) {
                const title = fd.get('title');
                if (existing) {
                    await CT.Ajax.post('ct_update_workspace', { id: existing.id, title });
                } else {
                    await CT.Ajax.post('ct_create_workspace', { title });
                }
                CT.Sidebar.load();
            }
        });
    },

    /* ---- Folder ---- */
    folderForm(workspaceId, existing = null) {
        CT.Modal.form({
            title: existing ? 'Editar Carpeta' : 'Nueva Carpeta',
            fields: CT.Modal.inputField('title', ctData.i18n.title, existing?.title || '', 'text', 'required autofocus'),
            deleteAction: existing ? async () => {
                await CT.Ajax.post('ct_delete_folder', { id: existing.id });
                CT.Sidebar.load();
            } : null,
            async onSubmit(fd) {
                const title = fd.get('title');
                if (existing) {
                    await CT.Ajax.post('ct_update_folder', { id: existing.id, title });
                } else {
                    await CT.Ajax.post('ct_create_folder', { title, workspace_id: workspaceId });
                }
                CT.Sidebar.load();
            }
        });
    },

    /* ---- List ---- */
    listForm(folderId, existing = null) {
        CT.Modal.form({
            title: existing ? 'Editar Lista' : 'Nueva Lista',
            fields: CT.Modal.inputField('title', ctData.i18n.title, existing?.title || '', 'text', 'required autofocus'),
            deleteAction: existing ? async () => {
                await CT.Ajax.post('ct_delete_list', { id: existing.id });
                CT.Sidebar.load();
                if (CT.Router.currentListId === existing.id) {
                    CT.Router.currentListId = null;
                    CT.Utils.show('#ct-empty-state');
                    CT.Utils.hide('#ct-kanban-view');
                    CT.Utils.hide('#ct-list-view');
                    document.getElementById('ct-btn-new-task').style.display = 'none';
                    document.getElementById('ct-filters-bar').style.display = 'none';
                }
            } : null,
            async onSubmit(fd) {
                const title = fd.get('title');
                if (existing) {
                    await CT.Ajax.post('ct_update_list', { id: existing.id, title });
                } else {
                    await CT.Ajax.post('ct_create_list', { title, folder_id: folderId });
                }
                CT.Sidebar.load();
            }
        });
    },

    /* ---- Task ---- */
    async taskForm(existing = null, defaultStatus = null) {
        if (!this.users) {
            const res = await CT.Ajax.post('ct_get_users');
            this.users = res.ok ? res.data : [];
        }

        const statuses = CT.Router.currentListData?.statuses || [];

        const statusOpts = statuses.map(s => ({ value: s.name, label: s.name }));
        const priorityOpts = [
            { value: 'urgent', label: ctData.i18n.urgent },
            { value: 'high',   label: ctData.i18n.high },
            { value: 'normal', label: ctData.i18n.normal },
            { value: 'low',    label: ctData.i18n.low },
        ];

        const assignedIds = (existing?.assigned || []).map(a => a.id);
        const userCheckboxes = this.users.map(u => {
            const checked = assignedIds.includes(u.id) ? 'checked' : '';
            return `<label class="ct-flex ct-items-center ct-gap-2 ct-text-sm ct-text-ct-text">
                <input type="checkbox" name="assigned" value="${u.id}" ${checked} class="ct-rounded">
                <img src="${CT.Utils.escAttr(u.avatar)}" class="ct-w-5 ct-h-5 ct-rounded-full"> ${CT.Utils.escHtml(u.name)}
            </label>`;
        }).join('');

        const fields = [
            CT.Modal.inputField('title', ctData.i18n.title, existing?.title || '', 'text', 'required autofocus'),
            CT.Modal.textareaField('description', ctData.i18n.description, existing?.description || ''),
            `<div class="ct-grid ct-grid-cols-2 ct-gap-3">
                ${CT.Modal.selectField('status', ctData.i18n.status, statusOpts, existing?.status || defaultStatus || 'To Do')}
                ${CT.Modal.selectField('priority', ctData.i18n.priority, priorityOpts, existing?.priority || 'normal')}
            </div>`,
            CT.Modal.inputField('due_date', ctData.i18n.due_date, existing?.due_date || '', 'date'),
            CT.Modal.inputField('tags', ctData.i18n.tags, (existing?.tags || []).join(', '), 'text', 'placeholder="tag1, tag2"'),
            `<div>
                <label class="ct-block ct-text-sm ct-text-ct-muted ct-mb-1">${ctData.i18n.assigned}</label>
                <div class="ct-space-y-1 ct-max-h-32 ct-overflow-y-auto ct-bg-ct-card ct-p-2 ct-rounded ct-border ct-border-ct-border">${userCheckboxes || '<span class="ct-text-xs ct-text-ct-muted">No hay usuarios</span>'}</div>
            </div>`,
        ].join('');

        CT.Modal.form({
            title: existing ? 'Editar Tarea' : 'Nueva Tarea',
            fields,
            deleteAction: existing ? async () => {
                await CT.Ajax.post('ct_delete_task', { id: existing.id });
                CT.TaskDetail.close();
                CT.Router.loadView();
            } : null,
            async onSubmit(fd) {
                const assigned = fd.getAll('assigned').map(Number);
                const tags = fd.get('tags');

                const payload = {
                    title: fd.get('title'),
                    description: fd.get('description'),
                    status: fd.get('status'),
                    priority: fd.get('priority'),
                    due_date: fd.get('due_date'),
                    assigned,
                    tags,
                };

                if (existing) {
                    payload.id = existing.id;
                    await CT.Ajax.post('ct_update_task', payload);
                    CT.TaskDetail.open(existing.id);
                } else {
                    payload.list_id = CT.Router.currentListId;
                    await CT.Ajax.post('ct_create_task', payload);
                }
                CT.Router.loadView();
            }
        });
    },
};
