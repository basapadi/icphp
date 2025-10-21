<script setup>
import { ref, onMounted, watch, onBeforeUnmount } from "vue";
import { EditorState, Compartment } from "@codemirror/state";
import { EditorView, keymap, lineNumbers, highlightSpecialChars } from "@codemirror/view";
import { defaultKeymap, history, historyKeymap, indentWithTab } from "@codemirror/commands";
import { sql } from "@codemirror/lang-sql";
import { oneDark } from "@codemirror/theme-one-dark";
import { autocompletion } from "@codemirror/autocomplete";
import { HighlightStyle, syntaxHighlighting } from "@codemirror/language";
import { tags, Tag } from "@lezer/highlight";

const props = defineProps({
  modelValue: { type: String, default: "" },
  schemas: { type: Object, default: () => ({}) },
});
const emit = defineEmits(["update:modelValue"]);

const editorContainer = ref(null);
let view = null;
const completionCompartment = new Compartment();

const sqlHighlighting = HighlightStyle.define([
  { tag: tags.keyword, color: "#ff7b72", fontWeight: "bold" },
  { tag: tags.string, color: "#a5d6ff" },
  { tag: tags.number, color: "#f78c6c" },
  { tag: tags.bool, color: "#f78c6c", fontStyle: "italic" },
  { tag: tags.variableName, color: "#d2a8ff" },
  { tag: tags.typeName, color: "#79c0ff" },
  { tag: tags.operator, color: "#ffab70" },
  { tag: tags.comment, color: "#8b949e", fontStyle: "italic" }
]);

/**
 * Build schema map
 */
const buildSchemaMap = (schemas) => {
  const map = {};
  for (const [table, columns] of Object.entries(schemas)) {
    map[table.toLowerCase()] = columns.map((c) => ({
      label: c.name,
      type: "column",
      info: `${c.type}${c.nullable ? " (nullable)" : ""}`,
    }));
  }
  return map;
};

/**
 * Completion source
 */
const schemaCompletionSource = (schemas) => {
  const schemaMap = buildSchemaMap(schemas);
  const allTables = Object.keys(schemaMap).map((t) => ({
    label: t,
    type: "table",
    info: `Tabel: ${t}`,
  }));
  const allColumns = Object.entries(schemaMap).flatMap(([t, cols]) =>
    cols.map((c) => ({ ...c, info: `Kolom di ${t} - ${c.info}` }))
  );

  return (context) => {
    const word = context.matchBefore(/[\w.]+/);
    if (!word || (word.from === word.to && !context.explicit)) return null;

    const before = context.state.sliceDoc(0, word.from).toLowerCase();

    // setelah FROM / JOIN → suggest table
    if (/\b(from|join)\s+$/i.test(before)) {
      return { from: word.from, options: allTables };
    }

    // case "table." → suggest kolom
    const matchTable = word.text.split(".");
    if (matchTable.length === 2) {
      const [tableName] = matchTable;
      if (schemaMap[tableName.toLowerCase()]) {
        return { from: word.from, options: schemaMap[tableName.toLowerCase()] };
      }
    }

    // default → semua
    return { from: word.from, options: [...allTables, ...allColumns] };
  };
};

onMounted(() => {
  view = new EditorView({
    state: EditorState.create({
      doc: props.modelValue,
      extensions: [
        keymap.of(defaultKeymap),
        sql(),
        lineNumbers(),
        highlightSpecialChars(),
        oneDark,
        history(),
        keymap.of(historyKeymap),
        keymap.of([...defaultKeymap, indentWithTab]),
        syntaxHighlighting(sqlHighlighting),
        completionCompartment.of(
          autocompletion({ override: [schemaCompletionSource(props.schemas)] })
        ),
        EditorView.updateListener.of((update) => {
          if (update.docChanged) {
            emit("update:modelValue", update.state.doc.toString());
          }
        }),
      ],
    }),
    parent: editorContainer.value,
  });
});

watch(
  () => props.modelValue,
  (newVal) => {
    if (view && newVal !== view.state.doc.toString()) {
      view.dispatch({
        changes: { from: 0, to: view.state.doc.length, insert: newVal },
      });
    }
  }
);

watch(
  () => props.schemas,
  (newSchemas) => {
    if (view) {
      view.dispatch({
        effects: completionCompartment.reconfigure(
          autocompletion({ override: [schemaCompletionSource(newSchemas)] })
        ),
      });
    }
  },
  { deep: true }
);

onBeforeUnmount(() => {
  if (view) view.destroy();
});
</script>

<template>
  <div ref="editorContainer" style="height: 100%; width: 100%"></div>
</template>
