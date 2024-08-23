<template>
	<div class="odds-graph">
		<svg ref="svgRef" :width="width" :height="height"></svg>
	</div>
</template>

<script setup>
	import { onMounted, ref, watch } from "vue";

	import * as d3 from "d3";

	const props = defineProps({
		data: {
			type: Array,
			required: true,
		},
		width: {
			type: Number,
			default: 632,
		},
		height: {
			type: Number,
			default: 200,
		},
	});

	const svgRef = ref(null);

	const drawGraph = () => {
		if (!props.data || props.data.length === 0) return;

		const svg = d3.select(svgRef.value);
		svg.selectAll("*").remove(); // Clear previous content

		const margin = { top: 20, right: 30, bottom: 30, left: 60 };
		const innerWidth = props.width - margin.left - margin.right;
		const innerHeight = props.height - margin.top - margin.bottom;

		const x = d3
			.scaleTime()
			.domain(d3.extent(props.data[0].values, (d) => d.date))
			.range([0, innerWidth]);

		const y = d3.scaleLinear().domain([0, 100]).range([innerHeight, 0]);

		const yAxis = d3.axisLeft(y).tickFormat((d) => `${d}%`);

		const xAxis = d3.axisBottom(x).tickFormat(d3.timeFormat("%H:%M"));

		const line = d3
			.line()
			.x((d) => x(d.date))
			.y((d) => y(d.probability));

		const g = svg
			.append("g")
			.attr("transform", `translate(${margin.left},${margin.top})`);

		// Add the X gridlines
		g.append("g")
			.attr("class", "grid")
			.attr("transform", `translate(0,${innerHeight})`)
			.call(d3.axisBottom(x).tickSize(-innerHeight).tickFormat(""));

		// Add the Y gridlines
		g.append("g")
			.attr("class", "grid")
			.call(d3.axisLeft(y).tickSize(-innerWidth).tickFormat(""));

		// Add the X Axis
		g.append("g")
			.attr("transform", `translate(0,${innerHeight})`)
			.call(xAxis);

		// Add the Y Axis
		g.append("g").call(yAxis);

		// Draw lines
		props.data.forEach((series) => {
			g.append("path")
				.datum(series.values)
				.attr("fill", "none")
				.attr("stroke", series.color)
				.attr("stroke-width", 2)
				.attr("d", line);
		});

		// Draw volume bars
		const volumeData = props.data[0].values.map((d) => ({
			date: d.date,
			volume: Math.random() * 100, // Random volume for demonstration
		}));

		const volumeScale = d3
			.scaleLinear()
			.domain([0, d3.max(volumeData, (d) => d.volume)])
			.range([0, innerHeight / 3]);

		g.selectAll(".volume-bar")
			.data(volumeData)
			.enter()
			.append("rect")
			.attr("class", "volume-bar")
			.attr("x", (d) => x(d.date) - 1)
			.attr("y", (d) => innerHeight - volumeScale(d.volume))
			.attr("width", 2)
			.attr("height", (d) => volumeScale(d.volume))
			.attr("fill", "rgba(100, 100, 100, 0.5)");
	};

	onMounted(drawGraph);

	watch(() => props.data, drawGraph, { deep: true });
	watch([() => props.width, () => props.height], drawGraph);
</script>

<style scoped>
	.odds-graph {
		background-color: #1e1e1e;
	}
	:deep(.grid line) {
		stroke: #2d2d2d;
	}
	:deep(.grid path) {
		stroke-width: 0;
	}
	:deep(.domain) {
		stroke: #4d4d4d;
	}
	:deep(.tick text) {
		fill: #8e8e8e;
	}
</style>
