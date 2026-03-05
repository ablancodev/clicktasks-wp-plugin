/**
 * CT.Comments - Task comments (matching Pencil design)
 */
CT.Comments = {
    async load(taskId) {
        const container = document.getElementById('ct-detail-comments');
        if (!container) return;

        const res = await CT.Ajax.post('ct_get_comments', { task_id: taskId });
        if (!res.ok) return;

        this.renderList(container, res.data, taskId);
    },

    renderList(container, comments, taskId) {
        let html = '';

        if (comments.length) {
            html += '<div style="display:flex;flex-direction:column;gap:16px;margin-bottom:16px">';
            comments.forEach(c => {
                const bg = CT.Utils.avatarColor(c.author.id || 0);
                html += `
                <div class="ct-flex" style="gap:10px">
                    <span class="ct-inline-flex ct-items-center ct-justify-center ct-rounded-full ct-text-white ct-shrink-0" style="width:28px;height:28px;font-size:10px;font-weight:600;background:${bg}">${CT.Utils.initials(c.author.name)}</span>
                    <div class="ct-flex-1 ct-min-w-0">
                        <div class="ct-flex ct-items-center ct-gap-2" style="margin-bottom:4px">
                            <span style="font-size:13px;font-weight:600;color:#111827">${CT.Utils.escHtml(c.author.name)}</span>
                            <span style="font-size:11px;color:#9CA3AF">${this.relativeDate(c.date)}</span>
                        </div>
                        <div style="font-size:13px;color:#374151;line-height:1.5;white-space:pre-wrap">${CT.Utils.escHtml(c.content)}</div>
                    </div>
                </div>`;
            });
            html += '</div>';
        }

        const userBg = CT.Utils.avatarColor(ctData.currentUser.id || 0);
        html += `
        <form id="ct-comment-form" class="ct-flex ct-items-center" style="gap:8px;padding:12px 0 4px 0">
            <span class="ct-inline-flex ct-items-center ct-justify-center ct-rounded-full ct-text-white ct-shrink-0" style="width:28px;height:28px;font-size:10px;font-weight:600;background:${userBg}">${CT.Utils.initials(ctData.currentUser.name)}</span>
            <div class="ct-flex ct-items-center ct-flex-1" style="height:36px;border:1px solid #E5E7EB;border-radius:8px;padding:0 12px">
                <input type="text" id="ct-comment-input" placeholder="Type a comment..." style="border:none;outline:none;flex:1;font-size:13px;color:#111827;background:transparent">
                <button type="submit" style="background:none;border:none;cursor:pointer;color:#9CA3AF;padding:0">
                    <i class="fa-solid fa-paper-plane" style="font-size:13px"></i>
                </button>
            </div>
        </form>`;

        container.innerHTML = html;

        document.getElementById('ct-comment-form')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const input = document.getElementById('ct-comment-input');
            const content = input.value.trim();
            if (!content) return;

            const res = await CT.Ajax.post('ct_create_comment', { task_id: taskId, content });
            if (res.ok) this.renderList(container, res.data, taskId);
        });
    },

    relativeDate(dateStr) {
        const d = new Date(dateStr);
        const now = new Date();
        const diff = Math.floor((now - d) / 1000);

        if (diff < 60) return 'ahora';
        if (diff < 3600) return `hace ${Math.floor(diff / 60)}m`;
        if (diff < 86400) return `hace ${Math.floor(diff / 3600)}h`;
        if (diff < 604800) return `hace ${Math.floor(diff / 86400)}d`;
        return d.toLocaleDateString('es-ES', { month: 'short', day: 'numeric' });
    },
};
