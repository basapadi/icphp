<template>
  <div class="flex flex-col gap-1.5 w-full max-h-[50vh]">
    <Label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
      <span class="text-muted-foreground">{{ label }}</span>
      <span v-if="required" class="text-red-800"> *</span>
    </Label>

    <div class="editor-wrapper" :class="{ fullscreen: isFullscreen }">
      <div v-if="editor" class="flex flex-wrap gap-1 border-b p-2 editor-toolbar">
        <div class="relative">
          <button type="button" class="flex items-center gap-1 h-8 px-2 rounded-md border hover:bg-accent"
            @click="showHeading = !showHeading">
            <HeadingIcon class="w-4 h-4" />
            <span class="text-xs">
              {{ currentHeading }}
            </span>
          </button>

          <div v-if="showHeading" class="absolute z-10 mt-1 w-28 rounded-md border bg-background shadow">
            <button type="button" class="menu-item" @click="setHeading(0)">
              Paragraf
            </button>
            <button type="button" class="menu-item" @click="setHeading(1)">
              Heading 1
            </button>
            <button type="button" class="menu-item" @click="setHeading(2)">
              Heading 2
            </button>
            <button type="button" class="menu-item" @click="setHeading(3)">
              Heading 3
            </button>
          </div>
        </div>
        <select class="h-8 px-2 text-xs border rounded-md" @change="setFont($event.target.value)">
          <option value="Arial">Default</option>
          <option value="Times New Roman">Times New Roman </option>
          <option value="Arial">Arial</option>
        </select>
        <select class="h-8 px-2 text-xs border rounded-md" @change="setFontSize($event.target.value)">
          <option value="">Default</option>
          <option value="10pt">10</option>
          <option value="11pt">11</option>
          <option value="12pt">12</option>
          <option value="14pt">14</option>
          <option value="16pt">16</option>
          <option value="18pt">18</option>
        </select>
        <button type="button" :class="btnClass(editor.isActive('indent'))"
          @click="editor.chain().focus().indent().run()">
          <IndentIncrease class="w-4 h-4" />
        </button>

        <button type="button" :class="btnClass(editor.isActive('outdent'))"
          @click="editor.chain().focus().outdent().run()">
          <IndentDecrease class="w-4 h-4" />
        </button>
        <button type="button" :class="btnClass(editor.isActive('bold'))"
          @click="editor.chain().focus().toggleBold().run()">
          <Bold class="w-4 h-4" />
        </button>
        <button type="button" :class="btnClass(editor.isActive('italic'))"
          @click="editor.chain().focus().toggleItalic().run()">
          <Italic class="w-4 h-4" />
        </button>

        <button type="button" :class="btnClass(editor.isActive('underline'))"
          @click="editor.chain().focus().toggleUnderline().run()">
          <UnderlineIcon class="w-4 h-4" />
        </button>

        <span class="mx-1 border-l"></span>

        <button type="button" :class="btnClass(editor.isActive('bulletList'))"
          @click="editor.chain().focus().toggleBulletList().run()">
          <List class="w-4 h-4" />
        </button>

        <button type="button" :class="btnClass(editor.isActive('orderedList'))"
          @click="editor.chain().focus().toggleOrderedList().run()">
          <ListOrdered class="w-4 h-4" />
        </button>

        <span class="mx-1 border-l"></span>

        <button type="button" :class="btnClass(editor.isActive('textAlign', { align: 'left' }))"
          @click="editor.chain().focus().setTextAlign('left').run()">
          <AlignLeft class="w-4 h-4" />
        </button>

        <button type="button" :class="btnClass(editor.isActive('textAlign', { align: 'center' }))"
          @click="editor.chain().focus().setTextAlign('center').run()">
          <AlignCenter class="w-4 h-4" />
        </button>

        <button type="button" :class="btnClass(editor.isActive('textAlign', { align: 'right' }))"
          @click="editor.chain().focus().setTextAlign('right').run()">
          <AlignRight class="w-4 h-4" />
        </button>

        <button type="button" :class="btnClass(editor.isActive('textAlign', { align: 'justify' }))"
          @click="editor.chain().focus().setTextAlign('justify').run()">
          <AlignJustify class="w-4 h-4" />
        </button>

        <span class="mx-1 border-l"></span>

        <div class="relative" @click="!showTableMenu">
          <button type="button" @click="showTableMenu = !showTableMenu" :class="btnClass(editor.isActive('textAlign', { align: 'table' }))">
            <TableIcon class="w-4 h-4" />
          </button>

          <div v-if="showTableMenu" class="absolute z-10 mt-1 w-32 rounded-md border bg-background shadow">
            <button type="button" class="menu-item flex text-sm" @click="editor.chain().focus().insertTable({ rows:3, cols:3, withHeaderRow:true }).run()">
              <Plus class="w-4 h-4" /> Tabel Baru
            </button>
            <button type="button" class="menu-item flex text-sm" @click="editor.chain().focus().addRowAfter().run()">
              <Plus class="w-4 h-4" /> Row
            </button>
            <button type="button" class="menu-item flex text-sm" @click="editor.chain().focus().addColumnAfter().run()">
              <Plus class="w-4 h-4" /> Column
            </button>
            <button type="button" class="menu-item flex text-sm" @click="editor.chain().focus().deleteRow().run()">
              <Minus class="w-4 h-4" /> Row
            </button>
            <button type="button" class="menu-item flex text-sm" @click="editor.chain().focus().deleteColumn().run()">
              <Minus class="w-4 h-4" /> Column
            </button>
          </div>
        </div>

        <button type="button" :class="btnClass(editor.isActive('pageBreak'))"
          @click="editor.chain().focus().insertContent('<div data-page-break></div>').run()">
          <File class="w-4 h-4" />
        </button>
        <button type="button" :class="btnClass(editor.isActive('print'))" @click="printDoc">
          <Printer class="w-4 h-4" />
        </button>
        <button type="button" :class="btnClass(editor.isActive('fullscreen'))" @click="toggleFullscreen">⛶</button>
      </div>
      <div id="print-area" class="editor-scroll">
        <div class="editor-page editor-zoom">
          <EditorContent :editor="editor" />
        </div>
      </div>
    </div>
  </div>
