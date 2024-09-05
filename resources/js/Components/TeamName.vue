<script setup>
	import { computed } from "vue";

	import { ucfirst } from "@/hooks";

	const props = defineProps({
		name: String,
		game: Object,
	});
	const mname = computed(() => {
		return props.name
			.replace("{{", "{")
			.replace("}}", "}")
			.replace("{home}", props.game.homeTeam.name)
			.replace("{away}", props.game.awayTeam.name);
	});
</script>
<template>
	<slot :name="mname">{{ ucfirst(mname) }}</slot>
</template>
