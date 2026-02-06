import { Table } from '@tiptap/extension-table'

export const TableCustom = Table.extend({
    addAttributes() {
        return {
            'data-border': {
                default: 'solid',
                parseHTML: element => element.getAttribute('data-border') || 'solid',
                renderHTML: attrs => ({
                    'data-border': attrs['data-border'],
                }),
            },
        }
    },
})