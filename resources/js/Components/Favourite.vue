<script setup>
	import { computed } from "vue";

	import { useForm, usePage } from "@inertiajs/vue3";
	import { HiSolidStar, HiStar } from "oh-vue-icons/icons";

	import Loading from "./Loading.vue";
	import VueIcon from "./VueIcon.vue";

	const props = defineProps({
		favId: String,
	});
	const form = useForm({ key: props.favId });
	const isFav = computed(() => {
		const quicklinks = usePage().props.quicklinks ?? [];
		return quicklinks.includes(props.favId);
	});
	const favourite = () => {
		form.post(window.route("favourites.store"), {
			preserveScroll: true,
			preserveState: true,
		});
	};
	const unfavourite = () => {
		form.delete(
			window.route("favourites.destroy", { favourite: props.favId }),
			{
				preserveScroll: true,
				preserveState: true,
			},
		);
	};
</script>
<template>
	<Loading class="!w-4 !h-4 mr-2" v-if="form.processing" />
	<a
		v-else-if="isFav"
		href="#"
		@click.stop="unfavourite"
		class="p-1 flex items-center">
		<VueIcon
			:icon="HiSolidStar"
			class="w-4 h-4 mr-2 text-amber-500 transition-transform duration-300" />
	</a>
	<a v-else href="#" @click.stop="favourite" class="p-1 flex items-center">
		<VueIcon
			:icon="HiStar"
			class="w-4 h-4 mr-2 opacity-20 group-hover:opacity-90 transition duration-300" />
	</a>
</template>
