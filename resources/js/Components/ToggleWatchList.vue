<template>
	<button
		@click="toggleWatchlist"
		type="button"
		class="focus:outline-none border-l ml-0.5 border-gray-150 dark:border-gray-750 flex items-center justify-center leading-[1.15] relative z-[1] flex-[0_0_2rem] order-5 overflow-visible [appearance:button] min-h-[70px] text-center bg-transparent [outline:none] cursor-pointer box-border shrink-0 flex-wrap m-0 p-0 rounded-[1px] right-0 top-0"
		:title="isWatched ? 'Remove from watchlist' : 'Add to watchlist'"
		:disabled="wForm.processing">
		<!-- Loading Spinner -->
		<Loading v-if="wForm.processing" class="!w-4 !h-4 !text-gray-400" />
		<!-- Star Icons -->
		<template v-else>
			<VueIcon
				:icon="HiSolidStar"
				v-if="isWatched"
				class="w-4 h-4 text-yellow-500"
				solid />
			<VueIcon
				:icon="HiStar"
				v-else
				class="w-4 h-4 text-gray-400 hover:text-yellow-500" />
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
