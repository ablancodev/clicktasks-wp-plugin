/**
 * CT.Utils - Utility functions
 */
window.CT = window.CT || {};

CT.Utils = {
    escHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    },

    escAttr(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    },

    priorityColor(priority) {
        const map = { urgent: '#EF4444', high: '#F97316', normal: '#3B82F6', low: '#9CA3AF' };
        return map[priority] || map.normal;
    },

    priorityLabel(priority) {
        const map = {
            urgent: ctData.i18n.urgent,
            high: ctData.i18n.high,
            normal: ctData.i18n.normal,
            low: ctData.i18n.low,
        };
        return map[priority] || priority;
    },

    priorityBg(priority) {
        const map = { urgent: '#FEE2E2', high: '#FFF7ED', normal: '#EFF6FF', low: '#F3F4F6' };
        return map[priority] || map.normal;
    },

    statusColor(statusName, statuses) {
        const s = (statuses || []).find(st => st.name === statusName);
        return s ? s.color : '#9CA3AF';
    },

    statusBg(color) {
        const map = {
            '#9CA3AF': '#F3F4F6', '#3B82F6': '#EFF6FF',
            '#F59E0B': '#FFFBEB', '#10B981': '#ECFDF5',
        };
        return map[color] || color + '15';
    },

    avatarColors: ['#F97316', '#3B82F6', '#14B8A6', '#8B5CF6', '#EC4899', '#10B981', '#EF4444', '#6366F1'],

    avatarColor(id) {
        return this.avatarColors[id % this.avatarColors.length];
    },

    initials(name) {
        if (!name) return '?';
        const parts = name.trim().split(/\s+/);
        if (parts.length >= 2) return (parts[0][0] + parts[1][0]).toUpperCase();
        return name.substring(0, 2).toUpperCase();
    },

    // Tag colors rotation
    tagColors: [
        { bg: '#FEE2E2', text: '#DC2626' },
        { bg: '#DBEAFE', text: '#2563EB' },
        { bg: '#D1FAE5', text: '#059669' },
        { bg: '#FEF3C7', text: '#D97706' },
        { bg: '#EDE9FE', text: '#7C3AED' },
        { bg: '#FCE7F3', text: '#DB2777' },
    ],

    tagColor(tag) {
        let hash = 0;
        for (let i = 0; i < tag.length; i++) hash = tag.charCodeAt(i) + ((hash << 5) - hash);
        return this.tagColors[Math.abs(hash) % this.tagColors.length];
    },

    formatDate(dateStr) {
        if (!dateStr) return '';
        const d = new Date(dateStr);
        const now = new Date();
        const diff = d - now;
        const days = Math.ceil(diff / (1000 * 60 * 60 * 24));

        const opts = { month: 'short', day: 'numeric' };
        const formatted = d.toLocaleDateString('es-ES', opts);

        if (days < 0) return `<span style="color:#EF4444">${formatted}</span>`;
        if (days <= 2) return `<span style="color:#F59E0B">${formatted}</span>`;
        return formatted;
    },

    avatarStack(users, max = 3) {
        if (!users || !users.length) return '';
        const shown = users.slice(0, max);
        let html = '<div class="ct-flex ct-items-center" style="gap:0;margin-left:0">';
        shown.forEach((u, i) => {
            const ml = i > 0 ? 'margin-left:-4px;' : '';
            const bg = this.avatarColor(u.id);
            html += `<span title="${this.escAttr(u.name)}" class="ct-inline-flex ct-items-center ct-justify-center ct-rounded-full ct-text-white ct-shrink-0" style="width:20px;height:20px;font-size:9px;font-weight:600;background:${bg};border:2px solid white;${ml}">${this.initials(u.name)}</span>`;
        });
        if (users.length > max) {
            html += `<span style="font-size:11px;color:#9CA3AF;margin-left:4px">+${users.length - max}</span>`;
        }
        html += '</div>';
        return html;
    },

    tagsPills(tags) {
        if (!tags || !tags.length) return '';
        return tags.map(t => {
            const c = this.tagColor(t);
            return `<span class="ct-tag-pill" style="background:${c.bg};color:${c.text}">${this.escHtml(t)}</span>`;
        }).join(' ');
    },

    show(el) {
        if (typeof el === 'string') el = document.querySelector(el);
        if (el) { el.style.display = ''; el.classList.remove('ct-hidden'); }
    },

    hide(el) {
        if (typeof el === 'string') el = document.querySelector(el);
        if (el) { el.style.display = 'none'; el.classList.add('ct-hidden'); }
    },

    on(selector, event, handler) {
        document.querySelectorAll(selector).forEach(el => el.addEventListener(event, handler));
    },

    delegate(parent, selector, event, handler) {
        const el = typeof parent === 'string' ? document.querySelector(parent) : parent;
        if (!el) return;
        el.addEventListener(event, e => {
            const target = e.target.closest(selector);
            if (target && el.contains(target)) {
                handler.call(target, e, target);
            }
        });
    },
};
