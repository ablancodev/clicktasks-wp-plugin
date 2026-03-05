/**
 * CT.TaskDetail - Slide-in task detail panel (matching Pencil modal design)
 */
CT.TaskDetail = {
    currentTask: null,

    init() {
        document.getElementById('ct-task-panel-overlay')?.addEventListener('click', () => this.close());
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && this.currentTask) this.close();
        });
    },

    async open(taskId) {
        const res = await CT.Ajax.post('ct_get_task', { id: taskId });
        if (!res.ok) return;

        this.currentTask = res.data;
        this.renderPanel();

        const panel = document.getElementById('ct-task-panel');
        panel.classList.remove('ct-translate-x-full');
        panel.classList.add('ct-panel-open');
        document.getElementById('ct-task-panel-overlay').style.display = '';
    },

    close() {
        this.currentTask = null;
        const panel = document.getElementById('ct-task-panel');
        panel.classList.add('ct-translate-x-full');
        panel.classList.remove('ct-panel-open');
        document.getElementById('ct-task-panel-overlay').style.display = 'none';
    },

    renderPanel() {
        const t = this.currentTask;
        if (!t) return;

        const statuses = CT.Router.currentListData?.statuses || [];
        const pColor = CT.Utils.priorityColor(t.priority);
        const pBg = CT.Utils.priorityBg(t.priority);
        const sColor = CT.Utils.statusColor(t.status, statuses);
        const sBg = CT.Utils.statusBg(sColor);
        const canManage = ctData.caps.manageTasks;

        const statusOptions = statuses.map(s =>
            `<option value="${CT.Utils.escAttr(s.name)}" ${s.name === t.status ? 'selected' : ''}>${CT.Utils.escHtml(s.name)}</option>`
        ).join('');

        const priorityOptions = ['urgent', 'high', 'normal', 'low'].map(p =>
            `<option value="${p}" ${p === t.priority ? 'selected' : ''}>${CT.Utils.priorityLabel(p)}</option>`
        ).join('');

        /* Assignee avatars for status row */
        const assigneeAvatars = (t.assigned || []).map(u =>
            `<span class="ct-inline-flex ct-items-center ct-justify-center ct-rounded-full ct-text-white ct-shrink-0" style="width:24px;height:24px;font-size:9px;font-weight:600;background:${CT.Utils.avatarColor(u.id)}">${CT.Utils.initials(u.name)}</span>`
        ).join('');

        const html = `
        <div class="ct-flex ct-flex-col ct-h-full">
            <!-- Top Bar -->
            <div class="ct-flex ct-items-center ct-justify-between ct-shrink-0" style="height:48px;padding:0 16px;border-bottom:1px solid #E5E7EB">
                <div class="ct-flex ct-items-center ct-gap-1.5" style="font-size:12px;color:#6B7280">
                    <span>${CT.Utils.escHtml(CT.Router.currentListData?.title || '')}</span>
                </div>
                <div class="ct-flex ct-items-center ct-gap-1">
                    ${canManage ? `<button id="ct-task-edit" class="ct-flex ct-items-center ct-justify-center ct-rounded-md hover:ct-bg-ct-surface-2 ct-text-ct-text-s hover:ct-text-ct-text ct-transition-colors" style="width:28px;height:28px" title="Editar">
                        <i class="fa-solid fa-pen" style="font-size:13px"></i>
                    </button>` : ''}
                    <button id="ct-task-close" class="ct-flex ct-items-center ct-justify-center ct-rounded-md ct-transition-colors" style="width:28px;height:28px;background:#F3F4F6;color:#374151">
                        <i class="fa-solid fa-xmark" style="font-size:14px"></i>
                    </button>
                </div>
            </div>

            <!-- Body: left column + right sidebar -->
            <div class="ct-flex ct-flex-1 ct-overflow-hidden">
                <!-- Left Column -->
                <div class="ct-flex-1 ct-overflow-y-auto ct-min-w-0" style="padding:24px;display:flex;flex-direction:column;gap:20px">
                    <!-- Status + Priority + Assignees row -->
                    <div class="ct-flex ct-items-center ct-gap-2 ct-flex-wrap">
                        ${canManage ?
                            `<select id="ct-detail-status" class="ct-rounded-md ct-border-0 ct-cursor-pointer" style="font-size:11px;font-weight:600;padding:5px 10px;background:${sBg};color:${sColor}">${statusOptions}</select>` :
                            `<span class="ct-inline-flex ct-items-center ct-gap-1.5 ct-rounded-md" style="font-size:11px;font-weight:600;padding:5px 10px;background:${sBg};color:${sColor}"><span class="ct-rounded-full" style="background:${sColor};width:6px;height:6px"></span>${CT.Utils.escHtml(t.status)}</span>`
                        }
                        ${canManage ?
                            `<select id="ct-detail-priority" class="ct-rounded-md ct-border-0 ct-cursor-pointer" style="font-size:11px;font-weight:600;padding:5px 10px;background:${pBg};color:${pColor}">${priorityOptions}</select>` :
                            `<span class="ct-inline-flex ct-items-center ct-gap-1.5 ct-rounded-md" style="font-size:11px;font-weight:600;padding:5px 10px;background:${pBg};color:${pColor}"><span class="ct-rounded-full" style="background:${pColor};width:6px;height:6px"></span>${CT.Utils.priorityLabel(t.priority)}</span>`
                        }
                        ${assigneeAvatars ? `<div class="ct-flex ct-items-center" style="gap:4px;margin-left:4px">${assigneeAvatars}</div>` : ''}
                    </div>

                    <!-- Title -->
                    <h2 style="font-size:22px;font-weight:700;color:#111827;line-height:1.3;margin:0">${CT.Utils.escHtml(t.title)}</h2>

                    <!-- Description -->
                    <div style="background:#F9FAFB;border-radius:8px;padding:12px;font-size:14px;color:#374151;line-height:1.5;min-height:60px">
                        ${t.description ? CT.Utils.escHtml(t.description) : '<span style="color:#9CA3AF;font-style:italic">Sin descripcion</span>'}
                    </div>

                    <!-- Tags -->
                    ${t.tags?.length ? `
                    <div class="ct-flex ct-flex-wrap ct-gap-1.5">${CT.Utils.tagsPills(t.tags)}</div>` : ''}

                    <!-- Activity / Comments section -->
                    <div>
                        <div class="ct-flex ct-items-center ct-gap-4" style="border-bottom:1px solid #E5E7EB;padding-bottom:8px;margin-bottom:16px">
                            <span style="font-size:13px;font-weight:600;color:#374151">Activity</span>
                            <span class="ct-rounded-md" style="font-size:12px;font-weight:500;color:#7C3AED;background:#EDE9FE;padding:4px 10px">Comments</span>
                        </div>
                        <div id="ct-detail-comments"></div>
                    </div>
                </div>

                <!-- Right Sidebar -->
                <div class="max-md:ct-hidden ct-shrink-0" style="width:280px;background:#FAFAFA;border-left:1px solid #E5E7EB;overflow-y:auto;padding:20px;display:flex;flex-direction:column;gap:12px">
                    <span style="font-size:10px;font-weight:600;color:#9CA3AF;letter-spacing:1px">DETAILS</span>

                    <div style="display:flex;flex-direction:column;padding-top:12px">
                        <!-- Assignees -->
                        <div class="ct-flex ct-items-center ct-justify-between" style="padding:8px 0">
                            <span style="font-size:12px;color:#6B7280">Assignees</span>
                            <div>${t.assigned?.length ? CT.Utils.avatarStack(t.assigned, 5) : '<span style="font-size:12px;color:#9CA3AF">--</span>'}</div>
                        </div>

                        <!-- Due date -->
                        <div class="ct-flex ct-items-center ct-justify-between" style="padding:8px 0">
                            <span style="font-size:12px;color:#6B7280">Due Date</span>
                            <span style="font-size:13px;color:#111827;font-weight:500">${CT.Utils.formatDate(t.due_date) || '--'}</span>
                        </div>

                        <!-- Created -->
                        <div class="ct-flex ct-items-center ct-justify-between" style="padding:8px 0">
                            <span style="font-size:12px;color:#6B7280">Created</span>
                            <span style="font-size:13px;color:#111827;font-weight:500">${new Date(t.created).toLocaleDateString('es-ES', { month: 'short', day: 'numeric', year: 'numeric' })}</span>
                        </div>

                        <!-- List -->
                        <div class="ct-flex ct-items-center ct-justify-between" style="padding:8px 0">
                            <span style="font-size:12px;color:#6B7280">List</span>
                            <span class="ct-flex ct-items-center ct-gap-1" style="font-size:13px;color:#7C3AED;font-weight:500">
                                <i class="fa-solid fa-circle" style="font-size:5px"></i>
                                ${CT.Utils.escHtml(CT.Router.currentListData?.title || '')}
                            </span>
                        </div>

                        <!-- Tags -->
                        ${t.tags?.length ? `
                        <div class="ct-flex ct-items-center ct-justify-between" style="padding:8px 0">
                            <span style="font-size:12px;color:#6B7280">Tags</span>
                            <div class="ct-flex ct-flex-wrap ct-gap-1 ct-justify-end">${CT.Utils.tagsPills(t.tags)}</div>
                        </div>` : ''}
                    </div>

                    <div style="height:1px;background:#E5E7EB"></div>

                    <!-- Task ID -->
                    <div class="ct-flex ct-items-center ct-justify-between" style="padding:8px 0">
                        <span style="font-size:12px;color:#6B7280">Task ID</span>
                        <span style="font-size:13px;color:#111827;font-weight:500">#${t.id}</span>
                    </div>
                </div>
            </div>
        </div>`;

        document.getElementById('ct-task-panel-content').innerHTML = html;

        document.getElementById('ct-task-close')?.addEventListener('click', () => this.close());
        document.getElementById('ct-task-edit')?.addEventListener('click', () => CT.Forms.taskForm(this.currentTask));

        document.getElementById('ct-detail-status')?.addEventListener('change', async (e) => {
            await CT.Ajax.post('ct_update_task', { id: t.id, status: e.target.value });
            CT.Router.loadView();
        });

        document.getElementById('ct-detail-priority')?.addEventListener('change', async (e) => {
            await CT.Ajax.post('ct_update_task', { id: t.id, priority: e.target.value });
            CT.Router.loadView();
        });

        CT.Comments.load(t.id);
    },
};
