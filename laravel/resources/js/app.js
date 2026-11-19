import Alpine from 'alpinejs';

window.Alpine = Alpine;

/*
 * Styled confirmation instead of the browser's confirm(). Any form with data-confirm
 * opens the dialog first; the texts come from the form:
 *   data-confirm="Explanation"  data-confirm-title="Zmazať článok?"
 *   data-confirm-item="Item name"  data-confirm-action="Zmazať"
 */
Alpine.data('confirmDialog', () => ({
    form: null,
    title: '',
    item: '',
    text: '',
    action: 'Zmazať',

    intercept(event) {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || !('confirm' in form.dataset)) return;

        event.preventDefault();
        this.form = form;
        this.title = form.dataset.confirmTitle || 'Naozaj pokračovať?';
        this.item = form.dataset.confirmItem || '';
        this.text = form.dataset.confirm || '';
        this.action = form.dataset.confirmAction || 'Zmazať';
        this.$refs.dialog.showModal();
        this.$nextTick(() => this.$refs.cancel.focus());
    },

    confirm() {
        const form = this.form;
        this.$refs.dialog.close();
        form?.submit(); // native submit: no second submit event, so no second dialog
    },
}));

Alpine.start();
