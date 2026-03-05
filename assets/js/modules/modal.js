/**
 * CT.Modal - Modal manager (light theme)
 */
CT.Modal = {
    el: null,
    body: null,

    init() {
        this.el = document.getElementById('ct-modal');
        this.body = document.getElementById('ct-modal-body');

        this.el.addEventListener('click', e => {
            if (e.target === this.el) this.close();
        });

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && this.el.style.display !== 'none') {
                this.close();
            }
        });
    },

    open(html) {
        this.body.innerHTML = html;
        this.el.style.display = 'flex';
    },

    close() {
        this.el.style.display = 'none';
        this.body.innerHTML = '';
    },

    form({ title, fields, onSubmit, submitLabel, deleteAction }) {
        let html = `
            <div class="ct-p-6">
                <h3 class="ct-text-lg ct-font-bold ct-text-ct-text ct-mb-5">${CT.Utils.escHtml(title)}</h3>
                <form id="ct-modal-form" class="ct-space-y-4">
                    ${fields}
                    <div class="ct-flex ct-justify-between ct-items-center ct-pt-4 ct-border-t ct-border-ct-border">
                        <div>
                            ${deleteAction ? `<button type="button" id="ct-modal-delete" class="ct-text-sm ct-text-red-500 hover:ct-text-red-700 ct-font-medium">${ctData.i18n.delete}</button>` : ''}
                        </div>
                        <div class="ct-flex ct-gap-2">
                            <button type="button" id="ct-modal-cancel" class="ct-px-4 ct-py-2 ct-text-sm ct-text-ct-text-s hover:ct-text-ct-text ct-font-medium ct-transition-colors">${ctData.i18n.cancel}</button>
                            <button type="submit" class="ct-px-4 ct-py-2 ct-bg-ct-primary hover:ct-bg-ct-primary-h ct-text-white ct-rounded-md ct-text-sm ct-font-semibold ct-transition-colors">${submitLabel || ctData.i18n.save}</button>
                        </div>
                    </div>
                </form>
            </div>`;

        this.open(html);

        document.getElementById('ct-modal-cancel').addEventListener('click', () => this.close());

        const form = document.getElementById('ct-modal-form');
        form.addEventListener('submit', async e => {
            e.preventDefault();
            await onSubmit(new FormData(form));
            this.close();
        });

        if (deleteAction) {
            document.getElementById('ct-modal-delete').addEventListener('click', async () => {
                if (confirm(ctData.i18n.confirm_delete)) {
                    await deleteAction();
                    this.close();
                }
            });
        }
    },

    inputField(name, label, value = '', type = 'text', attrs = '') {
        return `
            <div>
                <label class="ct-block ct-text-sm ct-font-medium ct-text-ct-text ct-mb-1.5">${CT.Utils.escHtml(label)}</label>
                <input type="${type}" name="${name}" value="${CT.Utils.escAttr(value)}" ${attrs}
                    class="ct-w-full ct-bg-white ct-text-ct-text ct-border ct-border-ct-border ct-rounded-md ct-px-3 ct-py-2 ct-text-sm">
            </div>`;
    },

    textareaField(name, label, value = '') {
        return `
            <div>
                <label class="ct-block ct-text-sm ct-font-medium ct-text-ct-text ct-mb-1.5">${CT.Utils.escHtml(label)}</label>
                <textarea name="${name}" rows="3"
                    class="ct-w-full ct-bg-white ct-text-ct-text ct-border ct-border-ct-border ct-rounded-md ct-px-3 ct-py-2 ct-text-sm">${CT.Utils.escHtml(value)}</textarea>
            </div>`;
    },

    selectField(name, label, options, selected = '') {
        const opts = options.map(o => {
            const val = typeof o === 'object' ? o.value : o;
            const lbl = typeof o === 'object' ? o.label : o;
            const sel = val === selected ? 'selected' : '';
            return `<option value="${CT.Utils.escAttr(val)}" ${sel}>${CT.Utils.escHtml(lbl)}</option>`;
        }).join('');

        return `
            <div>
                <label class="ct-block ct-text-sm ct-font-medium ct-text-ct-text ct-mb-1.5">${CT.Utils.escHtml(label)}</label>
                <select name="${name}"
                    class="ct-w-full ct-bg-white ct-text-ct-text ct-border ct-border-ct-border ct-rounded-md ct-px-3 ct-py-2 ct-text-sm">${opts}</select>
            </div>`;
    },
};
