<template>
    <div>
        <Line :data="chartData" :options="chartOptions" />
    </div>
</template>

<script setup>
import { computed } from "vue";
import {
    Chart as ChartJS,
    LineElement,
    PointElement,
    LinearScale,
    CategoryScale,
    Tooltip,
    Filler,
    Title,
} from "chart.js";
import { Line } from "vue-chartjs";

ChartJS.register(
    LineElement,
    PointElement,
    LinearScale,
    CategoryScale,
    Tooltip,
    Filler,
    Title,
);

const props = defineProps({
    labels: Array,
    values: Array,
    title: { type: String, default: "" },
});

const chartData = computed(() => ({
    labels: props.labels,
    datasets: [
        {
            label: props.title,
            data: props.values,
            fill: false,
            tension: 0.4, // ini bikin garis melengkung
            borderColor: "#4B5563",
            borderWidth: 2,
            pointRadius: 6,
            pointBackgroundColor: "#6366F1",
            pointBorderColor: "#fff",
            pointBorderWidth: 2,
        },
    ],
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,

    plugins: {
        legend: { display: false },

        title: {
            display: true,
            text: props.title,
            font: {
                size: 16,
                weight: "bold",
            },
            color: "#1F2937", // abu gelap
            padding: { bottom: 20 },
        },
    },

    scales: {
        x: { grid: { display: false } },
        y: { beginAtZero: true, grid: { color: "#E5E7EB" } },
    },
};
</script>
