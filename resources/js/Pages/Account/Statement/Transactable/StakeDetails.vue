<script setup>
	import StakeSidebarCard from "@/Components/Cards/StakeSidebarCard.vue";

	defineProps({
		transaction: {
			type: Object,
			required: true,
		},
	});
</script>
<template>
	<StakeSidebarCard
		class="!p-0 !border-none"
		:stake="transaction.transactable" />
</template>
