<script setup>
import { onMounted, onUnmounted, ref, watch } from "vue";
import d3linechart from "./d3.linechart";
const chart = ref();
const d3 = ref();

const props = defineProps({
    config: {
        type: Object,
        required: true,
        default: () => ({}),
    },
    datum: {
        type: Array,
        required: true,
        default: () => [],
    },
    title: {
        type: String,
        default: "",
    },
    source: {
        type: String,
        default: "",
    },
    height: {
        type: [Number, String],
        default: 300,
    },
});
onMounted(() => {
    chart.value = new d3linechart(
        d3.value,
        JSON.parse(JSON.stringify(props.datum)),
        props.config
    );
});
watch(
    () => props.config,
    (val) => chart.value.updateConfig(val),
    { deep: true }
);
watch(
    () => props.datum,
    (vals) => chart.value.updateData([...vals])
);
onUnmounted(() => {
    chart.value.destroyChart();
});
</script>
<template>
    <div class="chart__wrapper relative">
        <div v-if="title" class="chart__title">{{ title }}</div>
        <div ref="d3" :style="{ height: `${height}px` }"></div>
        <div v-if="source" class="chart__source">{{ source }}</div>
    </div>
</template>

<style lang="scss">
.chart {
    /* Wrapper div (chart, title and source) */
    &__wrapper {
        margin: 20px 0;
    }

    /* Wrap div (chart only) */
    &__wrap {
        margin: 0;
    }

    /* Title */
    &__title {
        text-align: center;
        font-weight: 700;
    }

    /* Source */
    &__source {
        font-size: 12px;
    }

    /* Tooltip */
    &__tooltip {
        position: fixed;
        pointer-events: none;
        opacity: 0;
        &.active {
            opacity: 100;
        }

        > div {
            background: #2b2b2b;
            color: white;
            padding: 6px 10px;
            border-radius: 3px;
        }
    }

    /* Axis */
    &__axis {
        font-size: 12px;
        shape-rendering: crispEdges;
        color: "#2b2b2b";
    }

    /* Grid */
    &__grid {
        .domain {
            stroke: none;
            fill: none;
        }

        .tick line {
            opacity: 0.2;
        }
    }

    /* Elements labels */
    &__label {
        font-size: 12px;
    }

    /* Clickable element */
    .clickable {
        cursor: pointer;
    }
}
.dark .chart {
    /* Wrapper div (chart, title and source) */

    /* Title */
    &__title {
        color: white;
    }
    /* Axis */
    &__axis {
        color: #94a3b8;
    }
}
</style>
