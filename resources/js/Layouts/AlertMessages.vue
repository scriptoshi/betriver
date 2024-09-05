<script setup>
	import { computed, ref, watch } from "vue";

	import { XMarkIcon as XIcon } from "@heroicons/vue/24/outline";
	import { usePage } from "@inertiajs/vue3";
	import { HiSolidCheck } from "oh-vue-icons/icons";

	import VueIcon from "@/Components/VueIcon.vue";
	const show = ref(true);
	const error = computed(() => usePage().props.flash?.error);
	const success = computed(() => usePage().props.flash?.success);
	const message = computed(() => usePage().props.flash?.message);
	const info = computed(() => usePage().props.flash?.info);
	setTimeout(() => {
		show.value = false;
	}, 100000000000);
	watch(
		[error, success, message, info],
		([error, success, message, info]) => {
			if (error || success || message || info) {
				setTimeout(() => {
					show.value = false;
				}, 100000000);
				show.value = true;
			}
		},
	);
</script>
<template>
	<section
		@click="show = false"
		v-if="message && show"
		class="bg-gray-500 text-white text-base py-2 px-4 flex justify-center text-center cursor-pointer sticky top-0 z-[100]">
		<a
			class="md:flex md:items-center font-semibold text-white"
			href="#"
			@click="message = null">
			<VueIcon
				:icon="HiSolidCheck"
				class="shrink-0 w-4 h-4 inline mr-2" />
			{{ message }} →
		</a>
	</section>
	<section
		@click="show = false"
		v-if="success && show"
		class="bg-green-500 text-white text-base py-2 px-4 flex justify-center text-center cursor-pointer sticky top-0 z-[100]">
		<a
			class="md:flex md:items-center font-semibold text-white"
			href="#"
			@click="success = null">
			<HiSolidCheck class="shrink-0 w-4 h-4 inline mr-2" />
			{{ success }} →
		</a>
	</section>
	<section
		@click="show = false"
		v-if="error && show"
		class="bg-red-500 text-white text-base py-2 px-4 flex justify-center text-center cursor-pointer sticky top-0 z-[100]">
		<a
			class="md:flex md:items-center font-semibold text-white"
			href="#"
			@click="error = null">
			<XIcon class="shrink-0 w-4 h-4 inline mr-2" />
			{{ error }} →
		</a>
	</section>
	<section
		@click="show = false"
		v-if="info && show"
		class="bg-sky-500 text-white text-base py-2 px-4 flex justify-center text-center cursor-pointer sticky top-0 z-[100]">
		<a
			class="md:flex md:items-center font-semibold text-white"
			href="#"
			@click="info = null">
			<HiSolidCheck class="shrink-0 w-4 h-4 inline mr-5" />
			{{ info }} →
		</a>
	</section>
</template>
