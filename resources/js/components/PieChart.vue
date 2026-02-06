<template>
    <div class="flex flex-col gap-4">
        <!-- Title -->
        <div v-if="title" class="text-lg font-semibold text-gray-800">
            {{ title }}
        </div>
        <div class="flex items-start gap-6 z-2">
            <!-- Chart -->
            <div
                :style="{ width: size + 'px', height: size + 'px' }"
                class="relative"
            >
                <svg
                    :width="size"
                    :height="size"
                    :viewBox="`-${padding} -${padding} ${size + padding * 2} ${size + padding * 2}`"
                    class="block overflow-visible"
                >
                    <g :transform="`translate(${half}, ${half})`">
                        <template v-for="(slice, i) in slices" :key="i">
                            <path
                                :d="slice.path"
                                :fill="slice.color"
                                class="transition-transform duration-200 origin-center"
                                :class="{ 'scale-[1.04]': hoverIndex === i }"
                                @mouseenter="hoverIndex = i"
                                @mouseleave="hoverIndex = null"
                            />
                        </template>
                        <!-- optional center hole: uncomment if you want donut -->
                        <!-- <circle :r="size * 0.18" fill="white"/> -->
                    </g>
                </svg>

                <!-- Tooltip (simple) -->
                <div
                    v-if="hoverIndex !== null"
                    class="absolute -translate-x-1/2 bg-white text-xs rounded shadow px-2 py-1 border pointer-events-none"
                    :style="tooltipStyle"
                >
                    <div class="font-medium text-gray-800">
                        {{ data[hoverIndex].label }}
                    </div>
                    <div class="text-gray-600">
                        {{ formatValue(data[hoverIndex].value) }} ({{
                            percent(hoverIndex)
                        }})
                    </div>
                </div>
            </div>

            <!-- Legend -->
            <div class="flex flex-col gap-2">
                <div
                    v-for="(d, i) in data"
                    :key="d.label"
                    class="flex items-center gap-2 cursor-default select-none"
                >
                    <div
                        class="w-4 h-4 rounded-sm"
                        :style="{ backgroundColor: d.color || defaultColor(i) }"
                    ></div>
                    <div class="text-sm text-gray-700">{{ d.label }}</div>
                    <div class="ml-2 text-sm text-gray-500">
                        {{ formatValue(d.value) }} — {{ percent(i) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from "vue";

/**
 * Props:
 * - data: [{ label: string, value: number, color?: string }]
 * - size: number (px)
 */
const props = defineProps({
    data: {
        type: Array,
        required: true,
        validator: (arr) =>
            arr.every((x) => typeof x.value === "number" && "label" in x),
    },
    size: {
        type: Number,
        default: 220,
    },
    title: { type: String, default: "" },
});

const hoverIndex = ref(null);
const half = computed(() => props.size / 2);

// helper: polar to Cartesian
function polarToCartesian(cx, cy, radius, angleInDegrees) {
    const angleInRadians = ((angleInDegrees - 90) * Math.PI) / 180.0;
    return {
        x: cx + radius * Math.cos(angleInRadians),
        y: cy + radius * Math.sin(angleInRadians),
    };
}

// describe wedge path from startAngle to endAngle (degrees)
function describeArc(cx, cy, radius, startAngle, endAngle) {
    const start = polarToCartesian(cx, cy, radius, endAngle);
    const end = polarToCartesian(cx, cy, radius, startAngle);
    const largeArcFlag = endAngle - startAngle <= 180 ? "0" : "1";
    // move to center, line to start, arc to end, close
    return [
        `M ${cx} ${cy}`,
        `L ${start.x} ${start.y}`,
        `A ${radius} ${radius} 0 ${largeArcFlag} 0 ${end.x} ${end.y}`,
        "Z",
    ].join(" ");
}

function describeFullCircle(radius) {
    return `
    M 0 ${-radius}
    A ${radius} ${radius} 0 1 1 0 ${radius}
    A ${radius} ${radius} 0 1 1 0 ${-radius}
    Z
  `;
}

// compute slices
const total = computed(() =>
    props.data.reduce((s, it) => s + Math.max(0, Number(it.value) || 0), 0),
);

const radius = computed(() => Math.min(props.size, props.size) / 2 - 2); // small padding

const slices = computed(() => {
    let acc = 0;

    // 🔥 FULL CIRCLE CASE
    if ((props.data ?? []).length === 1 && total.value > 0) {
        const d = props.data[0];
        return [
            {
                label: d.label,
                value: d.value,
                color: d.color || defaultColor(0),
                startAngle: 0,
                endAngle: 360,
                path: describeFullCircle(radius.value),
            },
        ];
    }

    // normal multi-slice
    return (props.data ?? []).map((d, i) => {
        const value = Math.max(0, Number(d.value) || 0);
        const startAngle = (acc / total.value) * 360;
        acc += value;
        const endAngle = (acc / total.value) * 360;

        return {
            label: d.label,
            value,
            color: d.color || defaultColor(i),
            path: describeArc(0, 0, radius.value, startAngle, endAngle),
            startAngle,
            endAngle,
        };
    });
});

// simple color palette (Tailwind-like)
const palette = [
    "#6366F1",
    "#10B981",
    "#F59E0B",
    "#EF4444",
    "#636E72",
    "#06B6D4",
    "#A78BFA",
    "#F97316",
];

function defaultColor(i) {
    return palette[i % palette.length];
}

function percent(i) {
    if (!total.value) return "0%";
    const p = (props.data[i].value / total.value) * 100;
    // round to 1 decimal if needed
    return Math.round(p * 10) / 10 + "%";
}

function formatValue(v) {
    // format simple number with commas
    return String(v).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// tooltip style: place at middle angle of hovered slice
const tooltipStyle = computed(() => {
    if (hoverIndex.value === null) return {};
    const s = slices.value[hoverIndex.value];
    const midAngle = (s.startAngle + s.endAngle) / 2;
    // compute position relative to chart container center
    const r = radius.value * 0.65;
    const rad = ((midAngle - 90) * Math.PI) / 180;
    const x = Math.cos(rad) * r + half.value;
    const y = Math.sin(rad) * r + half.value;
    return {
        left: `${x}px`,
        top: `${y}px`,
    };
});
</script>

<style scoped>
/* optional: make tooltip not capture pointer to underlying svg events */
</style>
