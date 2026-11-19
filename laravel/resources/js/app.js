import Alpine from 'alpinejs';
import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Placeholder from '@tiptap/extension-placeholder';

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

/*
 * Rich text editor (components/admin/rich-editor). Only the formatting the server's
 * sanitizer allows (App\Support\RichText): paragraphs, h2/h3, bold, italic, underline,
 * strike, lists, quotes and http(s)/mailto/relative links.
 */
const SAFE_LINK = /^(https?:\/\/|mailto:|\/|#)/i;

Alpine.data('richEditor', (content, placeholder, label) => {
    let editor = null; // kept outside Alpine's reactive proxy, which ProseMirror doesn't tolerate

    return {
        content,
        tick: 0, // bumped on every editor change so toolbar states re-render
        linkOpen: false,
        linkUrl: '',

        init() {
            editor = new Editor({
                element: this.$refs.editor,
                content: this.content,
                extensions: [
                    StarterKit.configure({
                        heading: { levels: [2, 3] },
                        code: false,
                        codeBlock: false,
                        horizontalRule: false,
                        link: {
                            openOnClick: false,
                            autolink: true,
                            defaultProtocol: 'https',
                            HTMLAttributes: { target: null, rel: 'noopener noreferrer' },
                            isAllowedUri: (url) => SAFE_LINK.test(url),
                        },
                    }),
                    Placeholder.configure({ placeholder }),
                ],
                editorProps: {
                    attributes: {
                        class: 'rich-text min-h-64 px-4 py-3 text-sm focus:outline-none',
                        'aria-label': label,
                        'aria-multiline': 'true',
                    },
                },
                onUpdate: ({ editor }) => { this.content = editor.isEmpty ? '' : editor.getHTML(); },
                onTransaction: () => { this.tick++; },
            });
        },

        destroy() {
            editor?.destroy();
        },

        isActive(name, attributes = {}) {
            this.tick;
            return editor ? editor.isActive(name, attributes) : false;
        },

        can(command) {
            this.tick;
            return editor ? editor.can()[command]() : false;
        },

        run(command, ...args) {
            editor.chain().focus()[command](...args).run();
        },

        openLink() {
            this.linkUrl = editor.getAttributes('link').href || '';
            this.linkOpen = true;
            // After Alpine shows the row and the click has finished moving focus.
            this.$nextTick(() => requestAnimationFrame(() => this.$refs.linkInput.focus()));
        },

        saveLink() {
            let url = this.linkUrl.trim();
            const chain = editor.chain().focus().extendMarkRange('link');
            if (!url) {
                chain.unsetLink().run();
            } else {
                if (!SAFE_LINK.test(url)) url = `https://${url.replace(/^[a-z]+:\/*/i, '')}`;
                chain.setLink({ href: url }).run();
            }
            this.closeLink();
        },

        removeLink() {
            editor.chain().focus().extendMarkRange('link').unsetLink().run();
            this.closeLink();
        },

        closeLink() {
            this.linkOpen = false;
            this.linkUrl = '';
        },
    };
});

Alpine.start();
