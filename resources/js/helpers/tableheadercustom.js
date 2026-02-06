import { TableHeader } from '@tiptap/extension-table-header'

export const CustomTableHeader = TableHeader.extend({
    addAttributes() {
        return {
            ...this.parent?.(),
            'data-border': {
                default: null,
                parseHTML: element => element.closest('table')?.getAttribute('data-border'),
                renderHTML: () => ({}),
            },
        }
    },
})