</template>
<style scoped>
.ProseMirror {
  min-height: 350px;
  padding: 16px;
  font-size: 12px;
  font-family: var(--editor-font, "Times New Roman");
  line-height: 1;
  outline:color(from color srgb r g b);
}

.ProseMirror h1 {
  font-size: 1.5rem;
  font-weight: bold;
}

.ProseMirror h2 {
  font-size: 1.25rem;
  font-weight: bold;
}

.ProseMirror h3 {
  font-size: 1.1rem;
  font-weight: bold;
}

.ProseMirror ul {
  list-style-type: disc;
  padding-left: 2rem;
}

.ProseMirror ol {
  list-style-type: decimal;
  padding-left: 1.25rem;
}

.ProseMirror ol[data-list-style] {
  list-style-type: attr(data-list-style);
}

.menu-item {
  width: 100%;
  text-align: left;
  padding: 6px 8px;
  font-size: 10px;
  cursor: pointer;
}

.menu-item:hover {
  background: hsl(var(--accent));
}

.page-break {
  page-break-after: always;
  height: 0;
  border-top: 1px dashed #ccc;
  margin: 24px 0;
}

.editor-page {
  width: 210mm;
  min-height: 297mm;
  margin: 16px auto;
  padding: 12mm 15mm;
  /* atas-bawah | kiri-kanan */
  background: white;
  box-shadow: 0 0 1px 1px #ddd;
}

.editor-container {
  background: #f3f4f6;
  padding: 24px 0;
}

.editor-wrapper {
  display: flex;
  flex-direction: column;
  height: 100%;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  overflow: auto;
}

.editor-toolbar {
  flex-shrink: 0;
  background: white;
  border-bottom: 1px solid #e5e7eb;
  z-index: 10;
}

.editor-scroll {
  flex: 1;
  overflow-y: auto;
  background: #f3f4f6;
}

.editor-page {
  width: 210mm;
  min-height: 297mm;
  margin: 24px auto;
  padding: 20mm;
  background: white;
  box-shadow: 0 0 0 1px #ddd;
}

.editor-wrapper.fullscreen {
  position: fixed;
  inset: 0;
  z-index: 9999;
  background: #f3f4f6;
  border-radius: 0;
  display: flex;
  flex-direction: column;
}

