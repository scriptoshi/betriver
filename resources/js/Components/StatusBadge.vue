<script setup>
	import { AlertTriangle, Check, Loader } from "lucide-vue-next";

	import { Badge } from "@/Components/ui/badge";

	defineProps({
		status: {
			type: String,
			required: true,
		},
	});
</script>
<template>
	<Badge
		variant="outline"
		class="uppercase text-xs font-bold font-inter"
		:class="{
			'border-gray-400 dark:border-gray-650 text-gray-700 dark:text-gray-200':
				['review', 'pending'].includes(status),
			'border-sky-400 dark:border-sky-650 text-sky-600 dark:text-sky-400':
				['processing'].includes(status),
			'dark:border-green-400 border-green-600 text-green-600 dark:text-green-400':
				['approved', 'complete', 'confirmed'].includes(status),
			'dark:border-red-400 border-red-600 text-red-600 dark:text-red-400':
				['rejected', 'failed', 'reversed', 'cancelled'].includes(
					status,
				),
		}">
		<Loader
			v-if="['review', 'pending', 'processing'].includes(status)"
			class="w-3 h-3 mr-1 -ml-0.5" />
		<Check
			v-else-if="['approved', 'complete', 'confirmed'].includes(status)"
			class="w-3 h-3 mr-1 -ml-0.5" />
		<AlertTriangle
			v-else-if="
				['rejected', 'failed', 'reversed', 'cancelled'].includes(status)
			"
			class="w-3 h-3 mr-1 -ml-0.5" />
		{{ status }}
	</Badge>
</template>
