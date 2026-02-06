import { Extension } from '@tiptap/core'

export const FirstLineIndent = Extension.create({
    name: 'FirstLineIndent',

    addGlobalAttributes() {
        return [
            {
                types: ['paragraph'],
                attributes: {
                    firstLineIndent: {
                        default: false,
                        parseHTML: element =>
                            element.style.textIndent && element.style.textIndent !== '0px',
                        renderHTML: attributes => {
                            if (!attributes.firstLineIndent) return {}

                            return {
                                style: 'text-indent: 1.27cm;',
                            }
                        },
                    },
                },
            },
        ]
    },

    addCommands() {
        return {
            enableFirstLineIndent:
                () =>
                    ({ state, tr }) => {
                        const { $from } = state.selection
                        const pos = $from.before($from.depth)

                        const node = state.doc.nodeAt(pos)
                        if (!node || node.type.name !== 'paragraph') return false

                        tr.setNodeMarkup(pos, undefined, {
                            ...node.attrs,
                            firstLineIndent: true,
                        })

                        return true
                    },
        }
    },
})