.editor-wrapper.fullscreen .editor-scroll {
  flex: 1;
  overflow: auto;
}

.editor-wrapper.fullscreen .editor-page {
  margin: 24px auto;
}

:deep(.ProseMirror table) {
  width: 100%;
  /* border-collapse: collapse; */
  table-layout: fixed;
}

:deep(.ProseMirror th),
:deep(.ProseMirror td) {
  border: 1px solid #aaa;
  padding: 2px;
  margin:2px;
  position: relative;
}

:deep(.ProseMirror td[data-bg-color]),
:deep(.ProseMirror th[data-bg-color]) {
  background-color: inherit;
}

:deep(.ProseMirror .column-resize-handle) {
  position: absolute;
  right: -2px;
  top: 0;
  bottom: 0;
  width: 4px;
  pointer-events: none;
}

:deep(.ProseMirror.resize-cursor) {
  cursor: col-resize;
}

.menu-item {
  width: 100%;
  text-align: left;
  padding: 6px 8px;
  font-size: 12px;
  cursor: pointer;
}

.menu-item:hover {
  background: hsl(var(--accent));
}

:deep(.ProseMirror ul) {
  list-style-type: disc;
  padding-left: 2rem;
}

:deep(.ProseMirror ol) {
  list-style-type: decimal;
  padding-left: 2rem;
}

:deep(.ProseMirror li) {
  display: list-item;
}

@media print {
  body {
    margin: 0;
    background: white;
  }

  body * {
    visibility: hidden;
  }

  #print-area,
  #print-area * {
    visibility: visible;
  }

  #print-area {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
  }

  .editor-toolbar,
  .no-print {
    display: none !important;
  }

  .editor-page {
    width: auto;
    min-height: auto;
    margin: 0;
    padding: 0;
    box-shadow: none;
  }

  .ProseMirror {
    padding: 0;
  }

  .page-break {
    break-after: page;
  }

  @page {
    size: A4;
    margin: 12mm;
  }
}
</style>
<script>
import Label from "@/components/ui/Label.vue";
import { cn } from "@/lib/utils";
import { Editor, EditorContent } from '@tiptap/vue-3'
import { StarterKit } from '@tiptap/starter-kit'
import { Underline } from '@tiptap/extension-underline'
import { Table } from '@tiptap/extension-table'
import { TableRow } from '@tiptap/extension-table-row'
import { TableCell } from '@tiptap/extension-table-cell'
import { TableHeader } from '@tiptap/extension-table-header'
import { TextAlign } from '@tiptap/extension-text-align'
import { Heading } from '@tiptap/extension-heading'
import { TextStyle } from '@tiptap/extension-text-style'
import { FontFamily } from '@tiptap/extension-font-family'

import { Indent } from '@/helpers/indent'
import { PageBreak } from '@/helpers/pagebreak'
import { FontSize } from '@/helpers/fontsize'
import { FirstLineIndent } from '@/helpers/firstlineindent'

import {
  Bold,
  Italic,
  Underline as UnderlineIcon,
  List,
  ListOrdered,
  AlignLeft,
  AlignCenter,
  AlignRight,
  AlignJustify,
  Table as TableIcon,
  Heading as HeadingIcon,
  IndentIncrease,
  IndentDecrease,
  File,
  Printer,
  Plus,
  Minus,
  Square,
} from 'lucide-vue-next'

