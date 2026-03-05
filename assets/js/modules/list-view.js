/**
 * CT.ListView - Table list view (matching Pencil design)
 */
CT.ListView = {
    tasks: [],
    statuses: [],
    sortKey: 'position',
    sortDir: 'asc',

    render(tasks, statuses) {
        this.tasks = tasks;
        this.statuses = statuses;
        const container = document.getElementById('ct-list-view');

        if (!tasks.length) {
            container.innerHTML = `<p class="ct-text-ct-muted ct-text-center ct-py-12 ct-text-sm">${ctData.i18n.no_tasks}</p>`;
            return;
        }

        const grouped = {};
        statuses.forEach(s => { grouped[s.name] = []; });
        tasks.forEach(t => {
            if (!grouped[t.status]) grouped[t.status] = [];
            grouped[t.status].push(t);
        });

        let html = `
        <div style="padding:16px">
        <div style="background:#FFFFFF;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.07);overflow:hidden">
            <!-- Table Header -->
            <div class="ct-flex ct-items-center" style="background:#FAFAFA;border-bottom:1px solid #E5E7EB;height:40px;padding:0 16px;font-size:11px;font-weight:600;color:#6B7280;text-transform:uppercase;letter-spacing:0.05em">
                <div style="width:36px" class="ct-shrink-0"></div>
                <div class="ct-sort-header ct-flex-1 ct-min-w-0" data-sort="title">Task Name ${this.sortIcon('title')}</div>
                <div class="ct-sort-header" style="width:100px" data-sort="assigned">Assignee</div>
                <div class="ct-sort-header" style="width:100px" data-sort="due_date">Due Date ${this.sortIcon('due_date')}</div>
                <div class="ct-sort-header" style="width:100px" data-sort="priority">Priority ${this.sortIcon('priority')}</div>
                <div class="ct-sort-header" style="width:110px" data-sort="status">Status ${this.sortIcon('status')}</div>
            </div>`;

        statuses.forEach(s => {
            const rows = grouped[s.name] || [];
            if (!rows.length) return;

            html += `
            <div class="ct-flex ct-items-center ct-gap-2" style="background:#FAFAFA;border-bottom:1px solid #E5E7EB;height:36px;padding:0 16px">
                <span class="ct-rounded-full ct-shrink-0" style="background:${s.color};width:8px;height:8px"></span>
                <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:${s.color}">${CT.Utils.escHtml(s.name)}</span>
                <span style="font-size:11px;color:#9CA3AF">${rows.length}</span>
                <i class="fa-solid fa-chevron-down" style="font-size:9px;color:#9CA3AF"></i>
            </div>`;

            const sorted = this.sort(rows);
            sorted.forEach((t, i) => {
                html += this.rowHtml(t, i);
            });

            html += `
            <div class="ct-flex ct-items-center ct-gap-1.5 ct-cursor-pointer ct-transition-colors" style="background:#FAFAFA;border-bottom:1px solid #F4F4F5;height:36px;padding:0 16px;color:#9CA3AF" onmouseover="this.style.color='#6B7280'" onmouseout="this.style.color='#9CA3AF'">
                <i class="fa-solid fa-plus" style="font-size:11px"></i>
                <span style="font-size:12px">Add Task...</span>
            </div>`;
        });

        html += '</div></div>';
        container.innerHTML = html;
        this.bindEvents();
    },

    rowHtml(task, idx) {
        const pColor = CT.Utils.priorityColor(task.priority);
        const pBg = CT.Utils.priorityBg(task.priority);
        const sColor = CT.Utils.statusColor(task.status, this.statuses);
        const sBg = CT.Utils.statusBg(sColor);

        const avatar = task.assigned?.length ?
            `<span class="ct-inline-flex ct-items-center ct-justify-center ct-rounded-full ct-text-white" style="width:24px;height:24px;font-size:9px;font-weight:600;background:${CT.Utils.avatarColor(task.assigned[0].id)}">${CT.Utils.initials(task.assigned[0].name)}</span>` : '';

        const bg = idx % 2 === 0 ? '#FFFFFF' : '#FAFAFA';

        return `
        <div class="ct-flex ct-items-center ct-cursor-pointer ct-transition-colors" style="background:${bg};border-bottom:1px solid #F4F4F5;height:44px;padding:0 16px" data-task-id="${task.id}" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='${bg}'">
            <div style="width:36px" class="ct-shrink-0 ct-flex ct-items-center ct-justify-center">
                <div style="width:16px;height:16px;border-radius:4px;border:1.5px solid #D1D5DB"></div>
            </div>
            <div class="ct-flex-1 ct-min-w-0 ct-flex ct-items-center ct-gap-2">
                <span style="font-size:13px;color:#111827;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${CT.Utils.escHtml(task.title)}</span>
                ${task.tags?.length ? task.tags.slice(0, 2).map(t => {
                    const c = CT.Utils.tagColor(t);
                    return `<span class="ct-tag-pill" style="background:${c.bg};color:${c.text}">${CT.Utils.escHtml(t)}</span>`;
                }).join('') : ''}
            </div>
            <div style="width:100px">${avatar}</div>
            <div style="width:100px;font-size:12px;color:#6B7280">${CT.Utils.formatDate(task.due_date) || '--'}</div>
            <div style="width:100px">
                <span class="ct-inline-flex ct-items-center ct-gap-1 ct-rounded-full" style="font-size:11px;font-weight:600;color:${pColor};background:${pBg};padding:2px 8px">
                    <span class="ct-rounded-full" style="background:${pColor};width:5px;height:5px"></span>
                    ${CT.Utils.priorityLabel(task.priority)}
                </span>
            </div>
            <div style="width:110px">
                <span class="ct-inline-flex ct-items-center ct-gap-1.5 ct-rounded-full" style="font-size:11px;font-weight:500;color:${sColor};background:${sBg};padding:2px 8px">
                    <span class="ct-rounded-full" style="background:${sColor};width:5px;height:5px"></span>
                    ${CT.Utils.escHtml(task.status)}
                </span>
            </div>
        </div>`;
    },

    sortIcon(key) {
        if (this.sortKey !== key) return '';
        return this.sortDir === 'asc' ? ' <i class="fa-solid fa-arrow-up" style="font-size:9px"></i>' : ' <i class="fa-solid fa-arrow-down" style="font-size:9px"></i>';
    },

    sort(tasks) {
        return [...tasks].sort((a, b) => {
            let va = a[this.sortKey] ?? '';
            let vb = b[this.sortKey] ?? '';
            if (typeof va === 'string') va = va.toLowerCase();
            if (typeof vb === 'string') vb = vb.toLowerCase();
            if (va < vb) return this.sortDir === 'asc' ? -1 : 1;
            if (va > vb) return this.sortDir === 'asc' ? 1 : -1;
            return 0;
        });
    },

    bindEvents() {
        CT.Utils.delegate('#ct-list-view', '.ct-sort-header', 'click', (e, th) => {
            const key = th.dataset.sort;
            if (this.sortKey === key) {
                this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortKey = key;
                this.sortDir = 'asc';
            }
            this.render(this.tasks, this.statuses);
        });

        CT.Utils.delegate('#ct-list-view', '[data-task-id]', 'click', (e, row) => {
            CT.TaskDetail.open(parseInt(row.dataset.taskId));
        });
    },
};
