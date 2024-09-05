<script setup>
	import { computed, onMounted, onUnmounted, ref } from "vue";

	const props = defineProps({
		endTime: {
			type: Number,
			required: true,
		},
	});

	const remainingTime = ref(0);
	const intervalId = ref(null);

	const updateRemainingTime = () => {
		const now = Date.now();
		remainingTime.value = Math.max(0, props.endTime - now);

		if (remainingTime.value === 0) {
			clearInterval(intervalId.value);
		}
	};

	onMounted(() => {
		updateRemainingTime();
		intervalId.value = setInterval(updateRemainingTime, 1000);
	});

	onUnmounted(() => {
		if (intervalId.value) {
			clearInterval(intervalId.value);
		}
	});

	const formattedTime = computed(() => {
		const minutes = Math.floor(remainingTime.value / 60000);
		const seconds = Math.floor((remainingTime.value % 60000) / 1000);
		return `${minutes.toString().padStart(2, "0")}:${seconds
			.toString()
			.padStart(2, "0")}`;
	});

	const isLessThanOneMinute = computed(() => {
		return remainingTime.value < 60000; // less than 1 minute
	});
</script>
<template>
	<div
		class="text-2xl font-bold"
		:class="{ 'text-red-600': isLessThanOneMinute }">
		{{ formattedTime }}
	</div>
</template>