export default {
  name: "Editor",
  components: {
    Label,
    EditorContent,
    Bold,
    Italic,
    UnderlineIcon,
    List,
    ListOrdered,
    AlignLeft,
    AlignCenter,
    AlignRight,
    AlignJustify,
    TableIcon,
    HeadingIcon,
    IndentIncrease,
    File,
    IndentDecrease,
    Printer,
    Plus,
    Minus,
    Square,
  },
  props: {
    modelValue: { type: String, default: "" },
    label: { type: String, default: "" },
    required: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    readonly: { type: Boolean, default: false },
    class: { type: String, default: "" },
  },
  emits: ["update:modelValue"],
  data() {
    return {
      editor: null,
      showHeading: false,
      isFullscreen: false,
      showTableMenu: false
    };
  },
  watch: {
    modelValue(value) {
      if (!this.editor) return
      if (value === this.editor.getHTML()) return

      this.editor.commands.setContent(value, false)
    }
  },
  computed: {
    wrapperClass() {
      return cn("rounded-md border border-input editor-container", this.class);
    },
    currentHeading() {
      if (!this.editor) return 'Paragraf'
      if (this.editor.isActive('heading', { level: 1 })) return 'H1'
      if (this.editor.isActive('heading', { level: 2 })) return 'H2'
      if (this.editor.isActive('heading', { level: 3 })) return 'H3'
      return 'Paragraf'
    }
  },
  methods: {
    onInput(value) {
      this.$emit("update:modelValue", value);
    },
    cmd(action) {
      this.editor.chain().focus()[action]().run()
    },

    align(type) {
      this.editor.chain().focus().setTextAlign(type).run()
    },

    setHeading(level) {
      this.editor.chain().focus().toggleHeading({ level }).run()
    },
    insertTable() {
      this.editor.chain().focus().insertTable({
        rows: 3,
        cols: 3,
        withHeaderRow: true,
      }).run()
    },
   toggleTableBorder() {
      const { state, view } = this.editor
      const { selection } = state

      if (!selection) return

      const cells = []

      state.doc.nodesBetween(selection.from, selection.to, (node, pos) => {
        if (node.type.name === 'tableCell' || node.type.name === 'tableHeader') {
          cells.push({ node, pos })
        }
      })

      if (!cells.length) return

      const current = cells[0].node.attrs['data-border'] || 'solid'
      const next = current === 'none' ? 'solid' : 'none'

      let tr = state.tr

      cells.forEach(({ node, pos }) => {
        tr = tr.setNodeMarkup(pos, undefined, {
          ...node.attrs,
          'data-border': next,
        })
      })

      view.dispatch(tr)
    },
    active(type) {
      return this.editor?.isActive(type)
        ? 'bg-primary text-primary-foreground'
        : 'border'
    },
    btnClass(active = false) {
      return cn(
        "h-8 w-8 inline-flex items-center justify-center rounded-md text-sm",
        "hover:bg-accent hover:text-accent-foreground",
        active && "bg-primary text-primary-foreground"
      )
    },
    setHeading(level) {
      this.showHeading = false
      const chain = this.editor.chain().focus()
      level === 0
        ? chain.setParagraph().run()
        : chain.toggleHeading({ level }).run()
    },
    setFont(font) {
      const chain = this.editor.chain().focus()
      font
        ? chain.setFontFamily(font).run()
        : chain.unsetFontFamily().run()
    },
    setFontSize(size) {
      const chain = this.editor.chain().focus()
      size
        ? chain.setFontSize(size).run()
        : chain.unsetFontSize().run()
    },
    async printDoc() {
      // window.print()
      this.$emit("print");
    },
    toggleFullscreen() {
      this.isFullscreen = !this.isFullscreen

      if (this.isFullscreen) {
        document.body.style.overflow = 'hidden'
      } else {
        document.body.style.overflow = ''
      }
    },
    onEscapeKeydown(e) {
      if (e.key === 'Escape' && this.isFullscreen) {
        this.toggleFullscreen()
      }
    },
  },
  mounted() {
    this.editor = new Editor({
      content: this.modelValue,
      extensions: [
        StarterKit.configure({
          heading: true, // kita kontrol sendiri
        }),
        Heading.configure({
          levels: [1, 2, 3],
        }),
        TextStyle,
        FontFamily,
        FontSize,
        Underline,
        TextAlign.configure({
          types: ['heading', 'paragraph'],
        }),
        Table.configure({ resizable: true }),
        TableRow,
        TableCell,
        TableHeader,
        Indent,
        // FirstLineIndent,
        PageBreak,
      ],
      onCreate: ({ editor }) => {
        editor
          .chain()
          .focus()
          .setFontSize('11pt')
          .setFontFamily('Times New Roman')
          .run()
        //editor.commands.enableFirstLineIndent()
      },
      onUpdate: ({ editor }) => {
        const html = editor.getHTML();
        const safeHtml = html.replace(/  /g, '&nbsp;&nbsp;')
        this.$emit('update:modelValue', safeHtml)
      },
    }),
      document.addEventListener('keydown', this.onEscapeKeydown)
  },
  beforeUnmount() {
    this.editor?.destroy()
    document.removeEventListener('keydown', this.onEscapeKeydown)
  },
};
</script>
