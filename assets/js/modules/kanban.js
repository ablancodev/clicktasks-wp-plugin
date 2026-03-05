/**
 * CT.Kanban - Kanban board view (matching Pencil design)
 */
CT.Kanban = {
    sortables: [],

    render(tasks, statuses) {
        const container = document.getElementById('ct-kanban-view');

        if (!tasks.length && !statuses.length) {
            container.innerHTML = `<p class="ct-text-ct-muted ct-text-center ct-py-12 ct-text-sm">${ctData.i18n.no_tasks}</p>`;
            return;
        }

        const grouped = {};
        statuses.forEach(s => { grouped[s.name] = []; });
        tasks.forEach(t => {
            if (!grouped[t.status]) grouped[t.status] = [];
            grouped[t.status].push(t);
        });

        let html = '<div class="ct-flex ct-gap-4 ct-h-full ct-overflow-x-auto ct-pb-4" style="padding:24px">';

        statuses.forEach(s => {
            const columnTasks = grouped[s.name] || [];
            html += `
            <div class="ct-kanban-column ct-flex ct-flex-col">
                <div class="ct-flex ct-items-center ct-gap-2 ct-px-1 ct-mb-3" style="height:36px">
                    <span class="ct-shrink-0 ct-rounded-full" style="background:${s.color};width:10px;height:10px"></span>
                    <span class="ct-text-[11px] ct-font-bold ct-uppercase ct-tracking-wide" style="color:${s.color}">${CT.Utils.escHtml(s.name)}</span>
                    <span class="ct-text-[11px] ct-font-semibold ct-text-ct-text-s ct-rounded-full ct-flex ct-items-center ct-justify-center" style="background:#F3F4F6;min-width:20px;height:20px;padding:0 8px">${columnTasks.length}</span>
                    <div class="ct-flex-1"></div>
                    <i class="fa-solid fa-plus ct-text-ct-muted ct-cursor-pointer hover:ct-text-ct-text-s" style="font-size:14px"></i>
                </div>
                <div class="ct-kanban-cards ct-flex-1 ct-overflow-y-auto ct-min-h-[60px]" style="display:flex;flex-direction:column;gap:8px" data-status="${CT.Utils.escAttr(s.name)}">
                    ${columnTasks.map(t => this.cardHtml(t, statuses)).join('')}
                </div>
                <button class="ct-flex ct-items-center ct-gap-1.5 ct-mt-2 ct-text-[13px] ct-text-ct-muted hover:ct-text-ct-text-s ct-w-full ct-transition-colors" style="height:36px;padding:0 8px;border:1px solid #E5E7EB;border-radius:6px;justify-content:center" data-add-status="${CT.Utils.escAttr(s.name)}">
                    <i class="fa-solid fa-plus" style="font-size:12px"></i>
                    Add card
                </button>
            </div>`;
        });

        html += '</div>';
        container.innerHTML = html;

        this.initSortable();
    },

    cardHtml(task, statuses) {
        const pColor = CT.Utils.priorityColor(task.priority);
        const pLabel = CT.Utils.priorityLabel(task.priority);
        const pBg = CT.Utils.priorityBg(task.priority);
        const tags = task.tags?.length ? task.tags.map(t => {
            const c = CT.Utils.tagColor(t);
            return `<span class="ct-inline-flex ct-items-center ct-gap-1 ct-rounded-full" style="background:${c.bg};padding:0 8px;height:18px"><span class="ct-rounded-full" style="background:${c.text};width:6px;height:6px"></span><span style="color:${c.text};font-size:11px;font-weight:600">${CT.Utils.escHtml(t)}</span></span>`;
        }).join('') : '';

        const avatars = CT.Utils.avatarStack(task.assigned);
        const dueHtml = CT.Utils.formatDate(task.due_date);

        return `
        <div class="ct-task-card ct-cursor-pointer ct-transition-colors" style="background:#FFFFFF;border-radius:8px;padding:12px;box-shadow:0 2px 8px rgba(0,0,0,0.07);display:flex;flex-direction:column;gap:8px" data-task-id="${task.id}">
            ${tags ? `<div class="ct-flex ct-flex-wrap ct-gap-1.5">${tags}</div>` : ''}
            <span class="ct-inline-flex ct-items-center ct-gap-1 ct-rounded-full" style="background:${pBg};padding:0 8px;height:20px;align-self:flex-start"><span class="ct-rounded-full" style="background:${pColor};width:6px;height:6px"></span><span style="color:${pColor};font-size:11px;font-weight:600">${CT.Utils.escHtml(pLabel)}</span></span>
            <p style="font-size:13px;font-weight:600;color:#111827;line-height:1.4">${CT.Utils.escHtml(task.title)}</p>
            <div class="ct-flex ct-items-center ct-gap-2" style="height:24px">
                ${avatars}
                <div class="ct-flex-1"></div>
                ${dueHtml ? `<span style="font-size:11px;color:#9CA3AF">${dueHtml}</span>` : ''}
                ${task.comment_count ? `<span class="ct-flex ct-items-center ct-gap-1" style="font-size:11px;color:#9CA3AF"><i class="fa-regular fa-comment" style="font-size:10px"></i>${task.comment_count}</span>` : ''}
            </div>
        </div>`;
    },

    initSortable() {
        this.sortables.forEach(s => s.destroy());
        this.sortables = [];

        document.querySelectorAll('.ct-kanban-cards').forEach(el => {
            const sortable = new Sortable(el, {
                group: 'kanban',
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                onEnd: (evt) => this.onDragEnd(evt),
            });
            this.sortables.push(sortable);
        });

        CT.Utils.delegate('#ct-kanban-view', '.ct-task-card', 'click', (e, card) => {
            CT.TaskDetail.open(parseInt(card.dataset.taskId));
        });

        CT.Utils.delegate('#ct-kanban-view', '[data-add-status]', 'click', (e, btn) => {
            CT.Forms.taskForm(null, btn.dataset.addStatus);
        });
    },

    async onDragEnd(evt) {
        const items = [];
        document.querySelectorAll('.ct-kanban-cards').forEach(col => {
            const status = col.dataset.status;
            col.querySelectorAll('.ct-task-card').forEach((card, idx) => {
                items.push({ id: parseInt(card.dataset.taskId), position: idx, status });
            });
        });
        await CT.Ajax.post('ct_reorder_tasks', { items });
    },
};
