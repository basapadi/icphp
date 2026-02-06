import { Extension } from '@tiptap/core'

export const Indent = Extension.create({
    name: 'indent',

    addGlobalAttributes() {
        return [
            {
                types: ['paragraph', 'heading'],
                attributes: {
                    indent: {
                        default: 0,
                        parseHTML: element =>
                            Number(element.style.marginLeft?.replace('px', '')) || 0,
                        renderHTML: attributes => {
                            if (!attributes.indent) return {}
                            return {
                                style: `margin-left: ${attributes.indent}px`,
                            }
                        },
                    },
                },
            },
        ]
    },

    addCommands() {
        return {
            indent:
                () =>
                    ({ editor, state, tr }) => {
                        const { $from } = state.selection
                        const nodePos = $from.before($from.depth)

                        const node = state.doc.nodeAt(nodePos)
                        if (!node) return false

                        const current = node.attrs.indent || 0
                        const next = Math.min(current + 24, 240)

                        tr.setNodeMarkup(nodePos, undefined, {
                            ...node.attrs,
                            indent: next,
                        })

                        editor.view.dispatch(tr)
                        return true
                    },

            outdent:
                () =>
                    ({ editor, state, tr }) => {
                        const { $from } = state.selection
                        const nodePos = $from.before($from.depth)

                        const node = state.doc.nodeAt(nodePos)
                        if (!node) return false

                        const current = node.attrs.indent || 0
                        const next = Math.max(current - 24, 0)

                        tr.setNodeMarkup(nodePos, undefined, {
                            ...node.attrs,
                            indent: next,
                        })

                        editor.view.dispatch(tr)
                        return true
                    },
        }
    },
})
