<script setup>
import D3LineChart from "@/Pages/Admin/Dashboard/AccountsCharts/D3LineChart.vue";
import { computed } from "vue";
const config = {
    date: { key: "date", inputFormat: "%Y-%m-%d" },
    values: ["profit", "loss", "arbitrage", "fees"],
    tooltip: {
        labels: [
            "Bookie Profits",
            "Bookie Loss",
            "Exchange Arbitrage",
            "Exchange Fees",
        ],
    },
    color: {
        key: false,
        keys: {
            profit: "#059669",
            loss: "#ef4444",
            arbitrage: "#3b82f6",
            fees: "#4f46e5",
        },
        scheme: false,
        current: "#1f77b4",
        default: "#AAA",
        axis: "#000",
    },
};

const generateRandomData = (count) => {
    const types = ["profit", "loss", "arbitrage", "fees"];
    const data = [];
    const startDate = new Date("2023-01-01");

    for (let i = 0; i < count; i++) {
        const date = new Date(startDate.getTime() + i * 24 * 60 * 60 * 1000);
        const dataPoint = {
            date: date.toISOString().split("T")[0],
        };

        types.forEach((type) => {
            dataPoint[type] = parseFloat((Math.random() * 100).toFixed(2));
        });

        data.push(dataPoint);
    }

    return data;
};
const data = computed(() => generateRandomData(10));
</script>
<template>
    <div
        class="w-full shadow-sm rounded-sm p-4 bg-white dark:bg-gray-800 h-[400px]"
    >
        <h3>Admin Earnings</h3>
        <D3LineChart :config="config" :datum="data" />
    </div>
</template>
