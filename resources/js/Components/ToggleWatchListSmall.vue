<template>
	<button
		@click.stop="toggleWatchlist"
		type="button"
		class="items-center group z-10 block transition-colors duration-[0.4s,opacity] delay-200 absolute overflow-visible text-center bg-transparent [outline:none] cursor-pointer p-0.5 rounded-[1px] border-[none] right-4 top-2"
		:title="isWatched ? 'Remove from watchlist' : 'Add to watchlist'"
		:disabled="wForm.processing">
		<!-- Loading Spinner -->
		<Loading v-if="wForm.processing" class="!w-4 !h-4 !text-gray-400" />
		<!-- Star Icons -->
		<template v-else>
			<VueIcon
				:icon="HiSolidStar"
				v-if="isWatched"
				class="w-4 h-4 text-yellow-500 group-hover:text-yellow-600"
				solid />
			<VueIcon
				:icon="HiStar"
				v-else
				class="w-4 h-4 text-gray-300 dark:text-gray-700 group-hover:text-emerald-500 dark:group-hover:text-emerald-400" />
		</template>
	</button>
</template>

<script setup>
	import { useForm } from "@inertiajs/vue3";
	import { HiSolidStar, HiStar } from "oh-vue-icons/icons";

	import Loading from "./Loading.vue";
	import VueIcon from "./VueIcon.vue";

	const props = defineProps({
		gameId: {
			type: Number,
			required: true,
		},
		isWatched: {
			type: Boolean,
			required: true,
		},
	});
	const wForm = useForm({});
	const toggleWatchlist = async () => {
		if (props.isWatched) {
			wForm.delete(window.route("sports.watchlist.remove", props.gameId));
		} else {
			wForm.post(window.route("sports.watchlist.add", props.gameId));
		}
	};
</script>
