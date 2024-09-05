<script setup>
	import LimitDeposits from "@/Pages/Account/Limits/LimitDeposits.vue";
	import LimitStake from "@/Pages/Account/Limits/LimitStake.vue";
	import PotentialLoss from "@/Pages/Account/Limits/PotentialLoss.vue";
	import TimeOut from "@/Pages/Account/Limits/TimeOut.vue";
	import SettingsLayout from "@/Pages/Account/Settings/SettingsLayout.vue";

	defineProps({
		personal: Object,
		lossOptions: Object,
		mailOptions: Object,
	});
</script>

<template>
	<SettingsLayout>
		<div class="py-2 grid gap-4">
			<LimitDeposits :personal="personal" />
			<PotentialLoss
				:lossOptions="Object.values(lossOptions)"
				:personal="personal" />
			<LimitStake :personal="personal" />
			<TimeOut :personal="personal" />
		</div>
	</SettingsLayout>
</template>
