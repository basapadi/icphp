import { TableCell } from '@tiptap/extension-table-cell'

export const CustomTableCell = TableCell.extend({
    addAttributes() {
        return {
            ...this.parent?.(),
            'data-border': {
                default: 'solid',
                parseHTML: el => el.getAttribute('data-border') || 'solid',
                renderHTML: attrs => ({
                    'data-border': attrs['data-border'],
                }),
            },
        }
    },
})
