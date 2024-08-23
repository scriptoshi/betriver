<script setup>
	import { useForm } from "@inertiajs/vue3";
	import { MdDownloadforoffline, PrCloudDownload } from "oh-vue-icons/icons";

	import Loading from "@/Components/Loading.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	const props = defineProps({
		sport: String,
		league: Number,
		hasOdds: Boolean,
	});
	const oddForm = useForm({
		sport: props.sport.toLowerCase(),
	});

	const loadData = () => {
		oddForm.put(
			window.route("admin.leagues.load.odds", { league: props.league }),
			{
				preserveScroll: true,
				preserveState: true,
			},
		);
	};
</script>
<template>
	<div>
		<button
			@click.prevent="loadData"
			:class="
				!hasOdds
					? ' text-sky-500 hover:text-sky-700 dark:hover:text-sky-300'
					: ' text-green-500 hover:text-green-700 dark:hover:text-green-300'
			"
			class="disabled:pointer-events-none ml-2 disabled:text-gray-400"
			:disabled="oddForm.processing"
			v-tippy="
				hasOdds
					? $t('Load odds from API')
					: $t('Attempt to load API Odds')
			">
			<Loading class="w-5 h-5" v-if="oddForm.processing" />
			<VueIcon
				class="w-auto h-5"
				v-else-if="hasOdds"
				:icon="MdDownloadforoffline" />
			<VueIcon class="w-auto h-5" v-else :icon="PrCloudDownload" />
		</button>
	</div>
</template>
