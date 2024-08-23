<script setup>
	import { computed, ref } from "vue";

	import { useForm, usePage } from "@inertiajs/vue3";
	import { HiSolidChevronDown } from "oh-vue-icons/icons";
	import { useI18n } from "vue-i18n";

	import CollapseTransition from "@/Components/CollapseTransition.vue";
	import Loading from "@/Components/Loading.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import Cn from "@/flags/cn.svg";
	import De from "@/flags/de.svg";
	import Us from "@/flags/us.svg";

	defineProps({
		expanded: Boolean,
	});
	const langs = [
		{
			name: "English",
			id: "en",
			icon: Us,
		},
		{
			name: "Deutsche",
			id: "de",
			icon: De,
		},
		{
			name: "Chinese",
			id: "zh",
			icon: Cn,
		},
	];
	const ulang = computed(() => usePage().props.auth.user.lang ?? "en");
	const { locale } = useI18n({ useScope: "global" });
	const isOpen = ref(false);
	const langForm = useForm({ lang: null });
	const changeLang = (lang) => {
		langForm
			.transform((d) => ({ lang }))
			.post(window.route("favourites.lang"), {
				onFinish: () => {
					locale.value = lang;
					isOpen.value = false;
				},
			});
	};
	const select = (newLang) => {
		console.log("hehehehe", ulang.value, newLang);
		if (ulang.value === newLang) {
			isOpen.value = !isOpen.value;
			console.log(isOpen.value);
			return;
		}
		langForm.lang = newLang;
		changeLang(newLang);
	};
</script>
<template>
	<div
		class="py-2 grid transition-colors duration-500"
		:class="{ ' bg-gray-200 dark:bg-gray-700': isOpen }">
		<template v-for="lang in langs" :key="lang.id">
			<CollapseTransition>
				<a
					class="py-2.5 px-6 w-auto text-gray-700 dark:text-gray-100 flex items-center hover:bg-gray-100 dark:hover:bg-gray-650"
					href="#"
					:class="{ 'order-first': ulang === lang.id }"
					@click.stop="select(lang.id)"
					v-show="ulang === lang.id || isOpen">
					<Loading
						class="mr-2 !w-4 !h-4"
						v-if="
							langForm.processing && langForm.lang == lang.id
						" />
					<img
						v-else
						:src="lang.icon"
						class="rounded-full mr-2 w-4 h-4" />
					<span v-if="expanded">{{ lang.name }}</span>
					<VueIcon
						v-if="ulang === lang.id"
						class="w-4 h-4 ml-auto transition-transform duration-300"
						:class="{ 'rotate-180': isOpen }"
						:icon="HiSolidChevronDown" />
				</a>
			</CollapseTransition>
		</template>
	</div>
</template>
